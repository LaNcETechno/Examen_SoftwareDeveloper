<?php
session_start();
require 'db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1️⃣ Input ophalen en valideren
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Vul zowel e-mailadres als wachtwoord in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Voer een geldig e-mailadres in.';
    } else {
        // 2️⃣ Gebruiker opzoeken
        $stmt = $pdo->prepare('SELECT id, email, password_hash, display_name FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // 3️⃣ Email niet gevonden
            $error = 'Dit e-mailadres is niet in gebruik.';
        } else {
            // 4️⃣ Email bestaat → controleer wachtwoord
            if (!password_verify($password, $user['password_hash'])) {
                $error = 'Het ingevoerde wachtwoord is onjuist.';
            } else {
                // 5️⃣ (Optioneel) Hash bijwerken
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                    $upd->execute([$newHash, $user['id']]);
                }

                // 6️⃣ Sessie starten
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'display_name' => $user['display_name']
                ];

                // 7️⃣ Login-tijd bijwerken
                $upd = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                $upd->execute([$user['id']]);

                // 8️⃣ Doorsturen
                header('Location: index.php');
                exit;
            }
        }
    }
}

include 'header.php';
?>
<main class="login-main">   
  <section class="auth-form">
    <h1 class="auth-title">Inloggen</h1>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" novalidate>
  <label class="form-label">E-mailadres:<br>
    <input type="email" name="email" required>
  </label><br>

  <label class="form-label">Wachtwoord:<br>
    <input type="password" name="password" required>
  </label><br>

  <!-- Nieuw zinnetje onder het wachtwoordveld -->
  <p class="no-account">
    Geen account? <a href="register.php">Maak er hier een aan!</a>
  </p>

  <button class="btn-login" type="submit">Inloggen</button>
</form>
  </section>
</main>
<?php include 'footer.php'; ?>
