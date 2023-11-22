<?php

function findMatchingFiles($folderPath, $pattern) {
    $matchingFiles = [];

    // Open the directory
    if ($handle = opendir($folderPath)) {
        // Loop through all files in the specified folder
        while (false !== ($filename = readdir($handle))) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $filename;

            // Check if the file matches the specified pattern
            if (preg_match($pattern, $filename) && pathinfo($filename, PATHINFO_EXTENSION) == 'ixt' && is_file($filePath)) {
                $matchingFiles[] = $filename;
            }
        }

        closedir($handle);
    }

    return $matchingFiles;
}

function main() {
    $folderPath = "/datafiles";
    $pattern = '/^[a-zA-Z0-9]+\.ixt$/';  // Regular expression for names consisting of numbers and letters of the Latin alphabet with .ixt extension

    // Find matching files
    $matchingFiles = findMatchingFiles($folderPath, $pattern);

    // Display the names of matching files, ordered by name
    sort($matchingFiles);
    foreach ($matchingFiles as $filename) {
        echo $filename . PHP_EOL;
    }
}

// Run the main function
main();
?>

