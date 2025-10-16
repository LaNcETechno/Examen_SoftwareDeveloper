<?php
session_start();
require 'db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // basic sanitatie/validatie
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $display_name = trim($_POST['display_name'] ?? '');

    if (!$email) $errors[] = 'Voer een geldig e-mailadres in.';
    if (strlen($password) < 8) $errors[] = 'Wachtwoord minimaal 8 tekens.';
    if ($password !== $password2) $errors[] = 'Wachtwoorden komen niet overeen.';

    if (empty($errors)) {
        // check of email al bestaat
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Dit e-mailadres is al in gebruik.';
        } else {
            // Hash het wachtwoord — gebruik password_hash (bcrypt of argon2id)
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, display_name) VALUES (?, ?, ?)');
            $stmt->execute([$email, $hash, $display_name ?: null]);

            // optioneel: auto-login na registratie
            $userId = $pdo->lastInsertId();
            $_SESSION['user'] = [
                'id' => $userId,
                'email' => $email,
                'display_name' => $display_name
            ];

            $success = true;
        }
    }
}

include 'header.php';
?>
<main class="register-main">
<section class="auth-form">
  <h1 class="auth-title">Registreren</h1>

  <?php if ($errors): ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <p>Registratie gelukt! Je bent nu ingelogd. <a href="index.php">Ga naar home</a></p>
  <?php else: ?>
    <form method="post" novalidate>
      <label class="form-label">Email:<br><input type="email" name="email" required></label><br>
      <label class="form-label">Wachtwoord (min 8):<br><input type="password" name="password" required minlength="8"></label><br>
      <label class="form-label">Herhaal wachtwoord:<br><input type="password" name="password2" required minlength="8"></label><br>
      <label class="form-label">Display naam (optioneel):<br><input type="text" name="display_name"></label><br>
      <button class="btn" type="submit">Registreren</button>
    </form>
  <?php endif; ?>
</section>
</main> 
<?php include 'footer.php'; ?>