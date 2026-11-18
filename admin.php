<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Espace Administrateur - Centre Formation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style_admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&display=swap" rel="stylesheet">
</head>
<body>

  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1>Espace <h1 class="newfont" style=" color: #F59E0B;">Administrateur</h1></h1>
       <?php
                session_start();
                require_once __DIR__ . '/config/connection.php';
                    ?>
                    <p style="color: black;">
                        BIENVENUE ADMIN <?= $_SESSION["name"] ?>
</p>
    </header>

    <div class="cards-grid">

      <a href="formateurs.php" class="card-3d">
        <img src="assets/images/download (11).jpg" alt="Gestion des formateurs" class="card-bg-img">
        <div class="card-overlay"></div>
        <div class="card-content">
          <span class="card-badge">Formateurs</span>
          <h2 class="card-title">Gérer les formateurs</h2>
        </div>
      </a>

     
      <a href="modules.php" class="card-3d">
        <img src="assets/images/download (12).jpg" alt="Gestion des modules" class="card-bg-img">
        <div class="card-overlay"></div>
        <div class="card-content">
          <span class="card-badge">Modules</span>
          <h2 class="card-title">Gérer les modules</h2>
        </div>
      </a>

      
      <a href="affectations.php" class="card-3d">
        <img src="assets/images/affectation.jpg" alt="Gestion des affectations" class="card-bg-img">
        <div class="card-overlay"></div>
        <div class="card-content">
          <span class="card-badge">Affectations</span>
          <h2 class="card-title">Gérer les affectations</h2>
        </div>
      </a>

    </div>
  </div>

</body>
</html>