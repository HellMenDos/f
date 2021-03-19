<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Models\Polls\Poll;
use App\Http\Resources\EventResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Users\User;

class EventController extends ApiController
{
    public function show(Request $request)
    {
        $areaId = $this->getAreaId();

        // Голосование
        $polls = Poll::where('published', '1')
            ->latest()
            ->get();

        foreach ($polls as $poll) {
            $dateArr = explode('-', $poll->started_at);
            $year = $dateArr[0];
            $month = $dateArr[1];
            $day = $dateArr[2];
            $poll->start = Carbon::create($year, $month, $day, 0, 0, 0, 'Asia/Yekaterinburg');

            $dateArr = explode('-', $poll->ended_at);
            $year = $dateArr[0];
            $month = $dateArr[1];
            $day = $dateArr[2];
            $poll->end = Carbon::create($year, $month, $day, 24, 0, 0, 'Asia/Yekaterinburg');
        }

        // Заявки
        $servicesRequest = ServiceRequest::whereRaw('service_requests.execution_date >= curdate()')
            ->select(
                'service_requests.*',
                'services_groups.id as group_id',
                'services.title',
                'services.price'
            )
            ->where([
                ['service_requests.service_id', '<>', 0],
                ['service_requests.status', '<>', 3],
                ['service_requests.status', '<>', 4],
            ])
            ->join('services', 'services.id', '=', 'service_requests.service_id', 'left')
            ->join('services_groups', 'services_groups.id', '=', 'services.group_id', 'left')
            ->get();

        $events['servicesRequest'] = $servicesRequest;
        $events['polls'] = $polls;
        $events['meter_reading'] = ['start' => $this->metersDataPeriodStart, 'end' => $this->metersDataPeriodEnd];

        return new EventResource($events);
    }

    private function getAreaId()
    {
        $user = User::findOrFail(Auth::user()->id);
        $area = $user->areas;

        return $area[0]->id;
    }
}
