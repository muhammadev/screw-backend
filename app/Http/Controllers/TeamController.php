<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Team::with(['players', 'games'])->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'players' => 'required|array',
            'players.*' => 'exists:players,id'
        ]);

        $team = Team::create(['name' => $validated['name']]);

        $team->players()->attach($validated['players']);

        return response()->json([
            'message' => 'Team created successfully!',
            'team' => $team->load('players')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'players' => 'required|array',
            'players.*' => 'exists:players,id'
        ]);

        $team->update(['name' => $validated['name']]);

        $team->players()->sync($validated['players']);

        return response()->json([
            'message' => 'Team updated successfully!',
            'team' => $team->load('players')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
    }
}
