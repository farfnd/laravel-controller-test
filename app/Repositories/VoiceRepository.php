<?php

namespace App\Repositories;

use App\Models\Voice;

class VoiceRepository
{
    protected $voice;

    public function __construct(Voice $voice)
    {
        $this->voice = $voice;
    }

    public function create($data)
    {
        return $this->voice->create($data);
    }

    public function findAll()
    {
        return $this->voice->all();
    }

    public function find($id)
    {
        return $this->voice->find($id);
    }

    public function findBy($column, $value)
    {
        return $this->voice->where($column, $value)->get();
    }
    public function findByUserAndQuestion($userId, $questionId)
    {
        return $this->voice
            ->where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function update($id, $data)
    {
        return $this->voice->where('id', $id)->update($data);
    }
}
