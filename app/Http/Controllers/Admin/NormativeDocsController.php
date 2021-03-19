<?php

namespace App\Http\Controllers\Admin;

use App\Models\NormativeDocs;
use App\Repositories\Admin\NormativeDocsRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Services\Image\Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NormativeDocsController extends BaseController
{
    protected $normative_docsRepo;

    public function __construct(NormativeDocsRepository $normative_docsRepo)
    {
        parent::__construct();

        $this->normative_docsRepo = $normative_docsRepo;

        view()->share(['heading' => 'Нормативные документы', 'title' => 'Список нормативных документов']);
    }

    public function index()
    {
        $perPage = 20;
        $data['normative_docs'] = $this->normative_docsRepo->getNormative_DocsPagination($perPage);
        $data['countND'] = $this->normative_docsRepo->getCountNormative_Docs();
        return view('admin.normative-docs.index', $data);
    }

    public function create()
    {
        $normative_docs = new NormativeDocs();
        $normative_docs->created_at = Carbon::now();
        $data['action'] = route('admin.normative-docs.store');
        $data['normative_docs'] = $normative_docs;
        return view('admin.normative-docs.form', $data)->with(['title' => 'Создание нормативного документа']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'imageForm' => 'required|mimes:pdf',
        ]);

        $newFileName = uniqid().'.pdf';
        $request->imageForm->move(public_path('normative_docs'), $newFileName);
        $normative_docs = new NormativeDocs();
        $normative_docs->title     = $request->title;
        $normative_docs->file_path = 'normative_docs/'.$newFileName;
        $normative_docs->save();

        if ($normative_docs) {
            $this->setFeed('Добавил нормативный документ <a href="'. route('admin.normative-docs.edit', $normative_docs->id) .'">&laquo;'. trim($request->title, '&raquo; &laquo;') .'&raquo;</a>');
        }
        return $this->redirectResponse($normative_docs, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.normative-docs.index'));
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $normative_docs = $this->normative_docsRepo->getId($id);
        $this->recordExists($normative_docs);
        $data['normative_docs'] = $normative_docs;
        $data['action'] = route('admin.normative-docs.update', $id);
        return view('admin.normative-docs.form', $data)->with(['title' => 'Редактирование нормативного документа']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'imageForm' => 'required|mimes:pdf',
        ]);

        $normative_docs = $this->normative_docsRepo->getId($id);

        $this->recordExists($normative_docs);

        $updated_at = ($request->updated_at != $normative_docs->updated_at)
            ? $request->updated_at
            : date('Y-m-d H:i:s');

        if(isset($request->imageForm))
        {
            $newFileName = uniqid().'.pdf';
            $request->imageForm->move(public_path('normative_docs'), $newFileName);
            $normative_docs->file_path = 'normative_docs/'.$newFileName;

        }
        $data = $request->all();
        $data['updated_at'] = $updated_at;
        $result = $normative_docs->update($data);


        if ($result) {
            $this->setFeed('Изменил нормативный документ <a href="'. route('admin.normative-docs.edit', $normative_docs->id) .'">&laquo;'. trim($request->title, '&raquo; &laquo;') .'&raquo;</a>');
        }
        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.normative-docs.index'));
    }

    public function download_file(Request $request)
    {
        $normative_docs = NormativeDocs::findOrFail($request->id);
        $file = public_path().$normative_docs->file_path;
        $headers = array('Content-Type: application/pdf',);
        return Response::download($file, $normative_docs->title.'pdf', $headers);

    }

    public function delete($id)
    {
        $nd = $this->normative_docsRepo->getId($id);


        $this->recordExists($nd);

        $result = NormativeDocs::destroy($id);

        if ($result) {
            $this->setFeed('Удалил голосование &laquo;' . $nd->title . '&raquo;');
        }
        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении']);
    }
    public function activate($id)
    {
       $normative_docs = NormativeDocs::find($id);

       $normative_docs->published = '1';

       $result = $normative_docs->save();

       return $this->redirectResponse($result, ['success' => 'Нормативный документ включен', 'error' => 'Ошибка! Нормативный документ не включен']);
    }

    /**
     * Deactivate the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        $normative_docs = NormativeDocs::find($id);

        $normative_docs->published = '0';

        $result = $normative_docs->save();

        return $this->redirectResponse($result, ['success' => 'Нормативный документ отключен', 'error' => 'Ошибка! Нормативный документ не отключен']);
    }
}
