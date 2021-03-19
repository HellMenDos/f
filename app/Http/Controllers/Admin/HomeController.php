<?php

namespace App\Http\Controllers\Admin;

use App\Models\Registry;
use App\Repositories\Admin\Articles\ArticleRepository;
use App\Repositories\Admin\MainRepository;
use App\Repositories\Admin\MetersDataRepository;
use App\Repositories\Admin\Statistic\StatisticRepository;
use App\Repositories\Admin\TodoRepository;
use App\Repositories\Admin\Users\AreaRepository;
use App\Models\AdminTodo;
use App\Models\MetersData;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends BaseController
{
    private $articleRepository;
    private $todoRepository;
    private $statisticRepository;
    private $metersDataRepository;
    private $areaRepo;
    private $message;
    private $messageType;

    public function __construct()
    {
        parent::__construct();

        $this->articleRepository = app(ArticleRepository::class);
        $this->statisticRepository = app(StatisticRepository::class);
        $this->todoRepository = app(TodoRepository::class);
        $this->metersDataRepository = app(MetersDataRepository::class);
        $this->areaRepo = app(AreaRepository::class);

        view()->share(['title' => 'Добро пожаловать', 'message' => ['title' => $this->message, 'type' => $this->messageType]]);
    }

    public function index()
    {
        $this->addStorageSymlinkAlert();

        $countAreas = MainRepository::getCountAreas();
        $countUsers = MainRepository::getCountUsers();
        $countCategories = MainRepository::getCountCategories();
        $countArticles = MainRepository::getCountArticles();
        $countFeeds = MainRepository::getCountFeeds();
        $lastFeeds = MainRepository::getFeeds();

        $perPage = 5;
        $lastArticles = $this->articleRepository->getAllArticles($perPage);

        // areas
        $areas = $this->areaRepo->getAllAreasPagination($perPage);

        $areasNotData = [];
        foreach ($areas as $area) {
            $currentMetersExists = MetersData::where([
                    ['area_id', '=', $area->id],
                    ['created_at', '>', $this->metersDataPeriodStart],
                    ['created_at', '<', $this->metersDataPeriodEnd],
                ])->get();

            $area->status = '';

            if ($currentMetersExists->count()) {
                if ($currentMetersExists[0]->water == null || $currentMetersExists[0]->water == '0.00') {
                    $area->status = 'no-data-water';
                    $areasNotData[] = $area;
                } elseif ($currentMetersExists[0]->electricity == null || $currentMetersExists[0]->electricity == '0.00') {
                    $area->status = 'no-data-electricity';
                    $areasNotData[] = $area;
                } elseif (is_null($currentMetersExists[0]->paid_at)) {
                    $area->status = 'no-paid';
                } elseif ( ! is_null($currentMetersExists[0]->paid_at)) {
                    $area->status = 'is-paid';
                }
            } else {
                $area->status = 'no-data';
                $areasNotData[] = $area;
            }
        }

        // service_requests
        $serviceRequests = ServiceRequest::latest()->limit(5)->get();

        foreach ($serviceRequests as $service) {
            $date = Carbon::parse($service->created_at);
            $service->created = $date->format('d-m-Y');

            switch ($service->status) {
                case 0:
                    $service->status_label = 'Новый';
                    $service->status_title = 'Заявка еще не рассмотрена';
                    $service->status_color = 'info';
                    break;
                case 1:
                    $service->status_label = 'Принято';
                    $service->status_title = 'Заявка находится в стадии выполнения';
                    $service->status_color = 'warning';
                    break;
                case 2:
                    $service->status_label = 'Передана';
                    $service->status_title = 'Заявка передана в подрядную организацию';
                    $service->status_color = 'warning';
                    break;
                case 3:
                    $service->status_label = 'Выполнена';
                    $service->status_title = 'Заявка выполнена';
                    $service->status_color = 'success';
                    break;
                default:
                    $service->status_label = 'Новый';
                    $service->status_title = 'Заявка еще не рассмотрена';
                    $service->status_color = 'info';
                    break;
            }
        }

        // visits
        $visits = [];
        $uniqueByDay = $this->statisticRepository->getUniqueVisitsByDay(date('Y-m-d', strtotime('-14 days')), date('Y-m-d'));
        $visitsByDay = $this->statisticRepository->getVisitsByDay(date('Y-m-d', strtotime('-14 days')), date('Y-m-d'));
        $todayVisits = $this->statisticRepository->getVisitsByDay(date('Y-m-d'), date('Y-m-d'))->toArray();

        $countTodayVisits = ($todayVisits) ? $todayVisits[0]['count'] : 0;

        foreach ($visitsByDay as $k => $v) {
            $visits[$k]['date'] = $v['date'];
            if (isset($visitsByDay[$k])) {
                $visits[$k]['unique'] = $visitsByDay[$k]['count'];
            } else {
                $visits[$k]['unique'] = 0;
            }
            $visits[$k]['visits'] = $v['count'];
        }


        // Todolist
        $data['todolist'] = $this->todoRepository->getTodoes();
        $data['todolistAutoIncrement'] = get_autoincrement('admin_todo');

        $data['meters_data'] = $this->metersDataRepository->getMetersData();
        $tariffData = Registry::where('group', 'tariff_data')->latest()->first();
        $data['tariff'] = json_decode($tariffData->value);
        foreach($data['meters_data'] as $md){
            $data['area'] = $this->metersDataRepository->getAreaMetersData($md->area_id);
        }
        // Блокнот
        $notes = config('settings.admin_notes');

        $data['countTodayVisits'] = $countTodayVisits;
        $data['countUsers'] = $countUsers;
        $data['countCategories'] = $countCategories;
        $data['lastArticles'] = $lastArticles;
        $data['countAreas'] = $countAreas;
        $data['areas'] = $areasNotData;
        $data['countArticles'] = $countArticles;
        $data['countFeeds'] = $countFeeds;
        $data['feeds'] = $lastFeeds;
        $data['visits'] = $visits;
        $data['notes'] = $notes;
        $data['serviceRequests'] = $serviceRequests;


        return view('admin.home.index', $data);
    }

    public function save(Request $request)
    {
        $request->validate([
           'insertDataDay'   => 'required|numeric',
           'insertDataNight' => 'required|numeric',
        ]);

        $meters_data = new MetersData([
            'area_id' => $request->get('area_id'),
            'water' => 0,
            'last_electricity_day' => $request->get('electricity_day'),
            'last_electricity_night' => $request->get('electricity_night'),
            'electricity_day' => $request->get('insertDataDay'),
            'electricity_night' => $request->get('insertDataNight'),
        ]);
        $meters_data->save();
        if ($meters_data){
            $this->setFeed('Добавил показания счетчика <a href="' . route('admin.dashboard.index', $meters_data->id) . '">&laquo;' . $meters_data->full_name . '&raquo;</a>');
        }
        return $this->redirectResponse($meters_data, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.dashboard.index'));
    }

    public function update($id)
    {
        $meters_data = MetersData::find($id);
        $meters_data->update();
        if ($meters_data){
            $this->setFeed('Изменил показания счетчика <a href="' . route('admin.dashboard.index', $meters_data->id) . '">&laquo;' . $meters_data->full_name . '&raquo;</a>');
        }
        return $this->redirectResponse($meters_data, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.dashboard.index'));
    }

    protected function addStorageSymlinkAlert()
    {
        $storageDisk = (!empty(config('app.storage.disk'))) ? config('app.storage.disk') : 'public';

        if (request()->has('fix-missing-storage-symlink')) {
            if (file_exists(public_path('storage'))) {
                if (@readlink(public_path('storage')) == public_path('storage')) {
                    rename(public_path('storage'), 'storage_old');
                }
            }

            if (!file_exists(public_path('storage'))) {
                $this->fixMissingStorageSymlink();
            }
        } elseif ($storageDisk == 'public') {
            if (!file_exists(public_path('storage')) || @readlink(public_path('storage')) == public_path('storage')) {
                return redirect()->route('admin.dashboard.index')->withFlashDanger('Не найдена ссылка на хранилище данных: это может вызвать проблемы с загрузкой медиафайлов. <a href="?fix-missing-storage-symlink=1" class="btn btn-default">Исправить</a>');
            }
        }
    }

    protected function fixMissingStorageSymlink()
    {
        app('files')->link(storage_path('app/public'), public_path('storage'));

        if (file_exists(public_path('storage'))) {
            return redirect()->route('admin.dashboard.index')->withFlashSuccess('Создана недостающая ссылка на хранилище данных.');
        } else {
            return redirect()->route('admin.dashboard.index')->withFlashDanger('Не удалось создать ссылку для хранилища данных.');
        }
    }

    public function ajaxSaveNotes(Request $request)
    {
        if (request()->ajax()) {

            $notes = $request->notes;

            $resultNotes = \DB::table('settings')
                ->where('key', 'admin_notes')
                ->update(['value' => $notes]);

            if ($resultNotes) {
                $result = 'success';
            } else {
                $result = 'error';
            }

            return response()->json($result);

        } else {
            return response()->json('error', 404);
        }
    }

  // --------------------------------------------------------------------

    public function ajaxTodo(Request $request)
    {
        if (request()->ajax()) {

            if ($request->action == 'add') {

                AdminTodo::insert([
                    'title'  => $request->title,
                    'status' => 0
                ]);

            } elseif ($request->action == 'edit') {

                AdminTodo::where('id', $request->id)
                    ->update([
                        'status' => $request->status
                    ]);

            } elseif ($request->action == 'delete') {

                AdminTodo::where('id', $request->id)
                    ->delete();

            } elseif ($request->action == 'clear') {

                AdminTodo::truncate();

            }

            $result = 'success';

            return response()->json($result);

        } else {
            return response()->json('error', 404);
        }
    }

}
