<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\ServiceRequest as Model;

class ServiceRequestRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getServiceRequestById($id) {
        $result = $this
            ->startConditions()
            ->select(
                'service_requests.*',
                'services.title',
                'services.price'
            )
            ->where('service_requests.id', $id)
            ->join('services', 'services.id', '=', 'service_requests.service_id', 'left')
            ->first();

        return $result;
    }

    public function getServiceRequestsPagination($perPage)
    {
        $result = $this
            ->startConditions()
            ->select(
                'service_requests.*',
                'services.title',
                'services.price'
            )
            ->join('services', 'services.id', '=', 'service_requests.service_id', 'left')
            ->latest()
            ->paginate($perPage);

        return $result;
    }

    public function getServiceRequests()
    {
        $result = $this
            ->startConditions()
            ->orderBy('title', 'asc')
            ->get();

        return $result;
    }

    public function getCountServiceRequests()
    {
        $result = $this
            ->startConditions()
            ->get()
            ->count();

        return $result;
    }


}
