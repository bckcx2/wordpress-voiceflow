<?php
// fetch-report-tags.php

$data = json_decode(file_get_contents('php://input'), true);
$sessionId = $data['sessionId'];
$tagsArray = $data['tagsArray'];

function getReportTagsBySessionId($tagsArray, $sessionId) {
    foreach ($tagsArray as $session) {
        if ($session['sessionId'] === $sessionId) {
            return $session['reportTags'];
        }
    }
    return [];
}

$reportTags = getReportTagsBySessionId($tagsArray, $sessionId);
header('Content-Type: application/json');
echo json_encode($reportTags);
?>
