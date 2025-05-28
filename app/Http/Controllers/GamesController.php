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
        $filePath = base_path('database/datasource.php');
        $gameList = include($filePath);

        $originalCount = count($gameList);

        $updatedList = array_filter($gameList, function ($game) use ($id) {
            return $game['id'] != $id;
        });

        if (count($updatedList) === $originalCount) {
            return response("Game with ID $id not found.\n", 404)
                ->header('Content-Type', 'text/plain');
        }

        // Convert back to PHP code and overwrite the file
        $phpCode = "<?php\nreturn " . var_export(array_values($updatedList), true) . ";\n";
        file_put_contents($filePath, $phpCode);

        // Build response
        $output = "Games List\n";
        foreach ($updatedList as $game) {
            $output .= "ID: {$game['id']}\n";
            $output .= "{$game['title']}\n";
            $output .= "{$game['developer']}\n\n";
        }

        return response($output, 200)
            ->header('Content-Type', 'text/plain');
    }
}
