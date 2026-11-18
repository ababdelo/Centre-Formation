<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Centre Formation</title>
  <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Poppins:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="page-bg">
    <div class="overlay"></div>

    <div class="brand-title">
      <span class="script-text">Centre</span>
      <h1 class="main-text">Formation</h1>
    </div>

    <div class="glass-box">
      <form method="post" action="checkuser.php">
         <?php require_once __DIR__ . '/config/connection.php';?>
        <?php if (isset($_GET['err'])): ?>
                <p style="color: red;">
                    Email ou mot de passe incorrect !
                </p>
            <?php endif; ?>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-enter">enter</button>
      </form>
    </div>
  </div>

</body>
</html>