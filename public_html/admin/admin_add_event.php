<?php
// Simple password-protected form to create new events.
require_once __DIR__ . '/../db_connect.php';

$ADMIN_PASSWORD = getenv('ADMIN_PASSWORD') ?: 'changeme123';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedPassword = $_POST['admin_password'] ?? '';
    if (!hash_equals($ADMIN_PASSWORD, $submittedPassword)) {
        $message = '<div class="alert alert-danger">Invalid admin password.</div>';
    } else {
        $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $imageUrl = filter_var(trim($_POST['image_url'] ?? ''), FILTER_SANITIZE_URL);
        $totalSeats = filter_var($_POST['total_seats'] ?? 0, FILTER_VALIDATE_INT);

        if (!$name || !$totalSeats || $totalSeats <= 0) {
            $message = '<div class="alert alert-warning">Name and total seats are required.</div>';
        } else {
            $stmt = $pdo->prepare("INSERT INTO events (name, description, image_url, total_seats, used_seats, status) VALUES (:name, :description, :image_url, :total_seats, 0, 'active')");
            $stmt->execute([
                ':name' => $name,
                ':description' => $description,
                ':image_url' => $imageUrl,
                ':total_seats' => $totalSeats,
            ]);
            $message = '<div class="alert alert-success">Event created successfully.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="h4 mb-4">Admin: Add Event</h1>
        <?php echo $message; ?>
        <form method="POST" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label class="form-label">Admin Password</label>
                <input type="password" name="admin_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Image URL</label>
                <input type="url" name="image_url" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Total Seats</label>
                <input type="number" name="total_seats" min="1" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</body>
</html>
