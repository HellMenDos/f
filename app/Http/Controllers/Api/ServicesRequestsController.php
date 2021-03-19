<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Users\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ServicesRequestsController extends ApiController
{
    public function serviceRequests(Request $request)
    {
        $areaId = $this->getAreaId();

        $servicesRequest = ServiceRequest::select(
                'service_requests.*',
                'services_groups.id as group_id',
                'services.title',
                'services.price',
                'units.title as unit'
            )
            ->where([
                ['service_requests.area_id', '=', $areaId],
                ['service_requests.service_id', '<>', 0],
            ])
            ->join('services', 'services.id', '=', 'service_requests.service_id', 'left')
            ->join('services_groups', 'services_groups.id', '=', 'services.group_id', 'left')
            ->join('units', 'units.id', '=', 'services.unit_id', 'left')
            ->latest()
            ->get();

        return ServiceRequestResource::collection($servicesRequest);
    }

    public function store(Request $request)
    {
        $rules = [
            'service_id'      => 'integer|nullable',
            'quantity'        => 'required|integer',
            'execution_date'  => 'string|nullable',
            'execution_hours' => 'integer|nullable',
            'description'     => 'string|min:10|max:500|nullable',
            'text_free'       => 'string|min:10|max:500|nullable',
        ];

        // Валидация с ajax
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $areaId = $this->getAreaId($request);

//        $service = Service::where('id', $request->service_id)->first();
        $service = Service::where('services.id', $request->service_id)
            ->select(
                'services.*',
                'units.title as unit'
            )
            ->join('units', 'units.id', '=', 'services.unit_id', 'left')
            ->first();

        $planningData = $request->all();
        $planningData['price'] = $service->price;
        $planningData['unit'] = $service->unit;

        if ($areaId) {
            $requestData = new ServiceRequest();
            $requestData->service_id      = $request->service_id;
            $requestData->quantity        = $request->quantity;
            $requestData->execution_date  = $request->execution_date;
            $requestData->execution_hours = $request->execution_hours;
            $requestData->description     = $request->description;
            $requestData->status          = 0;
            $requestData->text_free       = $request->text_free;
            $requestData->area_id         = $areaId;
            $requestData->planning        = serialize($planningData);

            if ($requestData->save()) {
                $requestData->title = $service->title;
                return (new ServiceRequestResource($requestData))
                    ->response()
                    ->setStatusCode(Response::HTTP_CREATED);
            }
        }
    }

    private function getAreaId()
    {
        $user = User::findOrFail(Auth::user()->id);
        $area = $user->areas;

        return $area[0]->id;
    }

    public function notActive(Request $request) {
        $servicesRequest = ServiceRequest::where([
            'service_requests.id' => $request->id,
        ])->first();

        $servicesRequestData = [
            'status' => 4,
        ];
        $servicesRequest->update($servicesRequestData);

        return (new ServiceRequestResource($servicesRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function setRating(Request $request) {
        $servicesRequest = ServiceRequest::where([
            'service_requests.id' => $request->id,
        ])->first();

        $servicesRequestData = [
            'rating' => $request->rating,
        ];

        $servicesRequest->update($servicesRequestData);

        return (new ServiceRequestResource($servicesRequest))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
