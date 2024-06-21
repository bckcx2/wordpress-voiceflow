<?php
// File: wp-voiceflow-view-transcripts.php

// Function to handle the transcripts page content.
function voiceflow_transcripts_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Define your projectID and authorization token
    //$projectID = '666b007557355c3c6e2c2d76';
    $project_id = get_option('voiceflow_project_id', '');
    $authToken = 'VF.DM.6671d43b1fa2c9baf88773d0.BDH7DpNAhRAUuhtE';

    // Define the API endpoint
    $apiEndpoint = "https://api.voiceflow.com/v2/transcripts/{$project_id}";

    // Initialize a new cURL session
    $ch = curl_init();

    // Set the options for the cURL session
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: {$authToken}",
        "Accept: application/json"
    ));

    // Execute the cURL session and fetch the response
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $transcripts = json_decode($response, true);

    // Start the dropdown list
    echo '<h1>Select Session</h1>';
    echo '<form method="post">';
    echo '<select name="sessionID">';

    // Populate the dropdown list with the session IDs
    foreach ($transcripts as $transcript) {
        echo '<option value="' . $transcript['_id'] . '">' . $transcript['sessionID'] . '</option>';
    }

    // End the dropdown list
    echo '</select>';
    echo '<input type="submit" name="submit" value="Get Transcript Dialog">';
    echo '</form>';

    // If the form is submitted

    if (isset($_POST['submit'])) {
        // Get the selected session ID
        $sessionID = $_POST['sessionID'];
        var_dump($sessionID);  // Debug line
        


        // Check if sessionID is a 24 character hex string
        if (ctype_xdigit($sessionID) && strlen($sessionID) == 24) {
            // Define the API endpoint for the transcript dialog
            $apiEndpoint = "https://api.voiceflow.com/v2/transcripts/{$project_id}/{$sessionID}";

            // Initialize a new cURL session
            $ch = curl_init();

            // Set the options for the cURL session
            curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: {$authToken}",
                "Accept: application/json"
            ));

            // Execute the cURL session and fetch the response
            $response = curl_exec($ch);

            // Close the cURL session
            curl_close($ch);

            
            
            
// Decode the JSON response
$dialog = json_decode($response, true);

// Check if decoding was successful
if (json_last_error() === JSON_ERROR_NONE) {
    // Display the messages
    echo '<style>
            .transcript-container {
                width: 100%;
                display: flex;
                flex-direction: column;
            }
            .text-message {
                text-align: left;
                margin-bottom: 20px;
            }
            .request-message {
                text-align: right;
                margin-bottom: 20px;
            }
          </style>';

    echo '<div class="transcript-container"><h1>Transcript Dialog</h1>';

    // Iterate through the array to find text and request messages
    foreach ($dialog as $entry) {
        if (isset($entry['type']) && $entry['type'] === 'text' && isset($entry['payload']['payload']['message'])) {
            // Display the text message
            echo '<div class="text-message">';
            //echo '<h2>Text Message</h2>';
            echo '<p>' . htmlspecialchars($entry['payload']['payload']['message'], ENT_QUOTES, 'UTF-8') . '</p>';
            echo '</div>';
        } elseif (isset($entry['type']) && $entry['type'] === 'request' && isset($entry['payload']['payload']['label'])) {
            // Display the request message
            echo '<div class="request-message">';
            //echo '<h2>Request Message</h2>';
            echo '<p>' . htmlspecialchars($entry['payload']['payload']['label'], ENT_QUOTES, 'UTF-8') . '</p>';
            echo '</div>';
        }
    }

    echo '</div>';
} else {
    echo '<p>Failed to decode JSON response: ' . json_last_error_msg() . '</p>';
}





            
        }






    }
}