<?php
// Returns the list of events with seat counts for the frontend.
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

try {
    // Pull events ordered by recency.
    $stmt = $pdo->prepare("SELECT id, name, description, image_url, total_seats, used_seats, status FROM events ORDER BY created_at DESC");
    $stmt->execute();
    $events = $stmt->fetchAll();

    // Ensure status reflects capacity even if rows were imported manually.
    foreach ($events as &$event) {
        if ($event['used_seats'] >= $event['total_seats']) {
            $event['status'] = 'full';
        }
    }

    echo json_encode(['success' => true, 'events' => $events]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to fetch events.']);
}
