<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Repositories\Front\FaqRepository;
use Illuminate\Http\Request;

class FaqController extends ApiController
{
    protected $faqRepo;

    public function __construct(FaqRepository $faqRepo)
    {
        parent::__construct();

        $this->faqRepo = $faqRepo;
    }

    public function index()
    {
        $category = $this->faqRepo->getCategories()->first();
        $cat_id = $category->id;

        $faq = $this->faqRepo->getCategoryFaq($cat_id);

        return (FaqResource::collection($faq))
            ->additional(['relationships' => [
                'category' => [
                    'title' => $category->title
                ],
            ]]);
    }

    public function category(Request $request, $cat_id)
    {
        $category = $this->faqRepo->getCategory($cat_id);

        $faq = $this->faqRepo->getCategoryFaq($cat_id);

        return (FaqResource::collection($faq))
            ->additional(['relationships' => [
                'category' => [
                    'title' => $category->title
                ],
            ]]);
    }

    public function categories()
    {
        $categories = $this->faqRepo->getCategories();

        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string',
            'email'    => 'required|email',
            'question' => 'required|string|min:5|max:800',
        ];

        // Валидация с ajax
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $faq = new Faq();
        $faq->name       = clean($request->name);
        $faq->email      = clean($request->email);
        $faq->question   = clean($request->question);
        $faq->published  = '0';
        $faq->ip_address = $request->ip();
        $faq->save();

        $result = [
            'type'    => 'success',
            'message' => 'Ваш вопрос успешно отправлен!'
        ];

        return response()->json($result, 201);
    }

}
