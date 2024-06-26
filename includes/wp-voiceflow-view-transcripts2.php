<?php


if (!empty($_POST)) {
    // Form has been submitted
    $transcripts = json_decode($response, true);
    $firstTranscript = $transcripts[0];

    // Do something with $firstTranscript
    // For example, display it:
    echo '<pre>';
    print_r($firstTranscript);
    echo '</pre>';
} else {
    // Form has not been submitted, display the form
    ?>
    <form method="post">
        <input type="submit" value="Load first transcript">
    </form>
    <?php
}

// File: wp-voiceflow-view-transcripts.php
function my_plugin_admin_enqueue_styles() {
    wp_enqueue_style('my-plugin-admin-style', plugin_dir_url(__FILE__) . 'styles2.css');
}
add_action('admin_enqueue_scripts', 'my_plugin_admin_enqueue_styles');


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
    //var_dump($response);

    // Close the cURL session
    curl_close($ch);

    // Decode the JSON response
    $transcripts = json_decode($response, true);
    //var_dump($transcripts);

    // Start the layout
    echo '<div class="container">
    <div class="sidebar">
        <h2>Transcripts (4)</h2>';
    

    // Populate the session list
    
    
    $tagsArray = array();
    foreach ($transcripts as $transcript) {
        $browser = $transcript['browser'];
        $createdAt = $transcript['createdAt'];
        //$transcriptId = $transcript['_id'];
        $reviewed = false;

        //$tagsArray = array();
        // Save the sessionId and reportTags in the array
        $tagsArray[] = array(
            'sessionId' => $transcript['_id'],
            'reportTags' => $transcript['reportTags']
        );
      
        

            if (in_array('system.reviewed', $transcript['reportTags'])) {
                $reviewed = true;
            }
        $saved = false;

            if (in_array('system.saved', $transcript['reportTags'])) {
                $saved = true;
            }
        $formattedDate = date('g:i a, M j', strtotime($createdAt));
        $tags = $transcript['reportTags'];

       


        echo '<div class="user"><p><strong>User</strong><br>ID:<a href="#" class="session-link" data-session-id="' . $transcript['_id'] . '">' . $transcript['sessionID'] . '</a>';

       
        if ($reviewed) {
            echo '<span class="checkmark">✔</span>';
        }
        

        if ($saved) {
            echo '<span class="bookmark">🔖</span>';
        }
        echo '<br>' . $formattedDate . '</p></div>';
        
    }
    
   
echo $transcriptId;
  
   echo '</div>
   <div class="main">
       <div class="transcript-header">
           <h2>Transcript</h2>
           <p>New session started</p>
       </div>
       <div id="transcriptContainer" class="conversation"></div>
   </div>
   <div class="actions">
       <h2>Actions</h2>
    <ul>
        <li><button type="button" onclick="markAsReviewed('.$transcriptId.')">Mark as Reviewed ✔</button></li>
        <li><button type="button">Save for Later</button></li>
        <li><button type="button">Delete</button></li>
    </ul>
    <div class="tags">
        <h3>Tags</h3>
        <div id="reportTagsList" class="textbox">
                   
                </div>
    </div>
       <div class="notes">
           <h3>Notes</h3>
           <p>Leave notes or @mention</p>
       </div>
   </div>
</div>';


    


    

    
    

    ?>
    

    
    
<script>

function markAsReviewed($transcriptId) {
    // Your code here
    console.log('The button was clicked.');
    

     const apiEndpoint = '<?php echo "https://api.voiceflow.com/v2/transcripts/{$project_id}/$sessionId/report_tag/system.reviewed/"; ?>'
        PUT(apiEndpoint, {
            headers: {
                'Authorization': '<?php echo $authToken; ?>',
                'Accept': 'application/json'
            }
        })
} 


document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.session-link').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const sessionId = this.getAttribute('data-session-id');
            fetchTranscript(sessionId);

            // Fetch tags for the selected session
            var tagsArray = <?php echo json_encode($tagsArray); ?>;
            fetchReportTags(sessionId, tagsArray);
        });
    });
});

function fetchReportTags(sessionId, tagsArray) {
    fetch('<?php echo plugin_dir_url(__FILE__); ?>fetch-report-tags.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            sessionId: sessionId,
            tagsArray: tagsArray
        }),
    })
    .then(response => response.json())
    .then(reportTags => {
        console.log(reportTags); // Output the fetched report tags

        // Display the report tags
        const reportTagsList = document.getElementById('reportTagsList');
        reportTagsList.innerHTML = ''; // Clear previous tags
        reportTags.forEach(tag => {
            const spanItem = document.createElement('span');
            spanItem.textContent = tag;
            spanItem.className = 'tag';
            reportTagsList.appendChild(spanItem);
        });
    });
}

    

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
            console.log(data); // Log the data to inspect its structure
            const container = document.getElementById('transcriptContainer');
            container.innerHTML = ''; // Clear previous transcript

            data.forEach(entry => {
                console.log(entry); // Log each entry to inspect its structure
                if (entry.type === 'text' || entry.type === 'request') {
                    let messageDiv = document.createElement('div');
                    
                    if (entry.type === 'text' && entry.payload.type === 'text' && entry.payload.payload && entry.payload.payload.message) {
                        const messageContent = entry.payload.payload.message.split('\n').map(paragraph => `<p>${paragraph}</p>`).join('');
                        messageDiv.classList.add('message', 'user-message');
                        messageDiv.innerHTML = `
                            <div class="message-header">
                                <span class="message-time">${new Date(entry.startTime).toLocaleTimeString()}</span>
                            </div>
                            <div class="message-body">
                                ${messageContent}
                            </div>
                        `;
                        container.appendChild(messageDiv);
                    } else if (entry.type === 'request' && entry.payload.type && entry.payload.payload && entry.payload.payload.label) {
                        let messageContainerDiv = document.createElement('div');
                        messageContainerDiv.classList.add('message-container');

                        const messageContent = entry.payload.payload.label.split('\n').map(paragraph => `<p>${paragraph}</p>`).join('');
                        messageDiv.classList.add('message', 'ai-message');
                        messageDiv.innerHTML = `
                            <div class="message-header">
                                <span class="message-time">${new Date(entry.startTime).toLocaleTimeString()}</span>
                            </div>
                            <div class="message-body-request">
                                ${messageContent}
                            </div>
                        `;

                        messageContainerDiv.appendChild(messageDiv);
                        container.appendChild(messageContainerDiv);
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching transcript:', error));
    }
</script>






    <?php
    

}
?>

