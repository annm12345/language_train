<?php

require 'vendor/autoload.php';

use Phpml\Classification\NaiveBayes;

// Path to your text file
$filePath = __DIR__ . '/training_data.csv';

// Check if the file exists
if (!file_exists($filePath)) {
    die("File not found: $filePath");
}

// Read the file and process its contents
$fileContent = file_get_contents($filePath);

// Split the file content into lines
$lines = explode("\n", $fileContent);

// Initialize arrays to store samples and labels
$samples = [];
$labels = [];

foreach ($lines as $line) {
    // Split each line into label and text
    $parts = explode(',', $line, 2);

    // Check if the line has both label and text
    if (count($parts) === 2) {
        $label = trim($parts[0]);
        $text = trim($parts[1]);

        $labels[] = $label;
        $samples[] = $text;
    }
}

// Tokenize and count words in each sample
$transformedSamples = [];

foreach ($samples as $sample) {
    // Basic tokenization (you might need to use a more sophisticated tokenizer for Burmese)
    $tokens = preg_split('/\s+/u', $sample, -1, PREG_SPLIT_NO_EMPTY);

    // Count occurrences of each word
    $wordCount = array_count_values($tokens);

    // Convert keys to numerical indices
    $transformedSamples[] = array_values($wordCount);
}

// Check if there are non-empty samples to proceed
if (!empty($transformedSamples)) {
    // Split the dataset into training and testing sets
    $splitIndex = (int) (count($transformedSamples) * 0.8);
    $trainingSamples = array_slice($transformedSamples, 0, $splitIndex);
    $trainingLabels = array_slice($labels, 0, $splitIndex);
    $testingSamples = array_slice($transformedSamples, $splitIndex);
    $testingLabels = array_slice($labels, $splitIndex);

    // Check if training samples are not empty before training
    if (!empty($trainingSamples) && !empty($trainingLabels)) {
        // Train the Naive Bayes classifier
        $classifier = new NaiveBayes();
        $classifier->train($trainingSamples, $trainingLabels);

        // Test the trained model
        $correctPredictions = 0;
        $totalTestingSamples = count($testingSamples);

        for ($i = 0; $i < $totalTestingSamples; $i++) {
            $prediction = $classifier->predict($testingSamples[$i]);
            $actualLabel = $testingLabels[$i];

            if ($prediction === $actualLabel) {
                $correctPredictions++;
            }
        }

       
        $accuracy = $correctPredictions / $totalTestingSamples;

        echo "Accuracy: {$accuracy}\n";

        // Now you can use the trained model to make predictions on new text.
        $newText = "ဖွဲ့စည်းပုံကိုပြောပြ";
        $newTextTokens = preg_split('/\s+/u', $newText, -1, PREG_SPLIT_NO_EMPTY);

        // Count occurrences of each word
        $newTextWordCount = array_count_values($newTextTokens);

        // Convert keys to numerical indices
        $newTextTransformed = array_values($newTextWordCount);

        // Use the trained model to predict the label for the new text
        $newTextPrediction = $classifier->predict($newTextTransformed);

        // Display the prediction
        echo "Prediction for '{$newText}': {$newTextPrediction}\n";
    } else {
        die("Training samples or labels are empty.");
    }
} else {
    die("All samples are empty.");
}
