<?php

namespace App\Http\Controllers\Admin;

use App\Models\Polls\Answer;
use App\Models\Polls\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\PollRepository;
use App\Models\Polls\Poll;
use Carbon\Carbon;

class PollController extends BaseController
{
    protected $pollRepo;

    public function __construct(PollRepository $pollRepo)
    {
        parent::__construct();

        $this->pollRepo = $pollRepo;

        view()->share(['heading' => 'Голосования', 'title' => 'Список голосований']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = 20;
        $data['polls'] = $this->pollRepo->getPollsPagination($perPage);
        $data['countPolls'] = $this->pollRepo->getCountPolls();

        return view('admin.polls.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $poll = new Poll();
        $poll->created_at = Carbon::now();
        $poll->started_at = Carbon::now()->toDateString();
        $poll->ended_at = Carbon::now()->addDays(2)->toDateString();

        $data['action'] = route('admin.polls.store');
        $data['poll'] = $poll;

        return view('admin.polls.form', $data)->with(['title' => 'Создание голосования']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);
        if (isset($request->option_value)) {
            $poll = new Poll([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'multiselect' => $request->get('multiselect'),
                'started_at' => Carbon::now()->toDateString(),
                'ended_at' => Carbon::now()->addDays(2)->toDateString()
            ]);
            $poll->save();

            $a_arr = [];

            foreach ($request->question as $key => $value) {
                $question = new Question([
                    'poll_id' => $poll->id,
                    'title' => $value,
                ]);
                $question->save();

                $a_arr[$key] = $question->id;
            }

            $new_t = [];
            foreach ($request->option_value as $k => $v) {
                foreach ($v as $v2) {
                    $new_t[$a_arr[$k]]['title'][] = $v2['title'];
                    $new_t[$a_arr[$k]]['ordering'][] = $v2['ordering'];
                }
            }

            foreach ($new_t as $k => $v) {
                foreach ($v['title'] as $kt => $vt){
                    $answer = new Answer([
                        'question_id' => $k ,
                        'title' => $vt,
                        'ordering' => $v['ordering'][$kt]
                    ]);
                    $answer->save();
                }
            }

            if ($poll) {
                $this->setFeed('Добавил голосование <a href="' . route('admin.polls.edit', $poll->id) . '">&laquo;' . $request->title . '&raquo;</a>');
            }
            return $this->redirectResponse($poll, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.polls.index'));
        } else {
            return back()->with(['error' => 'У вас должен быть как минимум один ответ на ваш вопрос']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $poll = $this->pollRepo->getId($id);
        $question = $this->pollRepo->getAllQuestions($poll->id);

        foreach ($question as $q){
            $answers[$q->id] = $this->pollRepo->getAllAnswers($q->id);
            $this->recordExists($poll);
            $data['poll'] = $poll;
            $data['question'] = $question;
            $data['answer'] = $answers;
        }
        $data['action'] = route('admin.polls.update', $id);
        return view('admin.polls.form', $data)->with(['title' => 'Редактирование голосования']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $poll = $this->pollRepo->getId($id);
        $question = $this->pollRepo->getAllQuestions($id);
        foreach ($question as $q) {
            Answer::where('question_id', $q->id)->delete();
        }
        Question::where('poll_id', $id)->delete();

        $a_arr = [];
        foreach ($request->question as $key => $value) {
            $question = new Question([
                'poll_id' => $poll->id,
                'title' => $value,
            ]);
            $question->save();

            $a_arr[$key] = $question->id;
        }

        $new_t = [];
        foreach ($request->option_value as $k => $v) {

            foreach ($v as $k2 => $v2) {
                $new_t[$a_arr[$k]]['title'][] = $v2['title'];
                $new_t[$a_arr[$k]]['ordering'][] = $v2['ordering'];
            }
        }

        foreach ($new_t as $k => $v) {

            foreach ($v['title'] as $kt => $vt) {

                $answer = new Answer([
                    'question_id' => $k ,
                    'title' => $vt,
                    'ordering' => $v['ordering'][$kt]
                ]);
                $answer->save();
            }
        }

        $this->recordExists($poll);
        $data = $request->all();
        $result = $poll->update($data);

        if ($result) {
            $this->setFeed('Изменил голосование <a href="' . route('admin.polls.edit', $id) . '">&laquo;' . $request->title . '&raquo;</a>');
        }
        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении'], route('admin.polls.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $poll = $this->pollRepo->getId($id);
        $question = $this->pollRepo->getAllQuestions($id);
        foreach ($question as $q) {
            Answer::where('question_id', $q->id)->delete();
        }
        Question::where('poll_id', $id)->delete();

        $this->recordExists($poll);

        $result = Poll::destroy($id);

        if ($result) {
            $this->setFeed('Удалил голосование &laquo;' . $poll->title . '&raquo;');
        }
        return $this->redirectResponse($result, ['success' => 'Успешно сохранено', 'error' => 'Ошибка при сохранении']);
    }

    public function activate($id)
    {
        $poll = $this->pollRepo->getId($id);

        $this->recordExists($poll);

        $updateData = ['published' => '1'];

        $result = $poll->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Голосование включено', 'error' => 'Ошибка! Голосование не включено']);
    }

    /**
     * Deactivate the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        $poll = $this->pollRepo->getId($id);

        $this->recordExists($poll);

        $updateData = ['published' => '0'];

        $result = $poll->update($updateData);

        return $this->redirectResponse($result, ['success' => 'Голосование отключено', 'error' => 'Ошибка! Голосование не отключено']);
    }

    public function statistic($id)
    {

    }
}
