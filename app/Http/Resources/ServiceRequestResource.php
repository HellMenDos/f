<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch ($this->status) {
            case 0:
                $status = '<p class="badge badge-info" style="font-size: 90%;font-weight: normal;">Новая заявка</p>';
                $status_title = 'Заявка еще не рассмотрена';
                break;
            case 1:
                $status = '<p class="badge badge-secondary" style="font-size: 90%;font-weight: normal;">Передана на исполнение</p>';
                $status_title = 'Заявка передана в подрядную организацию';
                break;
            case 2:
                $status = '<p class="badge badge-warning" style="font-size: 90%;font-weight: normal;">Принята на исполнение</p>';
                $status_title = 'Заявка находится в стадии выполнения';
                break;
            case 3:
                $status = '<p class="badge badge-success" style="font-size: 90%;font-weight: normal;">Выполнена</p>';
                $status_title = 'Заявка выполнена';
                break;
            case 4:
                $status = '<p class="badge badge-light" style="font-size: 90%;font-weight: normal;">Отменено</p>';
                $status_title = 'Заявка отменена';
                break;
        }

        return [
            'type'       => 'serviceRequest',
            'id'         => $this->id,
            'attributes' => [
                'title'           => $this->title,
                'description'     => $this->description,
                'created_at'      => $this->created_at,
                'execution_date'  => $this->execution_date,
                'execution_hours' => $this->execution_hours,
                'quantity'        => $this->quantity,
                'service_id'      => $this->service_id,
                'group_id'        => $this->group_id,
                'price'           => $this->price,
                'unit'            => $this->unit,
                'status'          => $status,
                'status_id'       => $this->status,
                'status_title'    => $status_title,
                'rating'          => $this->rating,
                'planning'        => unserialize($this->planning),
            ],
        ];
    }
}
