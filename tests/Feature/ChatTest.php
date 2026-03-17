<?php
namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Tests\TestCase;

class ChatTest extends TestCase
{
    public function test_user_can_send_message()
    {
        $user    = User::factory()->create();
        $channel = Channel::factory()->create();
        $channel->members()->attach($user->id);

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson("/api/channels/{$channel->id}/messages", [
                             'content' => 'Hello World!'
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('content', 'Hello World!');
    }

    public function test_user_cannot_send_to_channel_they_are_not_member_of()
    {
        $user    = User::factory()->create();
        $channel = Channel::factory()->create();
        // NOT adding user as member

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson("/api/channels/{$channel->id}/messages", [
                             'content' => 'Hello!'
                         ]);

        $response->assertStatus(403);
    }

    public function test_user_can_react_to_message()
    {
        $user    = User::factory()->create();
        $channel = Channel::factory()->create();
        $channel->members()->attach($user->id);
        $message = \App\Models\Message::factory()->create([
            'channel_id' => $channel->id
        ]);

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson("/api/messages/{$message->id}/react", [
                             'emoji' => '👍'
                         ]);

        $response->assertStatus(200)
                 ->assertJsonPath('action', 'added');
    }
}