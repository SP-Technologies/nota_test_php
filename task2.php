<?php

// Function to download a web page using cURL
function downloadPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// Function to extract headings, abstracts, pictures, and links from HTML content
function extractPageInfo($html) {
    $doc = new DOMDocument;
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($doc);

    // Extract headings
    $headings = [];
    $headingNodes = $xpath->query('//span[@class="mw-headline"]');
    foreach ($headingNodes as $headingNode) {
        $headings[] = $headingNode->nodeValue;
    }

    // Extract abstracts
    $abstracts = [];
    $abstractNodes = $xpath->query('//p[@class="lead"]');
    foreach ($abstractNodes as $abstractNode) {
        $abstracts[] = $abstractNode->nodeValue;
    }

    // Extract pictures
    $pictures = [];
    $pictureNodes = $xpath->query('//img/@src');
    foreach ($pictureNodes as $pictureNode) {
        $pictures[] = $pictureNode->nodeValue;
    }

    // Extract links
    $links = [];
    $linkNodes = $xpath->query('//a/@href');
    foreach ($linkNodes as $linkNode) {
        $links[] = $linkNode->nodeValue;
    }

    return [
        'headings' => $headings,
        'abstracts' => $abstracts,
        'pictures' => $pictures,
        'links' => $links,
    ];
}

// Function to save data to the database
function saveToDatabase($data, $url, $conn) {
    // Create a timestamp in the format: year-month-day hours:minutes:seconds
    $timestamp = date('Y-m-d H:i:s');

    foreach ($data['headings'] as $index => $heading) {
        // Limit the length of the title, URL, picture, and abstract as specified
        $title = substr($heading, 0, 230);
        $url = substr($url, 0, 240);
        $picture = substr($data['pictures'][$index] ?? '', 0, 240);
        $abstract = substr($data['abstracts'][$index] ?? '', 0, 256);

        // Prepare and execute the SQL query to insert data into the database
        $stmt = $conn->prepare("INSERT INTO wiki_sections (date_created, title, url, picture, abstract) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $timestamp, $title, $url, $picture, $abstract);
        $stmt->execute();
        $stmt->close();
    }
}

// Database configuration
$host = 'your_database_host';
$user = 'your_database_user';
$pass = 'your_database_password';
$dbname = 'your_database_name';

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// URL of the Wikipedia page
$url = 'https://www.wikipedia.org/';

// Download the page
$html = downloadPage($url);

// Extract information from the HTML content
$pageInfo = extractPageInfo($html);

// Save data to the database
saveToDatabase($pageInfo, $url, $conn);

// Close the database connection
$conn->close();

?>
