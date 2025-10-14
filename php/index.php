<?php
session_start();
include 'header.php';
require 'db.php'; 

// Haal producten uit de database
$result = $conn->query("SELECT * FROM products ORDER BY id ASC");
$producten = [];
while ($row = $result->fetch_assoc()) {
    $producten[] = $row;
}

// Toevoegen aan de winkelmand in database
if (isset($_POST['product_id'])) {
    $userId = $_SESSION['user']['id'] ?? null;

    // Als gebruiker niet is ingelogd → stuur naar login.php
    if (!$userId) {
        header("Location: login.php");
        exit;
    }

    $id = (int)$_POST['product_id'];

    // Kijkt of product al in winkelmand zit
    $stmt = $pdo->prepare('SELECT aantal FROM user_cart WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $id]);
    $row = $stmt->fetch();

    if ($row) {
        // Verhoogt aantal met 1 als product al in winkelmand zit
        $upd = $pdo->prepare('UPDATE user_cart SET aantal = aantal + 1 WHERE user_id = ? AND product_id = ?');
        $upd->execute([$userId, $id]);
    } else {
        // Anders, nieuw product in winkelmand
        $ins = $pdo->prepare('INSERT INTO user_cart (user_id, product_id, aantal) VALUES (?, ?, 1)');
        $ins->execute([$userId, $id]);
    }

    header("Location: winkelmand.php");
    exit;
}
?> 
<!DOCTYPE html>
<html lang="nl">
<head>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/main.js" defer></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<main class="products">
  <h1 class="product-title">Onze Producten</h1>
  <div class="product-grid">
    <?php foreach ($producten as $p): ?>
      <div class="product-card">
        <img src="../img/<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['naam']) ?>">
        <h2><?= htmlspecialchars($p['naam']) ?></h2>
        <p class="product-price">€<?= number_format($p['prijs'], 2, ',', '.') ?></p>
        <p class="product-description"><?= htmlspecialchars($p['beschrijving']) ?></p>
        <form method="post">
          <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
          <a href="product.php?id=<?= $p['id'] ?>">
            <button type="button" class="preview-button">Product bekijken</button>
          </a>
          <button type="submit" class="add-to-cart-button">In winkelmand</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</main>
<?php include 'footer.php'; ?>
