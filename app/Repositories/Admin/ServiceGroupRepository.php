<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\ServiceGroup as Model;

class ServiceGroupRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getServicesGroupPagination($perPage)
    {
        $result = $this
            ->startConditions()
            ->latest()
            ->paginate($perPage);

        return $result;
    }


    public function getGroups()
    {
        $result = $this
            ->startConditions()
            ->orderBy('title', 'asc')
            ->get();

        return $result;
    }


    public function getCountGroupServices($selectGroup)
    {
        $result = $this
            ->startConditions()
            ->where('group_id', $selectGroup)
            ->get()
            ->count();

        return $result;
    }


}
