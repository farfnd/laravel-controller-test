<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\User;
use App\Services\VoiceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoiceTest extends TestCase
{
    use RefreshDatabase;

    protected Collection $users;

    protected Model $question;

    protected VoiceService $voiceService;

    public function setUp(): void
    {
        parent::setUp();
        $this->users = User::factory(3)->create();
        $this->question = Question::factory()->create(
            [
                'user_id' => $this->users[1]->id
            ]
        );
    }

    public function test_create_voice()
    {
        $data = [
            'user_id' => $this->users[0]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $response = $this->postJson('/api/voices', $data);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Voting completed successfully', $response['message']);
        $this->assertDatabaseHas('voices', $data);
    }

    public function test_create_voice_own_question()
    {
        $data = [
            'user_id' => $this->users[1]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $response = $this->postJson('/api/voices', $data);
        $this->assertEquals(500, $response['status']);
        $this->assertEquals('User is not allowed to vote for this question', $response['error']);
        $this->assertDatabaseMissing('voices', $data);
    }

    public function test_create_voice_more_than_once()
    {
        $data = [
            'user_id' => $this->users[0]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->postJson('/api/voices', $data);
        $this->assertDatabaseHas('voices', $data);
        $response = $this->postJson('/api/voices', $data);

        $this->assertEquals(500, $response['status']);
        $this->assertEquals('The user is not allowed to vote more than once', $response['error']);
    }

    public function test_create_voice_question_not_found()
    {
        $this->question->delete();
        $this->assertSoftDeleted('questions', ['id' => $this->question->id]);

        $data = [
            'user_id' => $this->users[0]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $response = $this->postJson('/api/voices', $data);

        $this->assertEquals(404, $response['status']);
        $this->assertEquals('Question not found', $response['error']);
        $this->assertDatabaseMissing('voices', $data);
    }

    public function test_get_voices()
    {
        $data = [
            'user_id' => $this->users[0]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->postJson('/api/voices', $data);
        $response = $this->getJson('/api/voices');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(1, count($response['data']));
    }

    public function test_update_voice()
    {
        $data = [
            'user_id' => $this->users[0]->id,
            'question_id' => $this->question->id,
            'value' => 1
        ];

        $this->postJson('/api/voices', $data);
        $data['value'] = 0;
        $response = $this->postJson('/api/voices', $data);

        $this->assertEquals(201, $response['status']);
        $this->assertEquals('update your voice', $response['message']);
    }
}
