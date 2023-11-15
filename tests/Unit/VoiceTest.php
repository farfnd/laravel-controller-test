<?php

namespace Tests\Unit;

use App\Models\Question;
use App\Models\User;
use App\Models\Voice;
use App\Repositories\QuestionRepository;
use App\Repositories\VoiceRepository;
use App\Services\VoiceService;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class VoiceTest extends TestCase
{
    protected Model $user;

    protected Model $question;

    protected Model $voice;

    protected VoiceService $voiceService;

    protected $voiceRepository;

    protected $questionRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->question = Mockery::mock(Question::class);
        $this->question->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $this->question->shouldReceive('getAttribute')->with('user_id')->andReturn(2);
        $this->question->shouldReceive('getAttribute')->with('title')->andReturn('title');
        $this->question->shouldReceive('getAttribute')->with('body')->andReturn('body');

        $this->user = Mockery::mock(User::class);
        $this->user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $this->user->shouldReceive('getAttribute')->with('name')->andReturn('name');
        $this->user->shouldReceive('getAttribute')->with('email')->andReturn('email');
        $this->user->shouldReceive('getAttribute')->with('password')->andReturn('password');

        $this->voice = Mockery::mock(Voice::class);
        $this->voice->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $this->voice->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $this->voice->shouldReceive('getAttribute')->with('question_id')->andReturn(1);
        $this->voice->shouldReceive('getAttribute')->with('value')->andReturn(1);

        $this->voiceRepository = $this->createMock(VoiceRepository::class);
        $this->questionRepository = $this->createMock(QuestionRepository::class);


        $this->voiceService = new VoiceService($this->voiceRepository, $this->questionRepository);
    }

    public function test_create_vote()
    {
        $data = [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($data['question_id'])
            ->willReturn($this->question);

        $this->voiceRepository
            ->expects($this->once())
            ->method('findByUserAndQuestion')
            ->with($data['user_id'], $data['question_id'])
            ->willReturn(null);

        $this->voiceRepository
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn($this->voice);

        $result = $this->voiceService->createVote($data);

        $this->assertEquals([
            'status' => 200,
            'message' => 'Voting completed successfully'
        ], $result);
    }

    public function test_create_vote_own_question()
    {
        $data = [
            'user_id' => $this->question->user_id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($data['question_id'])
            ->willReturn($this->question);

        $this->voiceRepository
            ->expects($this->never())
            ->method('findByUserAndQuestion');

        try {
            $this->voiceService->createVote($data);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('User is not allowed to vote for this question', $e->getMessage());
        }
    }

    public function test_create_vote_more_than_once()
    {
        $data = [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($data['question_id'])
            ->willReturn($this->question);

        $this->voiceRepository
            ->expects($this->once())
            ->method('findByUserAndQuestion')
            ->with($data['user_id'], $data['question_id'])
            ->willReturn($this->voice);

        try {
            $this->voiceService->createVote($data);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The user is not allowed to vote more than once', $e->getMessage());
        }
    }

    public function test_create_vote_question_not_found()
    {
        $data = [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($data['question_id'])
            ->willReturn(null);

        try {
            $this->voiceService->createVote($data);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Question not found', $e->getMessage());
        }
    }

    public function test_update_vote()
    {
        $this->voiceRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->voice->id, ['value' => 0])
            ->willReturn($this->voice);


        $result = $this->voiceService->updateVote($this->voice, 0);

        $this->assertEquals([
            'status' => 201,
            'message' => 'update your voice'
        ], $result);
    }

    public function test_update_vote_same_value()
    {
        $data = [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($data['question_id'])
            ->willReturn($this->question);

        $this->voiceRepository
            ->expects($this->once())
            ->method('findByUserAndQuestion')
            ->with($data['user_id'], $data['question_id'])
            ->willReturn($this->voice);

        $this->voiceRepository
            ->expects($this->never())
            ->method('update');

        try {
            $this->voiceService->createVote($data);
            $this->fail('Expected InvalidArgumentException was not thrown');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('The user is not allowed to vote more than once', $e->getMessage());
        }
    }
}
