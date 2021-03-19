<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\ServiceExecutor as Model;

class ServiceExecutorRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getAllServicesExecutor()
    {
        $result = $this
            ->startConditions()
            ->latest()
            ->where('published', '1')
            ->get();

        return $result;
    }

    public function getServicesExecutorPagination($perPage)
    {
        $result = $this
            ->startConditions()
            ->latest()
            ->paginate($perPage);

        return $result;
    }

    public function getCountServicesExecutor()
    {
        $result = $this
            ->startConditions()
            ->get()
            ->count();

        return $result;
    }

    public function getGroupServicesExecutorPagination($perPage, $selectGroup)
    {
        $result = $this
            ->startConditions()
            ->where('service_group_id', $selectGroup)
            ->latest()
            ->paginate($perPage);

        return $result;
    }

    public function getCountGroupServicesExecutor($selectGroup)
    {
        $result = $this
            ->startConditions()
            ->where('service_group_id', $selectGroup)
            ->get()
            ->count();

        return $result;
    }

    public function getGroups()
    {
        $result = ServiceGroup::orderBy('title', 'asc')->get();

        return $result;
    }

}
