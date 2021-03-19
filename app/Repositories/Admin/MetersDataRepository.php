<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\MetersData as Model;
use \DB;

class MetersDataRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }
    public function getMetersData()
    {
        $result = $this
            ->startConditions()
            ->orderBy('created_at', 'asc')
            ->get();

        return $result;
    }
    public function getAreaMetersData($area_id)
    {
        $result = DB::table('areas')
            ->where('id', $area_id)
            ->get();
        return $result;
    }
}
