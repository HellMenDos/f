<?php

namespace App\Http\Controllers\Admin\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseController;
use App\Models\ServiceExecutor;
use App\Repositories\Admin\ServiceExecutorRepository;
use App\Repositories\Admin\ServiceRepository;

class ServiceExecutorController extends BaseController
{
    protected $serviceExecutorRepo;
    protected $serviceRepo;

    public function __construct(ServiceExecutorRepository $serviceExecutorRepo, ServiceRepository $serviceRepo)
    {
        parent::__construct();

        $this->serviceExecutorRepo = $serviceExecutorRepo;
        $this->serviceRepo = $serviceRepo;

        view()->share(['heading' => 'Исполнители услуг', 'title' => 'Список исполнителей услуг']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectGroup = $request->group;

        $perPage = 20;
        if (!$selectGroup) {
            $servicesExecutors = $this->serviceExecutorRepo->getServicesExecutorPagination($perPage);
            $countServicesExecutors = $this->serviceExecutorRepo->getCountServicesExecutor();
        } else {
            $servicesExecutors = $this->serviceExecutorRepo->getGroupServicesExecutorPagination($perPage, $selectGroup);
            $countServicesExecutors = $this->serviceExecutorRepo->getCountGroupServicesExecutor($selectGroup);
        }

        $data['servicesExecutors'] = $servicesExecutors;
        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['countServicesExecutors'] = $countServicesExecutors;
        $data['selectGroup'] = $selectGroup;

        return view('admin.services.executors.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $serviceExecutor = new ServiceExecutor();

        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['action'] = route('admin.services.executors.store');
        $data['selectGroup'] = $request->group;
        $data['serviceExecutor'] = $serviceExecutor;

        return view('admin.services.executors.form', $data)->with(['title' => 'Создание исполнителя']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $serviceExecutor = new ServiceExecutor();
        $serviceExecutor->title = $request->title;
        $serviceExecutor->description = $request->description;
        $serviceExecutor->price = $request->price;
        $serviceExecutor->service_group_id = $request->group_id;
        $serviceExecutor->published = $request->published;
        $serviceExecutor->save();

        if ($serviceExecutor) {
            $this->setFeed('Добавил исполнителя &laquo;' . trim($request->title) . '&raquo;');
        }
        return $this->redirectResponse($serviceExecutor,
            ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'],
            route('admin.services.executors.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $serviceExecutor = $this->serviceExecutorRepo->getId($id);

        $this->recordExists($serviceExecutor);

        $data['serviceExecutor'] = $serviceExecutor;
        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['action'] = route('admin.services.executors.update', $id);
        $data['executors'] = ServiceExecutor::where('service_group_id', $serviceExecutor->service_group_id)->get();

        return view('admin.services.executors.form', $data)->with(['title' => 'Редактирование исполнителя']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $serviceExecutor = $this->serviceExecutorRepo->getId($id);

        $this->recordExists($serviceExecutor);

        $data = $request->all();

        $result = $serviceExecutor->update($data);

        if ($result) {
            $this->setFeed('Изменил данные исполнителя &laquo;' . trim($request->title) . '&raquo;');
        }

        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.executors.index'));

    }

    /* @param int $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        $serviceExecutor = $this->serviceExecutorRepo->getId($id);

        $this->recordExists($serviceExecutor);

        $updateData = ['published' => '1'];

        $result = $serviceExecutor->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Исполнитель активирован', 'error' => 'Ошибка! Исполнитель не активирован']);
    }

    /* @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        $serviceExecutor = $this->serviceExecutorRepo->getId($id);

        $this->recordExists($serviceExecutor);

        $updateData = ['published' => '0'];

        $result = $serviceExecutor->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Исполнитель отключен', 'error' => 'Ошибка! Исполнитель не отключен']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $serviceExecutor = $this->serviceExecutorRepo->getId($id);

        $this->recordExists($serviceExecutor);

        $result = ServiceExecutor::destroy($id);

        if ($result) {
            $this->setFeed('Удалил исполнителя &laquo;'. trim($serviceExecutor->title, '&raquo; &laquo;') .'&raquo;');
        }
        return $this->redirectResponse($result, ['success' => 'Исполнитель удален', 'error' => 'Ошибка удаления']);
    }
}
