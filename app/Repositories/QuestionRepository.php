<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository
{
    protected $question;

    public function __construct(Question $question)
    {
        $this->question = $question;
    }

    public function create($data)
    {
        return $this->question->create($data);
    }

    public function findAll()
    {
        return $this->question->all();
    }

    public function find($id)
    {
        return $this->question->find($id);
    }

    public function findBy($filters)
    {
        return $this->question->where($filters)->get();
    }

    public function update($id, $data)
    {
        return $this->question->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->question->where('id', $id)->delete();
    }
}
