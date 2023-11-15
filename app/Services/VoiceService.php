<?php

namespace App\Services;

use App\Repositories\QuestionRepository;
use App\Repositories\VoiceRepository;
use InvalidArgumentException;

class VoiceService
{
    public function __construct(
        private readonly VoiceRepository $voiceRepository,
        private readonly QuestionRepository $questionRepository
    ) {
    }

    public function getAll()
    {
        return $this->voiceRepository->findAll();
    }

    public function createVote($data)
    {
        $userId = $data['user_id'];
        $questionId = $data['question_id'];
        $question = $this->questionRepository->find($questionId);

        if (!$question)
            throw new InvalidArgumentException('Question not found', 404);
        if ($question->user_id == $userId)
            throw new InvalidArgumentException('User is not allowed to vote for this question');

        $voice = $this->voiceRepository->findByUserAndQuestion($userId, $questionId);
        if ($voice) {
            return $this->updateVote($voice, $data['value']);
        }

        $this->voiceRepository->create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'value' => $data['value']
        ]);

        return [
            'status' => 200,
            'message' => 'Voting completed successfully'
        ];
    }

    public function updateVote($voice, $value)
    {
        if ($voice->value === $value) {
            throw new InvalidArgumentException('The user is not allowed to vote more than once');
        }

        $this->voiceRepository->update($voice->id, ['value' => $value]);

        return [
            'status' => 201,
            'message' => 'update your voice'
        ];
    }
}
