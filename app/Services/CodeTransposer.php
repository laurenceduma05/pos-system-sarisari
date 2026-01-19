<?php

namespace App\Services;

class CodeTransposer
{
    // Chord map with positions in the chromatic scale
    protected $chords = [
        'C' => 0, 'C#' => 1, 'D' => 2, 'D#' => 3, 'E' => 4, 
        'F' => 5, 'F#' => 6, 'G' => 7, 'G#' => 8, 'A' => 9, 
        'A#' => 10, 'B' => 11
    ];

    public function transposeChord($chord, $steps)
    {
        if (!isset($this->chords[$chord])) {
            return $chord;  // Return original if it's not a recognized chord
        }

        $currentIndex = $this->chords[$chord];
        $newIndex = ($currentIndex + $steps) % 12;

        // Handle negative indices (wrap around the scale)
        if ($newIndex < 0) {
            $newIndex += 12;
        }


        // Return the new chord
        return array_flip($this->chords)[$newIndex];
    }

    // Transpose the lyrics with all chords in them
    public function transposeLyrics($lyrics, $currentKey, $targetKey)
    {
        // Calculate the transpose steps based on the selected key
        $steps = $this->chords[$targetKey] - $this->chords[$currentKey];

        // Use regex to match chords in the lyrics
        return preg_replace_callback('/\b(C|C#|D|D#|E|F|F#|G|G#|A|A#|B)\b/', function ($matches) use ($steps) {
            return $this->transposeChord($matches[0], $steps);
        }, $lyrics);
    }
}
