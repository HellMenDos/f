<?php

namespace App\Http\Controllers\Admin\Services;

use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ServiceGroupRequest;
use App\Repositories\Admin\ServiceGroupRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\ResponseLib;
use App\Http\Controllers\Admin\BaseController;

class ServiceGroupController extends BaseController
{

    protected $serviceGroupRepo;

    public function __construct(ServiceGroupRepository $serviceGroupRepo)
    {
        parent::__construct();

        $this->serviceGroupRepo = $serviceGroupRepo;

        view()->share(['heading' => 'Группы услуг', 'title' => 'Группы услуг']);
    }

    public function create()
    {
        $serviceGroup = new ServiceGroup();
        $serviceGroup->created_at = date('Y-m-d H:i:s');

        $data['action'] = route('admin.services.group.store');
        $data['serviceGroup'] = $serviceGroup;

        return view('admin.services.group.form', $data)->with(['title' => 'Создание группы услуг']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceGroupRequest $request)
    {

        $serviceGroup = new ServiceGroup();
        $serviceGroup->title = $request->title;
        $serviceGroup->description = $request->description;
        $serviceGroup->save();

        if ($serviceGroup) {
            $this->setFeed('Добавил группу услуг &laquo;' . trim($request->title) . '&raquo;');
        }
        return $this->redirectResponse($serviceGroup, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.index'));
    }

    public function edit($id)
    {
        $serviceGroup = $this->serviceGroupRepo->getId($id);

        $this->recordExists($serviceGroup);

        $data['serviceGroup'] = $serviceGroup;
        $data['action'] = route('admin.services.group.update', $id);

        return view('admin.services.group.form', $data)->with(['title' => 'Редактирование группы услуг']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceGroupRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceGroupRequest $request, $id)
    {
        $serviceGroup = $this->serviceGroupRepo->getId($id);

        $this->recordExists($serviceGroup);

        $data = $request->all();
        $result = $serviceGroup->update($data);

        if ($result) {
            $this->setFeed('Изменил группу услуг  &laquo;' . trim($request->title) . '&raquo;');
        }

        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.services.index'));
    }
}
