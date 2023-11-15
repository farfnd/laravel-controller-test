<?php

namespace App\Services;

use App\Repositories\QuestionRepository;

class QuestionService
{

    public function __construct(
        private readonly QuestionRepository $questionRepository
    ) {
    }

    public function getAll()
    {
        return $this->questionRepository->findAll();
    }

    public function create($data)
    {
        $q = $this->questionRepository->create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'body' => $data['body']
        ]);

        return [
            'status' => 200,
            'message' => 'successfully created your question',
            'data' => $q
        ];
    }

    public function get(int $id)
    {
        return $this->questionRepository->find($id);
    }

    public function delete($id)
    {
        $this->questionRepository->delete($id);

        return [
            'status' => 200,
            'message' => 'successfully deleted your question'
        ];
    }
}
