<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CandidateSkillsModel;
use App\Models\GithubAnalysisModel;
use App\Models\AiInterviewModel;
use App\Libraries\AiInterviewer;

class AiInterview extends BaseController
{
    public function interview()
    {
        //return view('interview/start');
        return redirect()->to(base_url('/ai-interview/overview'));

    }
    public function overview()
    {
        $candidateId = session()->get('user_id');

        $skillModel = new CandidateSkillsModel();
        $githubModel = new GithubAnalysisModel();

        $resumeSkills = $skillModel
            ->where('candidate_id', $candidateId)
            ->findColumn('skill_name') ?? [];

        $github = $githubModel
            ->where('candidate_id', $candidateId)
            ->first();

        $githubLanguages = $github
            ? explode(',', $github['languages_used'])
            : [];

        return view('interview/start', [
            'resumeSkills' => $resumeSkills,
            'githubLanguages' => $githubLanguages
        ]);
    }


    public function start()
    {
        $candidateId = session()->get('user_id');

        $skillModel = new CandidateSkillsModel();
        $githubModel = new GithubAnalysisModel();

        $skills = $skillModel
            ->where('candidate_id', $candidateId)
            ->findAll();

        $github = $githubModel
            ->where('candidate_id', $candidateId)
            ->first();

        $githubLanguages = $github
            ? explode(',', $github['languages_used'])
            : [];

        $ai = new AiInterviewer();
        $questions = $ai->generateQuestions($skills, $githubLanguages);

        $sessionModel = new AiInterviewModel();

        $sessionId = $sessionModel->insert([
            'candidate_id' => $candidateId,
            'questions' => json_encode($questions),
            'answers' => json_encode([]),
            'status' => 'in_progress',
            'started_at' => date('Y-m-d H:i:s')
        ]);

        session()->set('interview_session_id', $sessionId);
        session()->set('current_question', 0);

        return redirect()->to('/ai-interview/question');
    }

    public function question()
    {
        $sessionId = session()->get('interview_session_id');
        if (!$sessionId) {
            return redirect()->to('/interview');
        }

        $model = new AiInterviewModel();
        $session = $model->find($sessionId);

        $questions = json_decode($session['questions'], true);
        $currentIndex = session()->get('current_question');

        if (!isset($questions[$currentIndex])) {
            return redirect()->to('/interview/result');
        }

        return view('interview/question', [
            'question' => $questions[$currentIndex],
            'current_question' => $currentIndex + 1,
            'total_questions' => count($questions),
            'session_id' => $sessionId
        ]);
    }

    public function submit()
    {
        $sessionId = session()->get('interview_session_id');
        $answer = $this->request->getPost('answer');

        $model = new AiInterviewModel();
        $session = $model->find($sessionId);

        $answers = json_decode($session['answers_json'], true);
        $answers[] = $answer;

        $model->update($sessionId, [
            'answers_json' => json_encode($answers)
        ]);

        session()->set(
            'current_question',
            session()->get('current_question') + 1
        );

        return redirect()->to('/interview/question');
    }

    public function result()
    {
        $sessionId = session()->get('interview_session_id');

        $model = new AiInterviewModel();
        $session = $model->find($sessionId);

        $ai = new AiInterviewer();

        $questions = json_decode($session['questions_json'], true);
        $answers = json_decode($session['answers_json'], true);

        $result = $ai->evaluateInterview($questions, $answers);

        $model->update($sessionId, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ]);

        return view('interview/result', $result);
    }

    public function saveDraft()
    {
        return $this->response->setJSON(['success' => true]);
    }
}
