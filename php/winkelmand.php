<?php
session_start();
require 'db.php';
include 'header.php';

// Check of user ingelogd is
if (!isset($_SESSION['user']['id'])) {
    echo "<p>Je moet inloggen om je winkelmand te bekijken.</p>";
    include 'footer.php';
    exit;
}

$userId = (int)$_SESSION['user']['id']; // Gebruik sessie

// Voeg product toe als POST aanwezig is
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // Check of product al in winkelmand staat
    $stmt = $pdo->prepare("SELECT aantal FROM user_cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $product_id]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        // Verhoog aantal
        $stmt = $pdo->prepare("UPDATE user_cart SET aantal = aantal + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $product_id]);
    } else {
        // Voeg nieuw toe
        $stmt = $pdo->prepare("INSERT INTO user_cart (user_id, product_id, aantal) VALUES (?, ?, 1)");
        $stmt->execute([$userId, $product_id]);
    }
}

// Verwijder product
if (isset($_POST['remove_product_id'])) {
    $removeId = (int)$_POST['remove_product_id'];
    $stmt = $pdo->prepare('DELETE FROM user_cart WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $removeId]);
    header('Location: winkelmand.php');
    exit;
}

// Haal winkelmand op
$cart = [];
$totaal = 0;
$stmt = $pdo->prepare('SELECT product_id, aantal FROM user_cart WHERE user_id = ?');
$stmt->execute([$userId]);
foreach ($stmt as $row) {
    $cart[$row['product_id']] = $row['aantal'];
}

// Haal producten uit database
$producten = [];
$stmt = $pdo->query("SELECT * FROM products");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $producten[$row['id']] = $row;
}
?>

<head>
  <link rel="stylesheet" href="../css/style.css">
  <script src="../js/main.js" defer></script>
</head>

<main class="cart-container">
  <h1 class="cart-title">Jouw Winkelmand</h1>

  <?php if (!$userId): ?>
    <p>Log in om je winkelmand te bekijken.</p>

  <?php elseif (empty($cart)): ?>
    <p class="cart-empty">Je winkelmand is leeg. Klik <a class="cart-link3" href="../php/index.php">hier</a> om onze producten te bekijken.</p>

  <?php else: ?>
    <div class="cart-items">
      <?php foreach ($cart as $id => $aantal): 
        if (!isset($producten[$id])) continue;
        $p = $producten[$id];
        $sub = $p['prijs'] * $aantal;
        $totaal += $sub;
      ?>
        <div class="cart-card">
          <div class="cart-product">
            <div class="cart-img">
              <img src="../img/<?= htmlspecialchars($p['img']); ?>" alt="<?= htmlspecialchars($p['naam']); ?>">
            </div>
            <div class="cart-info">
              <h3><?= htmlspecialchars($p['naam']); ?></h3>
              <span>Aantal: <?= $aantal; ?></span>
              <span>Prijs: €<?= number_format($p['prijs'], 2, ',', '.'); ?></span>
              <span>Subtotaal: €<?= number_format($sub, 2, ',', '.'); ?></span>
            </div>
          </div>
          <div class="cart-actions">
            <form method="post" style="display:inline">
              <input type="hidden" name="remove_product_id" value="<?= $id ?>">
              <button type="submit" class="btn-remove" title="Verwijder">&times;</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="cart-summary">
      Totaal: €<?= number_format($totaal, 2, ',', '.'); ?>
    </div>

    <div class="cart-button">
      <button id="openPopup" class="cart-link">Bestel nu</button>
    </div>

    <!-- Popup -->
    <div class="popup-overlay" id="popupOverlay">
      <div class="popup">
        <p>Weet je zeker dat je deze bestelling wilt afronden?</p>
        <form action="bestel.php" method="post">
            <button type="submit" class="bestellen">Bestellen</button>
            <button type="button" class="cancel" id="cancelBtn">Cancel</button>
        </form>

      </div>
    </div>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
