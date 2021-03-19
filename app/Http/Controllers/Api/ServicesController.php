<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Resources\ServiceResource;

class ServicesController extends ApiController
{
    public function index()
    {
        $services = Service::where([
                'published' => '1',
            ])
            ->select(
                'services.*',
                'units.title as unit'
            )
            ->join('units', 'units.id', '=', 'services.unit_id', 'left')
            ->latest()->get();

        return ServiceResource::collection($services);
    }

    public function groups()
    {
        $groups = ServiceGroup::get();

        return ServiceResource::collection($groups);
    }
}
