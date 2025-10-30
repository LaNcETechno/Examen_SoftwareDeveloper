<?php
session_start();
require 'db.php'; // database connectie

// Check of gebruiker is ingelogd
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];

// Verwijder alle producten van deze gebruiker uit de winkelmand
$stmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
$stmt->execute([$userId]);

include 'header.php';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/style.css">
    <title>Bestelling voltooid</title>
</head>
<body>
    <main class="bedankt-main">
        <div id="bedankt-container">
            <h1>Je bestelling is voltooid</h1>
            <p>Bedankt voor je bestelling!</p>
            <a href="index.php"><button>Terug naar homepage</button></a>
        </div>
    </main>
</body>
</html>
<?php include 'footer.php'; ?>