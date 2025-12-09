<?php
// Validates a redemption request and marks a key as used when applicable.
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

// Sanitize inputs.
$keyCode = isset($_POST['key_code']) ? trim($_POST['key_code']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

$keyCode = preg_replace('/[^A-Za-z0-9-]/', '', $keyCode);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

if ($keyCode === '') {
    echo json_encode(['success' => false, 'message' => 'Key is required.']);
    exit;
}

try {
    // Lock the key row (and joined event) for consistent seat counts.
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT k.id, k.key_code, k.is_used, k.used_by, k.event_id, e.total_seats, e.used_seats, e.status
                            FROM `keys` k
                            INNER JOIN events e ON e.id = k.event_id
                            WHERE k.key_code = :code
                            FOR UPDATE");
    $stmt->execute([':code' => $keyCode]);
    $key = $stmt->fetch();

    // Validation: key must exist and be unused.
    if (!$key || (int)$key['is_used'] === 1) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Invalid or already used key.']);
        exit;
    }

    // Validation: event must still be active and have capacity.
    if ($key['status'] !== 'active' || (int)$key['used_seats'] >= (int)$key['total_seats']) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Event is no longer available.']);
        exit;
    }

    // Update key usage metadata.
    $updateKey = $pdo->prepare("UPDATE `keys`
                                SET is_used = 1, used_by = :email, used_at = NOW()
                                WHERE id = :id");
    $updateKey->execute([
        ':email' => $email !== '' ? $email : null,
        ':id' => $key['id'],
    ]);

    // Increment seats and mark event as full when needed.
    $updateEvent = $pdo->prepare("UPDATE events
                                  SET used_seats = used_seats + 1,
                                      status = CASE WHEN used_seats + 1 >= total_seats THEN 'full' ELSE status END
                                  WHERE id = :event_id");
    $updateEvent->execute([':event_id' => $key['event_id']]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Seat redeemed successfully!']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to redeem key right now.']);
}
