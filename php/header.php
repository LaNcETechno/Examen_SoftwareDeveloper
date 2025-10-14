<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = isset($_SESSION['user']);
?>
<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>CHIEFS of SHOES</title>
  <link rel="stylesheet" href="../css/style.css?v=1.0">
  <script src="../js/main.js" defer></script>
  <script src="https://kit.fontawesome.com/71efa2c44e.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="navbar">
  <div><a class="logo" href="index.php">CHIEFS of SHOES</a></div>
  <div class="auth">
    <?php if ($isLoggedIn): ?>
      <span>Welkom, <?= htmlspecialchars($_SESSION['user']['display_name'] ?? $_SESSION['user']['email']) ?></span>
      <a href="index.php" class="nav-link"><i class="fa-solid fa-house"></i></a>
      <a href="winkelmand.php" class="nav-link2"><i class="fa-solid fa-cart-shopping"></i></a>

      <!-- Dropdown container -->
    <div class="dropdown">
      <i class="fa-solid fa-user" id="userIcon" style="cursor:pointer; font-size: 1.2rem; color: white;"></i>
      <div class="dropdown-content" id="dropdownMenu">
          <a href="mijn_bestellingen.php">Mijn bestellingen</a>
          <a href="logout.php">Uitloggen</a>
      </div>
    </div>

    <?php else: ?>
      <a href="login.php" class="nav-link">Inloggen</a>
      <a href="register.php" class="nav-link">Registreren</a>
    <?php endif; ?>
  </div>
</header>

<main>