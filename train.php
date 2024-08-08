<?php
require_once __DIR__ . '/vendor/autoload.php';

use Phpml\Classification\NaiveBayes;
use Phpml\Tokenization\WordTokenizer;

// Define Burmese stop words
$stopWordsforlabel = [
    'ကျွန်တော်သည်','တပ်ရင်းမှူး','တပ်တည်','ဖွဲ့စည်းပုံ','တပ်ရင်း','တပ်မတော်','ဘယ်သူလဲ','ဘယ်မှာလဲ','ဘာလဲ','သိလား','တွေရဲ့','ရဲ့ဖွဲ့စည်းပုံ','ဖွဲ့စည်း','ထားလဲ','တိတိကျကျ','ထုတ်ပေးပါ','သိလို့ရမလား','ကို','က','မှ','တွင်','သည်','နေရာ','တွေ','ပြပါ','ပြ','ထိပ်တန်းလျှို့ဝှက်','ကို','အင်အား','သိချင်တယ်','တယ်','အင်အားကို','များများ','ဘယ်လောက်','နာမည်','တပ်မမှူး','တပ်မ','တပ်','အမည်','ကြေးနန်း','ကြေး','နန်း','မှတ်','ပုံ','တင်','အ','စီ','အ','ရင်','ခံ','စာ','အစီအရင်ခံစာ','တိုက်ပွဲ','ကျေးနန်း','ကြေးနန်းစာ','ကြေးနန်းစာမှတ်ပုံတင်','မှတ်ပုံတင်ကြေးနန်းစာ','နေ့စဉ်ကြေးနန်း','ကြေးနန်းစာပုံစံ','ထိတွေ့မှုကြေးနန်း','တိုက်ပွဲအစီအရင်ခံစာ','မြေပုံ',
];
$stopWordsforCommand = [
    'ပြပါ','ပြ','ကို','တယ်','ရှာ','ပေး','ချင်','လို့','ပါ','ပေးပါ','ချင်တယ်','ရှာပေး','ရှာပေးပါ','သိချင်တယ်','ပြပေးပါ','သိလို့ရမလား','သိချင်တယ်','သိချင်ပါတယ်','ပြပေးလို့ရမလား','ပြပေး','သွားပေး','သွားပါ','ကိုသွားပါ','ကိုသွားပေးပါ','သွားလိုက်','သွားချင်တယ်','သွားချင်','သွားလိုက်','သွားကွာ','သွားလိုက်ပါ','ရေး','ရေးပေး','ရေးပေးပါ','စမ်း','ရေပေးစမ်း','ရေးပေးပါဦး','ရေးစေချင်တယ်','ရေးပေးစေချင်ပါတယ်','ရေးပေးကွာ','ရေးစေချင်တယ်','ရေးပါ','ရေးဦး','ရေးလိုက်','အကွာအဝေး','အကွာအဝေးတိုင်း','အကွာအဝေးတိုင်းပါ','အကွာအဝေးတိုင်းပါဦး','အကွာအဝေးတိုင်းစမ်းပါ','အကွာအဝေးတိုင်းပေး','အကွာအဝေးတိုင်းပေးပါ','အကွာအဝေးတိုင်းပေးပါဦး','တိုင်း','တိုင်းပေး','တိုင်းလိုက်','တိုင်းလိုက်ဦး','တိုင်းလိုက်ပါဦး','တိုင်းလိုက်စမ်း','တိုင်းပေးဦး','တိုင်းပေးစမ်း','တိုင်းပေးစမ်းပါ','တိုင်းလိုက်ကွာ','တိုင်းပေးပါ'
];


// Load training data for label classification
$trainingData = [];
$labels = [];
$handle = fopen('data.csv', 'r');
// Skip the header row
fgetcsv($handle);
while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    // Tokenize the text
    $tokenizer = new WordTokenizer();
    $words = $tokenizer->tokenize($data[0]);

    // Remove stop words for label classification
    $filteredWords = array_diff($words, $stopWordsforlabel);

    // Convert filtered words to string
    $filteredText = implode(' ', $filteredWords);

    $trainingData[] = [$filteredText]; // Assuming text is in the first column
    $labels[] = $data[1]; // Assuming labels are in the second column
}
fclose($handle);

// Train the model for label classification
$classifierLabel = new NaiveBayes();
$classifierLabel->train($trainingData, $labels);

// Load training data for command classification
$commandData = [];
$commands = [];
$cmfile = fopen('balanced_data.csv', 'r');
// Skip the header row
fgetcsv($cmfile);
while (($commanddata = fgetcsv($cmfile, 1000, ',')) !== false) {
    // Tokenize the text
    $tokenizer = new WordTokenizer();
    $words = $tokenizer->tokenize($commanddata[0]);

    // Remove stop words for command classification
    $filteredWords = array_diff($words, $stopWordsforCommand);

    // Convert filtered words to string
    $filteredText = implode(' ', $filteredWords);

    $commandData[] = [$filteredText]; // Assuming text is in the first column
    $commands[] = $commanddata[1]; // Assuming commands are in the second column
}
fclose($cmfile);

// Train the model for command classification
$classifierCommand = new NaiveBayes();
$classifierCommand->train($commandData, $commands);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputText = $_POST['burmese_text'];
    // Tokenize the input text
    $tokenizer = new WordTokenizer();
    $inputWords = $tokenizer->tokenize($inputText);

    // Remove stop words for label classification
    $filteredInputWordsLabel = array_diff($inputWords, $stopWordsforlabel);
    // Convert filtered input words to string for label classification
    $filteredInputTextLabel = implode(' ', $filteredInputWordsLabel);

    // Predict label
    $predictedLabel = $classifierLabel->predict([$filteredInputTextLabel]);

    // Remove stop words for command classification
    $filteredInputWordsCommand = array_diff($inputWords, $stopWordsforCommand);
    // Convert filtered input words to string for command classification
    $filteredInputTextCommand = implode(' ', $filteredInputWordsCommand);

    // Predict command
    $predictedCommand = $classifierCommand->predict([$filteredInputTextCommand]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burmese Text Classification</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <h1>Burmese Text Classification</h1>
    <form method="post">
        <textarea name="burmese_text" rows="4" cols="50" placeholder="Enter Burmese text..."></textarea>
        <br>
        <input type="submit" value="Classify">
    </form>
    <?php if (isset($predictedLabel)) : ?>
        <p>Predicted Label: <?php echo $predictedLabel; ?></p>
    <?php endif; ?>
    <?php if (isset($predictedCommand)) : ?>
        <p>Predicted Command: <?php echo $predictedCommand; ?></p>
    <?php endif; ?>
</body>
</html>