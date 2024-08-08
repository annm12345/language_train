<?php
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\NaiveBayes;

// Training data with labeled sentences
$trainingData = [
    ['မြန်မာ ကျေးဇူးတင်ပါတယ်', ['noun', 'verb', 'subject', 'verb', 'noun']],
    ['ဒီ ကို သင်ချင်းစာ ဆောင်ရွက်ပါ', ['noun', 'noun', 'verb', 'noun', 'verb']],
    ['ချက် ချင်းပြီး သင့် အဖြစ် ပြောပြပါ', ['noun', 'verb', 'subject', 'noun', 'verb', 'verb']]
];

// Extract features and labels from training data
$samples = [];
$labels = [];
foreach ($trainingData as $data) {
    $sentence = $data[0];
    $tokens = explode(' ', $sentence);

    $sample = [];
    $label = [];
    foreach ($tokens as $index => $token) {
        // Assign parts of speech based on the position of the word in the sentence
        if ($index == 0 || $index == count($tokens) - 1) {
            $pos = 'noun'; // Assuming the first and last words are nouns
        } else {
            $pos = 'verb'; // Assuming all other words are verbs
        }

        $sample[] = $token;
        $label[] = $pos;
    }

    $samples[] = $sample;
    $labels[] = $label;
}

// Train the Naive Bayes classifier
$classifier = new NaiveBayes();
$classifier->train($samples, $labels);

// Test the classifier
$testSentence = 'မြန်မာ ရိုးရာနိုင်ငံရှိ နိုင်ငံတကာ အသုံးပြုသူတွေ အားလုံး အသုံးပြုပါတယ်';
$tokens = explode(' ', $testSentence);

$testSample = [];
foreach ($tokens as $index => $token) {
    if ($index == 0 || $index == count($tokens) - 1) {
        $pos = 'noun'; // Assuming the first and last words are nouns
    } else {
        $pos = 'verb'; // Assuming all other words are verbs
    }

    $testSample[] = $token;
}

$predictedLabels = $classifier->predict([$testSample]);

// Output the predicted labels
echo '<pre>';
print_r($predictedLabels);
echo '</pre>';
?>
