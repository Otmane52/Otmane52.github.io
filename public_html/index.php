<?php
// Landing page for event key redemption.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Seat Redemption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
    <header class="bg-primary text-white py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Premium Events</h1>
                    <p class="mb-0">Redeem your key to claim a seat instantly.</p>
                </div>
                <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#redeemModal">Redeem Key</button>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Available Events</h2>
            <small class="text-muted">Updates every 15 seconds</small>
        </div>
        <div id="eventsContainer" class="row g-4">
            <!-- Event cards injected dynamically -->
        </div>
    </main>

    <!-- Redeem Modal -->
    <div class="modal fade" id="redeemModal" tabindex="-1" aria-labelledby="redeemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="redeemModalLabel">Redeem Your Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="redeemForm">
                        <input type="text" name="key_code" placeholder="Enter your key" required class="form-control mb-3" maxlength="64">
                        <input type="email" name="email" placeholder="Enter your email (optional)" class="form-control mb-3" maxlength="120">
                        <button type="submit" class="btn btn-success w-100">Redeem</button>
                    </form>
                    <div id="redeemResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/script.js"></script>
</body>
</html>
