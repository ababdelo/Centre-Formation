<?php 
require_once __DIR__ . '/../classes/module.php';
require_once __DIR__ . '/../config/connection.php';

$moduleObj = new module($pdo);
$modules = $moduleObj->lister();

$message = "";
$erreur = "";
$moduleAEditer = null;


if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];
    $moduleAEditer = $moduleObj->trouver($idEdit);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifier') {
    try {
        $id           = (int) $_POST['id'];
        $code         = trim($_POST['code']);
        $intitule     = trim($_POST['intitule']);
        $description  = trim($_POST['description']);
        $volumeHoraire = (int) preg_replace('/[^0-9]/', '', $_POST['volume_horaire']); 

        $moduleObj->ModifieModule($id, $code, $intitule, $description, $volumeHoraire);
        $message = "Le module a été modifié avec succès.";

        
        $modules = $moduleObj->lister();
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
    try {
        $code          = trim($_POST['code']);
        $intitule      = trim($_POST['intitule']);
        $description   = trim($_POST['description']);
        $volumeHoraire = (int) preg_replace('/[^0-9]/', '', $_POST['volume_horaire']); 

        $moduleObj->ajouter($code, $intitule, $description, $volumeHoraire);
        $message = "Le module a été créé avec succès.";

        
        $modules = $moduleObj->lister();
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

$recherche = trim($_GET['q'] ?? '');

if ($recherche !== '') {
    $mot = mb_strtolower($recherche);
    $modules = array_filter($modules, function ($mod) use ($mot) {
        $texte = mb_strtolower(
            ($mod['code'] ?? '') . ' ' .
            ($mod['intitule'] ?? '') . ' ' .
            ($mod['description'] ?? '') . ' ' .
            ($mod['volume_horaire'] ?? '')
        );
        return mb_strpos($texte, $mot) !== false;
    });
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Modules - Centre Formation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style_formateur.css">
</head>
<body>

  <!-- Header -->
  <header class="top-header">
    <div class="header-left">
      <a href="dashboard.php" class="btn-back" title="Retour aux cartes">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Retour</span>
      </a>
      <h1 class="page-title">Gestion des Modules</h1>
    </div>

    <div class="header-right">
      <div class="admin-profile">
        <div class="avatar-circle">A</div>
        <div class="admin-info">
          <span class="admin-name"><?php session_start(); require_once __DIR__ . '/../config/connection.php';  echo $_SESSION["name"]  ?></span>
          <span class="admin-role">Administrateur</span>
        </div>
      </div>
      <a href="deconnexion.php" class="btn-logout">Déconnexion</a>
    </div>
  </header>

  <!-- Main Container -->
  <main class="main-container">
    <div class="glass-panel">
       <?php if ($erreur): ?>
            <p style="color: #dc2626; font-weight: 600; margin-bottom: 12px;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <?php if ($message): ?>
        <p style="color: #16a34a; font-weight: 600; margin-bottom: 12px;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
      <!-- Rechercher + Créer -->
      <div class="toolbar">
       
       <form method="GET" class="search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input
            type="text"
            id="searchInput"
            name="q"
            value="<?= htmlspecialchars($recherche) ?>"
            placeholder="Rechercher un module..."
          >
        </form>

        <a href="#addModuleModal" class="btn-primary">+ Créer un module</a>
      </div>

      <!-- Liste des Modules -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Intitulé</th>
              <th>Description</th>
              <th>Volume Horaire</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($modules as $mod): ?>
            <tr>
              <td><strong><?= htmlspecialchars($mod['code']) ?></strong></td>
              <td><?= htmlspecialchars($mod['intitule']) ?></td>
              <td><?= htmlspecialchars($mod['description']) ?></td>
              <td><span class="badge-status active"><?= htmlspecialchars($mod['volume_horaire']) ?></span></td>
              <td>
                <a href="?edit=<?= (int) $mod['id'] ?>#editModuleModal" class="btn-edit">Modifier</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!--  Créer Module -->
  <div class="modal-backdrop" id="addModuleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Créer un module</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
        <input type="hidden" name="action" value="ajouter">

        <div class="form-grid">
          <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" placeholder="Ex: MOD-104" required>
          </div>
          <div class="form-group">
            <label>Volume horaire</label>
            <input type="text" name="volume_horaire" placeholder="Ex: 50h" required>
          </div>
          <div class="form-group full-width">
            <label>Intitulé</label>
            <input type="text" name="intitule" placeholder="Titre du module" required>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <input type="text" name="description" placeholder="Brève description du contenu" required>
          </div>
        </div>

        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <!--  Modifier Module -->
  <div class="modal-backdrop" id="editModuleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier le module</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
       <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id" value="<?= (int) ($moduleAEditer['id'] ?? 0) ?>">

        <div class="form-grid">
          <div class="form-group">
            <label>Code</label>
            <input
              type="text"
              name="code"
              value="<?= htmlspecialchars($moduleAEditer['code'] ?? '') ?>"
              required
            >
          </div>
          <div class="form-group">
            <label>Volume horaire</label>
            <input
              type="text"
              name="volume_horaire"
              value="<?= htmlspecialchars($moduleAEditer['volume_horaire'] ?? '') ?>"
              required
            >
          </div>
          <div class="form-group full-width">
            <label>Intitulé</label>
            <input
              type="text"
              name="intitule"
              value="<?= htmlspecialchars($moduleAEditer['intitule'] ?? '') ?>"
              required
            >
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <input
              type="text"
              name="description"
              value="<?= htmlspecialchars($moduleAEditer['description'] ?? '') ?>"
              required
            >
          </div>
        </div>

        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Mettre à jour</button>
        </div>
      </form>
    </div>
   </div>
  <script>
    const inputRecherche = document.getElementById('searchInput');
    const lignesTableau = document.querySelectorAll('tbody tr');

    if (inputRecherche) {
      inputRecherche.addEventListener('input', () => {
        const valeur = inputRecherche.value.toLowerCase().trim();

        lignesTableau.forEach((ligne) => {
          const contenuLigne = ligne.textContent.toLowerCase();
          ligne.style.display = contenuLigne.includes(valeur) ? '' : 'none';
        });
      });
    }
  </script>
</body>
</html>