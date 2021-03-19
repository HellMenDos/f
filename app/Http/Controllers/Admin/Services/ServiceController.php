<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\Service;
use App\Models\ServiceExecutor;
use App\Repositories\Admin\ServiceRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\ResponseLib;
use App\Http\Controllers\Admin\BaseController;
use Illuminate\Support\Facades\DB;


class ServiceController extends BaseController
{
    protected $serviceRepo;

    public function __construct(ServiceRepository $serviceRepo)
    {
        parent::__construct();

        $this->serviceRepo = $serviceRepo;

        view()->share(['heading' => 'Услуги', 'title' => 'Список услуг']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectGroup = $request->group;

        $perPage = 20;
        if (!$selectGroup) {
            $services = $this->serviceRepo->getServicesPagination($perPage);
            $countServices = $this->serviceRepo->getCountServices();
        } else {
            $services = $this->serviceRepo->getGroupServicesPagination($perPage, $selectGroup);
            $countServices = $this->serviceRepo->getCountGroupServices($selectGroup);
        }

        $data['services'] = $services;
        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['countServices'] = $countServices;
        $data['selectGroup'] = $selectGroup;

        return view('admin.services.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $service = new Service();
        $service->created_at = date('Y-m-d H:i:s');

        $data['units'] = DB::table('units')->get();
        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['action'] = route('admin.services.store');
        $data['selectGroup'] = $request->group;
        $data['service'] = $service;
        if ($request->group) {
            $data['executors'] = ServiceExecutor::where(['service_group_id' => $request->group, 'published' => '1'])->get();
        } else {
            $data['executors'] = ServiceExecutor::where('published', '1')->get();
        }

        return view('admin.services.form', $data)->with(['title' => 'Создание услуги']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequest $request
     * @return \Illuminate\Http\Responsew
     */
    public function store(ServiceRequest $request)
    {
        $service = new Service();
        $service->title = $request->title;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->unit_id = $request->unit_id;
        $service->group_id = $request->group_id;
        $service->published = $request->published;
        $service->created_at = $request->created_at;
        $service->executor_id = $request->executor_id;
        $service->save();

        if ($service) {
            $this->setFeed('Добавил услугу &laquo;' . trim($request->title) . '&raquo;');
        }
        return $this->redirectResponse($service, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = $this->serviceRepo->getId($id);

        $this->recordExists($service);

        $data['units'] = DB::table('units')->get();
        $data['service'] = $service;
        $data['servicesGroups'] = $this->serviceRepo->getGroups();
        $data['action'] = route('admin.services.update', $id);
        $data['executors'] = ServiceExecutor::where('service_group_id', $service->group_id)->get();

        return view('admin.services.form', $data)->with(['title' => 'Редактирование услуги']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequest $request, $id)
    {
        $service = $this->serviceRepo->getId($id);

        $this->recordExists($service);

        $updated_at = ($request->updated_at != $service->updated_at)
            ? $request->updated_at
            : date('Y-m-d H:i:s');

        $data = $request->all();
        $data['updated_at'] = $updated_at;
        $result = $service->update($data);

        if ($result) {
            $this->setFeed('Изменил услугу  &laquo;' . trim($request->title) . '&raquo;');
        }

        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.index'));
    }

    /* @param int $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        $service = $this->serviceRepo->getId($id);

        $this->recordExists($service);

        $updateData = ['published' => '1'];

        $result = $service->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Услуга активирована', 'error' => 'Ошибка! Услуга не активирована']);
    }

    /* @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        $service = $this->serviceRepo->getId($id);

        $this->recordExists($service);

        $updateData = ['published' => '0'];

        $result = $service->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Услуга отключена', 'error' => 'Ошибка! Услуга не отключена']);
    }
}
