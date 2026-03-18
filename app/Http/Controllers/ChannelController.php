<?php
namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Workspace $workspace)
    {
        $channels = $workspace->channels()->with('members')->get();
        return response()->json($channels);
    }

    public function store(Request $request, Workspace $workspace)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_private'  => 'boolean',
        ]);

        $channel = $workspace->channels()->create([
            'created_by'  => $request->user()->id,
            'name'        => $request->name,
            'description' => $request->description,
            'is_private'  => $request->is_private ?? false,
        ]);

        $channel->members()->attach($request->user()->id, [
            'joined_at' => now(),
        ]);

        return response()->json($channel, 201);
    }

    public function show(Workspace $workspace, Channel $channel)
    {
        return response()->json($channel->load('members'));
    }

    public function update(Request $request, Workspace $workspace, Channel $channel)
    {
        $request->validate([
            'name'        => 'sometimes|string|max:100',
            'description' => 'nullable|string',
        ]);

        $channel->update($request->only('name', 'description'));
        return response()->json($channel);
    }

    public function destroy(Workspace $workspace, Channel $channel)
    {
        $channel->delete();
        return response()->json(['message' => 'Channel deleted']);
    }

    public function join(Request $request, Workspace $workspace, Channel $channel)
    {
        if ($channel->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Already a member'], 409);
        }

        $channel->members()->attach($request->user()->id, [
            'joined_at' => now(),
        ]);

        return response()->json(['message' => 'Joined successfully']);
    }

    public function leave(Request $request, Workspace $workspace, Channel $channel)
    {
        $channel->members()->detach($request->user()->id);
        return response()->json(['message' => 'Left channel']);
    }
}