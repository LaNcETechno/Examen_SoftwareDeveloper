<?php
session_start();
require 'db.php'; // PDO connectie

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];

try {
    $pdo->beginTransaction();

    // Haal producten uit winkelmand
    $stmt = $pdo->prepare("
        SELECT uc.product_id, uc.aantal, p.prijs
        FROM user_cart uc
        JOIN products p ON uc.product_id = p.id
        WHERE uc.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$cartItems) {
        throw new Exception("Je winkelmand is leeg.");
    }

    // Bereken totaal
    $totaal = 0;
    foreach ($cartItems as $item) {
        $totaal += $item['prijs'] * $item['aantal'];
    }

    // Voeg order toe
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_nummer, user_id, totaal, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $orderNummer = uniqid('ORD-');
    $stmt->execute([$orderNummer, $userId, $totaal]);
    $orderId = $pdo->lastInsertId();

    // Voeg order_items toe
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, aantal, prijs)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cartItems as $item) {
        $stmt->execute([$orderId, $item['product_id'], $item['aantal'], $item['prijs']]);
    }

    // Leeg winkelmand
    $stmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
    $stmt->execute([$userId]);

    $pdo->commit();

    // Redirect naar bedankt.php
    header('Location: bedankt.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Er is iets misgegaan: " . $e->getMessage();
}