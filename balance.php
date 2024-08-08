<?php
// ဒေတာဖိုင်ကို ဖတ်ပြီး တူညီတဲ့ sample အရေအတွက်နဲ့ စီစဉ်ပေးမယ့် function
function balance_samples($filePath, $labelIndex, $desiredCount) {
    $handle = fopen($filePath, 'r');
    $data = [];
    $labels = [];

    // ဖိုင်ထဲက ဒေတာတွေကို ဖတ်ပြီး array ထဲမှာ ထည့်မယ်
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        $labels[] = $row[$labelIndex];
        $data[] = $row;
    }
    fclose($handle);

    // တံဆိပ်တွေရဲ့ အရေအတွက်ကို တွက်မယ်
    $labelCounts = array_count_values($labels);
    $minCount = min($labelCounts);

    // တူညီတဲ့ sample အရေအတွက်နဲ့ စီစဉ်မယ်
    $balancedData = [];
    $currentLabelCounts = array_fill_keys(array_keys($labelCounts), 0);
    foreach ($data as $row) {
        $label = $row[$labelIndex];
        if ($currentLabelCounts[$label] < $desiredCount) {
            $balancedData[] = $row;
            $currentLabelCounts[$label]++;
        }
    }

    // စီစဉ်ပြီးတဲ့ ဒေတာတွေကို ဖိုင်ထဲပြန်ရေးမယ်
    $outputHandle = fopen('data_sec.csv', 'w');
    foreach ($balancedData as $row) {
        fputcsv($outputHandle, $row);
    }
    fclose($outputHandle);
}

// သင့်ရဲ့ ဒေတာဖိုင်နဲ့ တံဆိပ်ရဲ့ index ကို ထည့်ပြီး function ကို ခေါ်မယ်
balance_samples('data.csv', 1, 15);
