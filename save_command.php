<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label = $_POST['label'];
    $text = $_POST['text'];

    // Append the new data to the existing CSV file
    $filePath = __DIR__ . '/command.csv';
    $handle = fopen($filePath, 'a');

    if ($handle === false) {
        die("Unable to open the file: $filePath");
    }

    // Write the new data to the file
    fwrite($handle, "$text,$label\n");

    fclose($handle);

    echo "Data has been saved to the file.\n";
} else {
    echo "Invalid request method.";
}
?>
