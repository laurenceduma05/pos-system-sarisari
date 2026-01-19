<script setup>
import { ref } from 'vue';

// Define the song lyrics and chords.
const song = ref([
  {
    lyrics: ["Amazing Grace, how", "sweet the", "sound", "That saved a", "wretch like", "me"],
    chords: ["D", "G/D", "D", "A/D", "D", "D/F#"]
  },
  {
    lyrics: ["I once was", "lost but", "now am", "found", "Was blind but", "now I see"],
    chords: ["G", "D", "A/D", " ", " ", "D", "G", "D"]
  },
  {
    lyrics: ["My chains are", "gone, I've been set", "free", "My God, my", "Savior has", "ransomed me"],
    chords: ["D/F#", "G", "D/F#", "G/B", "D/A", "D/F#"]
  },
  {
    lyrics: ["And like a", "flood His mercy", "rains", "Unending", "love, Amazing", "Grace"],
    chords: ["G", "D/F#", "G", "D/F#", "Em7", "A7"]
  }
]);

// Define available chords and their transpositions
const chordsMap = {
  'C': 0, 'C#': 1, 'D': 2, 'D#': 3, 'E': 4, 'F': 5, 'F#': 6, 'G': 7, 'G#': 8, 'A': 9, 'A#': 10, 'B': 11
};

// Define the available keys (to be used in the dropdown)
const availableKeys = Object.keys(chordsMap);

// Store the selected key and the transposition interval
const selectedKey = ref('D'); // Default key
const transpositionInterval = ref(0);

// Function to transpose a single chord
const transposeChord = (chord, interval) => {
  const rootChord = chord.replace(/[0-9]+/g, ''); // Remove any suffixes like 'm', '7', etc.
  const suffix = chord.replace(rootChord, ''); // Capture any suffix (e.g., 'm', '7', etc.)

  const rootNoteIndex = chordsMap[rootChord];
  if (rootNoteIndex === undefined) return chord; // If it's an unknown chord, return it as-is
  
  // Calculate the new root chord index after applying the interval
  const newRootIndex = (rootNoteIndex + interval + 12) % 12;
  const newRootChord = Object.keys(chordsMap).find(key => chordsMap[key] === newRootIndex);

  return newRootChord + suffix; // Return the new chord with the suffix
};

// Function to transpose a list of chords
const transposeChords = (chords, interval) => {
  return chords.map(chord => {
    if (!chord || chord === " ") return chord; // Don't transpose empty spaces
    if (chord.includes('/')) {
      // Handle compound chords (e.g., D/F#)
      const [rootChord, bassNote] = chord.split('/');
      return `${transposeChord(rootChord, interval)}/${bassNote}`;
    } else {
      return transposeChord(chord, interval);
    }
  });
};

// Function to handle transposing the entire song based on the selected key
const transposeToSelectedKey = () => {
  const currentKeyIndex = chordsMap[selectedKey.value];
  const transposition = currentKeyIndex - chordsMap['D']; // Transpose from the original key 'D'
  transpositionInterval.value = transposition; // Store the transposition interval
  song.value = song.value.map(line => ({
    lyrics: line.lyrics,
    chords: transposeChords(line.chords, transposition)
  }));
};

// Call the transpose function initially to set to the default key
transposeToSelectedKey();
</script>

<template>
  <div class="container">
    <!-- Dropdown to select the key -->
    <select v-model="selectedKey" @change="transposeToSelectedKey">
      <option v-for="key in availableKeys" :key="key" :value="key">{{ key }}</option>
    </select>

    <!-- Display the song -->
    <div v-for="(line, index) in song" :key="index" class="verse-line">
      <div class="line-container">
        <div class="chords">
          <span v-for="(chord, idx) in line.chords" :key="idx" class="chord">{{ chord }}</span>
        </div>
        <div class="lyrics">
          <span v-for="(word, idx) in line.lyrics" :key="idx" class="word">{{ word }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Styling for the container */
.container {
  max-width: 80%;
  margin: 0 auto;
  padding: 1rem;
}

.custom-select {
  width: 100%;
  font-size: 1.2rem;
  padding: 0.5rem;
  margin-bottom: 2rem;
  border-radius: 0.5rem;
  border: 1px solid #ccc;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.custom-select:focus {
  border-color: #4CAF50;
  box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
}

/* Verse line styles */
.verse-line {
  margin-bottom: 1em;
}


/* Chords styling */
.chords {
  display: flex;
  flex-wrap: wrap;
}

.chord {
  margin: 0 10px;
  font-weight: bold;
  color: blue;
  font-size: 1rem;
}

.lyrics {
  display: flex;
  flex-wrap: wrap;
}

.word {
  margin-right: 10px;
  font-size: 1.2rem;
}
</style>
