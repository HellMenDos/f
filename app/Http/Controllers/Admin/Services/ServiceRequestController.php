<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\ServiceRequest;
use App\Repositories\Admin\Users\AreaRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ServiceRequestRequest;
use App\Repositories\Admin\ServiceRequestRepository;
use App\Repositories\Admin\ServiceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\ResponseLib;
use App\Http\Controllers\Admin\BaseController;

class ServiceRequestController extends BaseController
{
    protected $serviceRequestRepo;
    protected $serviceRepo;

    public function __construct(ServiceRequestRepository $serviceRequestRepo,
                                ServiceRepository $serviceRepo,
                                AreaRepository $areaRepo)
    {
        parent::__construct();

        $this->serviceRequestRepo = $serviceRequestRepo;
        $this->serviceRepo = $serviceRepo;
        $this->areaRepo = $areaRepo;

        view()->share(['heading' => 'Заявки', 'title' => 'Заявки']);
    }

    public function index()
    {
        $perPage = 20;
        $serviceRequests = $this->serviceRequestRepo->getServiceRequestsPagination($perPage);
        $countServiceRequests = $this->serviceRequestRepo->getCountServiceRequests();

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
                case 4:
                    $service->status_label = 'Отменена';
                    $service->status_title = 'Заявка отменена';
                    $service->status_color = 'secondary';
                    break;
                default:
                    $service->status_label = 'Новый';
                    $service->status_title = 'Заявка еще не рассмотрена';
                    $service->status_color = 'info';
                    break;
            }

            $service->rating = adminShowRate($service->rating);

//            $day = date("d-m-Y");
//            if($service->execution_date==$day){
//                $serviceRequest = $this->serviceRequestRepo->getId($service->id);
//
//                $this->recordExists($serviceRequest);
//
//                $serviceRequest->status = 2;
//                $serviceRequest->update();
//            }
        }

        $data['serviceRequests'] = $serviceRequests;
        $data['countServiceRequests'] = $countServiceRequests;

        return view('admin.services.request.index', $data);
    }

    public function show($id)
    {
        $serviceRequest = $this->serviceRequestRepo->getServiceRequestById($id);

        $this->recordExists($serviceRequest);

        $planningServiceRequest = unserialize($serviceRequest->planning);
        $service = $this->serviceRepo->getId($planningServiceRequest['service_id']);
        if ($planningServiceRequest['service_id']) {
            $planningServiceRequest['price'] = $service->price;
        }

        $date = Carbon::parse($serviceRequest->execution_date);
        $serviceRequest->execution_date = $date->format('d-m-Y');

        $date = Carbon::parse($planningServiceRequest['execution_date']);
        $planningServiceRequest['execution_date'] = $date->format('d-m-Y');

        $data['planningServiceRequest'] = $planningServiceRequest;
        $data['serviceRequest'] = $serviceRequest;

        return view('admin.services.request.details', $data)->with(['heading' => $serviceRequest->title, 'title' => 'Просмотр заявки']);
    }

    public function create()
    {
        $serviceRequest = new ServiceRequest();
        $serviceRequest->created_at = date('Y-m-d H:i:s');
        $serviceRequest->execution_date = date('Y-m-d H:i:s');
        $services = $this->serviceRepo->getAllActiveServices();

        $data['addresses'] = $this->areaRepo->getAreas()->toArray();
        $data['action'] = route('admin.services.request.store');
        $data['serviceRequest'] = $serviceRequest;
        $data['services'] = $services;

        return view('admin.services.request.form', $data)->with(['title' => 'Создание заявки', 'heading' => 'Создание заявки']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequestRequest $request)
    {
        $serviceRequest = new ServiceRequest();
        $serviceRequest->title = $request->title;
        $serviceRequest->description = $request->description;
        $serviceRequest->quantity = $request->quantity;
        $serviceRequest->area_id = $request->area_id;
        $serviceRequest->execution_date = $request->execution_date;
        $serviceRequest->price = $request->price;
        $serviceRequest->save();

        if ($serviceRequest) {
            $this->setFeed('Добавил  заявку &laquo;' . trim($request->title) . '&raquo;');
        }

        return $this->redirectResponse($serviceRequest, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.request.index'));
    }

    public function edit($id)
    {
        $serviceRequest = $this->serviceRequestRepo->getServiceRequestById($id);

        $this->recordExists($serviceRequest);

        $services = $this->serviceRepo->getAllActiveServices();

        $data['addresses'] = $this->areaRepo->getAreas()->toArray();
        $data['action'] = route('admin.services.request.update', $id);
        $data['serviceRequest'] = $serviceRequest;
        $data['services'] = $services;

        return view('admin.services.request.form', $data)->with(['title' => 'Редактирование заявки']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequestRequest $request, $id)
    {
        $serviceRequest = $this->serviceRequestRepo->getId($id);

        $this->recordExists($serviceRequest);

        $data = $request->all();
        $result = $serviceRequest->update($data);

        // Меняем статус
        $result = $serviceRequest->update(['status' => '1']);

        if ($result) {
            $this->setFeed('Изменил заявку  &laquo;' . trim($request->title) . '&raquo;');
        }

        return $this->redirectResponse($serviceRequest, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.request.show', $id));
    }

    /**
     * Accept the service Request
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function accept($id)
    {
        $serviceRequest = $this->serviceRequestRepo->getId($id);

        $this->recordExists($serviceRequest);

        $updateData = ['status' => '1'];

        $result = $serviceRequest->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Заявка принята!', 'error' => 'Ошибка! Заявка не принята']);
    }

    /**
     * Complete the service Request
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function complete($id)
    {
        $serviceRequest = $this->serviceRequestRepo->getId($id);

        $this->recordExists($serviceRequest);

        $updateData = ['status' => '3'];

        $result = $serviceRequest->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Заявка выполнена!', 'error' => 'Ошибка! Заявка не выполнена']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $serviceRequest = $this->serviceRequestRepo->getId($id);

        $this->recordExists($serviceRequest);

        $result = $serviceRequest::destroy($id);

        if ($result) {
            $this->setFeed('Удалил заявку &laquo;'. trim($serviceRequest->title, '&raquo; &laquo;') .'&raquo;');
        }
        return $this->redirectResponse($result, ['success' => 'Заявка удалена', 'error' => 'Ошибка удаления']);
    }
}
