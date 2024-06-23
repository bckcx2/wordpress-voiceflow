<?php

// File: wp-voiceflow-view-transcripts.php


// Function to handle the transcripts page content.
function voiceflow_transcripts_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Define your projectID and authorization token
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

    // Start the layout
    echo '<div class="container">';
    echo '<div class="column left">';
    echo '<h1>Sessions</h1>';
    echo '<ul id="sessionList">';

    // Populate the session list
    foreach ($transcripts as $transcript) {
        echo '<li><a href="#" class="session-link" data-session-id="' . $transcript['_id'] . '">' . $transcript['sessionID'] . '</a></li>';
    }

    echo '</ul>';
    echo '</div>';
    echo '<div class="column middle">';
    echo '<h1>Transcript Dialog</h1>';
    echo '<div id="transcriptContainer" class="transcript-container"></div>';
    echo '</div>';
    echo '<div class="column right">';
    echo '<h1>Settings</h1>';
    // Add any future settings here
    echo '</div>';
    echo '</div>';

    // Add JavaScript to handle session clicks and fetch the transcript data
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.session-link').forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const sessionId = this.getAttribute('data-session-id');
                    fetchTranscript(sessionId);
                });
            });
        });

        function fetchTranscript(sessionId) {
            const apiEndpoint = '<?php echo "https://api.voiceflow.com/v2/transcripts/{$project_id}/"; ?>' + sessionId;
            fetch(apiEndpoint, {
                headers: {
                    'Authorization': '<?php echo $authToken; ?>',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('transcriptContainer');
                container.innerHTML = ''; // Clear previous transcript

                data.forEach(entry => {
                    let messageDiv = document.createElement('div');
                    messageDiv.classList.add('message');
                    
                    if (entry.type === 'text' && entry.payload.payload.message) {
                        messageDiv.classList.add('text');
                        messageDiv.innerHTML = '<img src="path/to/user-icon.png" alt="User Icon"><p>' + entry.payload.payload.message + '</p>';
                    } else if (entry.type === 'request' && entry.payload.payload.label) {
                        messageDiv.classList.add('request');
                        messageDiv.innerHTML = '<img src="path/to/assistant-icon.png" alt="Assistant Icon"><p>' + entry.payload.payload.label + '</p>';
                    }

                    container.appendChild(messageDiv);
                });
            })
            .catch(error => console.error('Error fetching transcript:', error));
        }
    </script>
    <?php
}
?>
