<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use App\Repositories\QuestionRepository;
use App\Repositories\VoiceRepository;
use App\Services\VoiceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    protected Model $user;

    protected Model $question;

    protected Model $voice;

    protected VoiceService $voiceService;

    protected VoiceRepository $voiceRepository;

    protected QuestionRepository $questionRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_create_question()
    {
        $data = [
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => 'test body'
        ];

        $response = $this->postJson('/api/questions', $data);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('successfully created your question', $response['message']);
        $this->assertDatabaseHas('questions', $data);
    }

    public function test_get_all_questions()
    {
        $this->question = Question::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );

        $response = $this->getJson('/api/questions');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(1, count($response['data']));
    }

    public function test_get_question()
    {
        $this->question = Question::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );

        $response = $this->getJson('/api/questions/' . $this->question->id);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals($this->question->id, $response['data']['id']);
    }

    public function test_delete_question()
    {
        $this->question = Question::factory()->create(
            [
                'user_id' => $this->user->id
            ]
        );

        $response = $this->deleteJson('/api/questions/' . $this->question->id);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('successfully deleted your question', $response['message']);
        $this->assertSoftDeleted('questions', ['id' => $this->question->id]);
    }
}
