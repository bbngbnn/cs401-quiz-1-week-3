<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GamesController extends Controller
{
    protected $game_list;

    public function __construct()
    {
        $this->game_list = require __DIR__ . '/../../../database/datasource.php';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Step 3. Your code here
        return view('games.index', ['games' => $this->game_list]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Step 4: Find the game by ID
        $filtered = array_filter($this->game_list, function ($game) use ($id) {
            return $game['id'] == $id; // correct comparison
        });

        $filtered = array_values($filtered); // Reindex the array

        if (empty($filtered)) {
            abort(404, 'Game not found.');
        }

        $game = $filtered[0]; // ✅ Now this is safe

        return view('games.show', ['game' => $game]); // ✅ Use 'game' singular
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Step 1: Simulate deletion by filtering the game list
        $updatedList = array_filter($this->game_list, function ($game) use ($id) {
            return $game['id'] != $id; // Keep all games EXCEPT the one with the matching ID
        });

        // Step 2: Reindex the array for consistency
        $updatedList = array_values($updatedList);

        // Step 3: Return JSON with a message and the updated list
        return response()->json([
            'message' => "Game with ID $id deleted successfully.",
            'games' => $updatedList
        ], 200);
    }
}
