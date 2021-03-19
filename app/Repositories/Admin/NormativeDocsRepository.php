<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\Models\NormativeDocs as Model;
use \DB;

class NormativeDocsRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getNormative_DocsPagination($perPage = 10)
    {
        $result = $this
            ->startConditions()
            ->limit($perPage)
            ->latest()
            ->paginate($perPage);

        return $result;
    }

    public function getCountNormative_Docs()
    {
        $result = $this
            ->startConditions()
            ->get()
            ->count();

        return $result;
    }
}
