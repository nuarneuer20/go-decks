<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShuffleController extends Controller
{
    // Shuffle and distribute 52 playing cards to a given number of people
    public function shuffle(Request $request) {

        // 1. Get the number of people from the request
        $numPeople = $request->input('totalPeople');

        // 2. Define suits and values of a standard deck
        $suits = ['S', 'H', 'D', 'C']; // Spade, Heart, Diamond, Club
        $values = [1,2,3,4,5,6,7,8,9,10,11,12,13];

        // 3. Initialize an empty deck
        $deck = [];

        // 4. Map special values to their corresponding labels
        $valueLabels = [
            1 => 'A',  // Ace
            10 => 'X', // Ten as 'X'
            11 => 'J', // Jack
            12 => 'Q', // Queen
            13 => 'K'  // King
        ];

        // 5. Build the full deck of 52 cards with suit and value
        foreach ($suits as $suit) {
            foreach ($values as $val) {
                $label = $valueLabels[$val] ?? (string) $val; // Use label or number
                $deck[] = "$suit-$label"; // e.g., H-A, D-7, C-K
            }
        }

        // 6. Shuffle the full deck randomly
        shuffle($deck);

        // 7. Initialize empty hands for each person
        $hands = array_fill(0, $numPeople, []);

        // 8. Distribute cards round-robin to each person
        foreach ($deck as $i => $card) {
            $hands[$i % $numPeople][] = $card;
        }

        // 9. Format the result with each person's hand
        $result = array_map(function ($hand, $index) {
            $handNumber = $index + 1; // People numbering starts at 1
            $people = implode(',', $hand); // Join cards with commas

            // If no cards assigned, mention it
            if ($people === "") {
                return "Hand $handNumber: No card distributed". "\n";
            }

            // Return formatted result string
            return "Hand $handNumber: $people". "\n";
        }, $hands, array_keys($hands));

        // 10. Return the result as a JSON response
        return response()->json([
            'result' => $result,
            'message' => 'Cards shuffled and distributed successfully!'
        ], 200);
    }
}
