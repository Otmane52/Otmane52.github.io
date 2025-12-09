<?php
// Password-protected tool to attach new redemption keys to events.
require_once __DIR__ . '/../db_connect.php';

$ADMIN_PASSWORD = getenv('ADMIN_PASSWORD') ?: 'changeme123';
$message = '';

$eventsStmt = $pdo->prepare('SELECT id, name FROM events ORDER BY name');
$eventsStmt->execute();
$events = $eventsStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedPassword = $_POST['admin_password'] ?? '';
    if (!hash_equals($ADMIN_PASSWORD, $submittedPassword)) {
        $message = '<div class="alert alert-danger">Invalid admin password.</div>';
    } else {
        $eventId = filter_var($_POST['event_id'] ?? 0, FILTER_VALIDATE_INT);
        $rawKeys = trim($_POST['keys'] ?? '');

        if (!$eventId || $rawKeys === '') {
            $message = '<div class="alert alert-warning">Event and keys are required.</div>';
        } else {
            $keys = preg_split('/\r?\n/', $rawKeys);
            $keys = array_filter(array_map(function ($key) {
                $clean = preg_replace('/[^A-Za-z0-9-]/', '', trim($key));
                return $clean;
            }, $keys));

            if (empty($keys)) {
                $message = '<div class="alert alert-warning">No valid keys provided.</div>';
            } else {
                $inserted = 0;
                $stmt = $pdo->prepare("INSERT INTO `keys` (event_id, key_code, is_used) VALUES (:event_id, :key_code, 0)");

                foreach ($keys as $key) {
                    try {
                        $stmt->execute([
                            ':event_id' => $eventId,
                            ':key_code' => $key,
                        ]);
                        $inserted++;
                    } catch (PDOException $e) {
                        // Skip duplicates quietly.
                        continue;
                    }
                }

                $message = '<div class="alert alert-success">' . $inserted . ' key(s) added successfully.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Keys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="h4 mb-4">Admin: Add Keys</h1>
        <?php echo $message; ?>
        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Admin Password</label>
                <input type="password" name="admin_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event</label>
                <select name="event_id" class="form-select" required>
                    <option value="">Select event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?php echo (int)$event['id']; ?>"><?php echo htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Keys (one per line)</label>
                <textarea name="keys" class="form-control" rows="6" placeholder="ABC123\nXYZ789" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Keys</button>
        </form>
    </div>
</body>
</html>
