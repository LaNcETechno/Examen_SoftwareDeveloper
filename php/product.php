<?php
require 'db.php';
include 'header.php';

// Controleer of er een ID is meegegeven
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Ongeldig product.</p>";
    include 'footer.php';
    exit;
}

$id = (int)$_GET['id'];

// Haal product op uit de database
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<p>Product niet gevonden.</p>";
    include 'footer.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../css/style.css">
  <title><?= htmlspecialchars($product['naam']) ?></title>
</head>
<body>
  <main class="product-detail">
    <div class="product-detail-card">
      <img src="../img/<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>">
      <h1><?= htmlspecialchars($product['naam']) ?></h1>
      <p class="product-price">€<?= number_format($product['prijs'], 2, ',', '.') ?></p>
      <p class="product-description"><?= nl2br(htmlspecialchars($product['beschrijving'])) ?></p>
      
      <form method="post" action="winkelmand.php">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <button type="submit" class="submit">In winkelmand</button>
      </form>

      <a href="index.php"><button type="button" class="overview-button">Terug naar overzicht</button></a>
    </div>
  </main>
</body>
</html>
<?php include 'footer.php'; ?>
