<?php

namespace App\Http\Controllers;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
     // Get all workspaces for logged in user
     public function index(Request $request)
     {
         $workspaces = $request->user()
                               ->workspaces()
                               ->with(['owner', 'channels'])
                               ->get();
 
         return response()->json($workspaces);
     }

       // Create workspace
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $workspace = Workspace::create([
            'owner_id'    => $request->user()->id,
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        // Add creator as admin member
        $workspace->members()->attach($request->user()->id, [
            'role'      => 'admin',
            'joined_at' => now(),
        ]);

        // Create default general channel
        $channel = $workspace->channels()->create([
            'created_by' => $request->user()->id,
            'name'       => 'general',
            'description'=> 'General discussion',
        ]);

        // Add creator to general channel
        $channel->members()->attach($request->user()->id, [
            'joined_at' => now(),
        ]);

        return response()->json($workspace->load('channels'), 201);
    }

    //invite member to workspace
  public function invite(Request $request , Workspace $workspace)
  {
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'role'  => 'required|in:admin,member',
    ]);
    $user = \App\Models\User::where('email', $request->email)->first();

    if(!$user){
        return response()->json(['message' => 'User not found'], 404);
    }
    // check if already a member

    if($workspace->members()->where('user_id', $user->id)->exists()){
        return response()->json(['message' => 'User is already a member'], 409);
    }
    $workspace->members()->attach($user->id, [
        'role' => $request->role,
        'joined_at' => now(),
    ]);
    return response()->json(['message' => 'Member added successfully'], 200);
  }

}
