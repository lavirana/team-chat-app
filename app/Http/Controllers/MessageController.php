<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    //Get messages for a channel (paginated)
    public function index(Request $request, Channel $channel){
        $messages = $channel->messages()
        ->with(['user','attachments','reactions.user'])
        ->latest()
        ->paginate(30);

        // update last_read_at for this year
         $channel->members()->updateExistingPivot(
            $request->user()->id,
            ['last_read_at' => now()]
         );
         return response()->json($messages);
    }

    //send a message
    public function store(Request $request, Channel $channel)
    {
        $request->validate([
            'content'    => 'nullable|string|required_without:file',
            'file'       => 'nullable|file|max:10240', // 10MB max
            'parent_id'  => 'nullable|exists:messages,id',
        ]);
        $message = Message::create([
            'channel_id' => $channel->id,
            'user_id'    => $request->user()->id,
            'content'    => $request->content,
            'parent_id'  => $request->parent_id,
            'type'       => $request->hasFile('file') ? 'file' : 'text',
        ]);

        // Handle file upload
        if($request->hasFile('file')){
            $file = $request->file('file');
            $path = $file->store("chat-files/channel-{$channel->id}", 'local');

            $message->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $message->update(['type' => str_contains($file->getMimeType(), 'image') ? 'image' : 'file']);
        }
        $message->load(['user', 'attachments', 'reactions']);
// Broadcast to all channel members in real-time
broadcast(new MessageSent($message))->toOthers();

return response()->json($message, 201);
      
    }

    // Edit a message
    public function update(Request $request, Message $message)
    {
        // Only message owner can edit
        if ($message->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate(['content' => 'required|string']);

        $message->update([
            'content'   => $request->content,
            'is_edited' => true,
        ]);

        return response()->json($message);
    }

    // Delete a message
    public function destroy(Request $request, Message $message)
    {
        if ($message->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Delete attached files
        foreach ($message->attachments as $attachment) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $message->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // React to a message
    public function react(Request $request, Message $message)
    {
        $request->validate(['emoji' => 'required|string|max:10']);

        $existing = $message->reactions()
                            ->where('user_id', $request->user()->id)
                            ->where('emoji', $request->emoji)
                            ->first();

        if ($existing) {
            // Remove reaction if already reacted
            $existing->delete();
            return response()->json(['action' => 'removed']);
        }

        $message->reactions()->create([
            'user_id' => $request->user()->id,
            'emoji'   => $request->emoji,
        ]);

        return response()->json(['action' => 'added']);
    }
}
