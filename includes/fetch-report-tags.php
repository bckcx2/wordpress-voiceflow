<?php
// Get the session ID from the query string
$sessionId = isset($_GET['sessionId']) ? $_GET['sessionId'] : null;

if ($sessionId) {
    // Fetch the tags for the given session ID from the $tagsArray
    foreach ($tagsArray as $item) {
        if ($item['sessionId'] == $sessionId) {
            // Output the reportTags as a JSON array
            echo json_encode($item['reportTags']);
            exit;
        }
    }

    // Output an empty JSON array if the session ID was not found in the $tagsArray
    echo json_encode([]);
} else {
    // Output an error message if no session ID was provided
    echo 'Error: No session ID provided.';
}
?>