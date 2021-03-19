<?php

namespace App\Repositories\Front;

use \DB;
use App\Models\Faq as Model;
use App\Repositories\CoreRepository;

class FaqRepository extends CoreRepository
{
    function __construct()
    {
        parent::__construct();
    }

    protected function getModelClass()
    {
        return Model::class;
    }

    public function getAllFaq($perPage)
    {
        $result = $this->startConditions()
            ->limit($perPage)
            ->latest()
            ->where(['published' => '1'])
            ->paginate($perPage);

        return $result;
    }

    public function getCategoryFaq($cat_id)
    {
        $result = $this->startConditions()
            ->latest()
            ->where([
                'published' => '1',
                'cat_id' => $cat_id
            ])
            ->get();

        return $result;
    }

    public function getCategories()
    {
        return DB::table('faq_category')
            ->get();
    }

    public function getCategory($param)
    {
        if (is_numeric($param)) {
            $paramName = 'id';
        } else {
            $paramName = 'alias';
        }

        return DB::table('faq_category')
            ->where([$paramName => $param])
            ->first();
    }

}
