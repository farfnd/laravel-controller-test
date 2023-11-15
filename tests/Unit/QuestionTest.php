<?php

namespace Tests\Unit;

use App\Models\Question;
use App\Models\User;
use App\Models\Voice;
use App\Repositories\QuestionRepository;
use App\Services\QuestionService;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;

class QuestionTest extends TestCase
{
    protected Model $user;

    protected Model $question;

    protected QuestionService $questionService;

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

        $this->questionRepository = $this->createMock(QuestionRepository::class);

        $this->questionService = new QuestionService($this->questionRepository);
    }

    public function test_create_question()
    {
        $data = [
            'user_id' => $this->user->id,
            'title' => $this->question->title,
            'body' => $this->question->body
        ];

        $this->questionRepository
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn($this->question);

        $result = $this->questionService->create($data);

        $this->assertEquals($this->question, $result['data']);
    }

    public function test_get_questions()
    {
        $this->questionRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$this->question]);

        $result = $this->questionService->getAll();

        $this->assertEquals([$this->question], $result);
    }


    public function test_get_one_question()
    {
        $this->questionRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->question->id)
            ->willReturn($this->question);

        $result = $this->questionService->get($this->question->id);

        $this->assertEquals($this->question, $result);
    }

    public function test_delete_question()
    {
        $this->questionRepository
            ->expects($this->once())
            ->method('delete')
            ->with($this->question->id);

        $result = $this->questionService->delete($this->question->id);

        $this->assertEquals([
            'status' => 200,
            'message' => 'successfully deleted your question'
        ], $result);
    }
}
