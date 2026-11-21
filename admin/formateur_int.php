<?php
require_once __DIR__ . '/../config/connection.php';

$message = "";
$erreur = "";
$formateurAEditer = null;

if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];
    $stmtEdit = $pdo->prepare("
        SELECT f.id, f.id_utilisateur, f.identifiant, f.specialite, f.statut, f.rib,
               u.nom, u.prenom, u.email, u.date_naissance
        FROM formateur f
        LEFT JOIN utilisateur u ON f.id_utilisateur = u.id
        WHERE f.id = ?
    ");
    $stmtEdit->execute([$idEdit]);
    $formateurAEditer = $stmtEdit->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'ajouter') {
            $identifiant = trim($_POST['identifiant'] ?? '');
            $specialite  = trim($_POST['specialite'] ?? '');
            $nom         = trim($_POST['nom'] ?? '');
            $prenom      = trim($_POST['prenom'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $statut      = strtoupper(trim($_POST['statut'] ?? 'ACTIF'));
            $rib         = trim($_POST['rib'] ?? '');
            $motDePasse  = trim($_POST['mot_de_passe'] ?? 'Formateur@123');
            $dateNaiss   = trim($_POST['date_naissance'] ?? '1995-01-01');

            if ($identifiant === '' || $specialite === '' || $nom === '' || $prenom === '' || $email === '') {
                throw new Exception("Veuillez remplir tous les champs obligatoires.");
            }

            $chk = $pdo->prepare("SELECT COUNT(*) FROM formateur WHERE identifiant = ?");
            $chk->execute([$identifiant]);
            if ((int) $chk->fetchColumn() > 0) {
                throw new Exception("Cet identifiant de formateur existe déjà.");
            }

            $pdo->beginTransaction();

            $insU = $pdo->prepare("
                INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, date_naissance, role)
                VALUES (?, ?, ?, ?, ?, 'FORMATEUR')
            ");
            $insU->execute([$nom, $prenom, $email, $motDePasse, $dateNaiss]);
            $idUtilisateur = (int) $pdo->lastInsertId();

            $insF = $pdo->prepare("
                INSERT INTO formateur (id_utilisateur, identifiant, specialite, statut, rib)
                VALUES (?, ?, ?, ?, ?)
            ");
            $insF->execute([$idUtilisateur, $identifiant, $specialite, $statut, $rib !== '' ? $rib : null]);

            $pdo->commit();
            $message = "Formateur ajouté avec succès.";
        } elseif ($action === 'modifier') {
            $id            = (int) ($_POST['id'] ?? 0);
            $idUtilisateur = (int) ($_POST['id_utilisateur'] ?? 0);
            $identifiant   = trim($_POST['identifiant'] ?? '');
            $specialite    = trim($_POST['specialite'] ?? '');
            $nom           = trim($_POST['nom'] ?? '');
            $prenom        = trim($_POST['prenom'] ?? '');
            $email         = trim($_POST['email'] ?? '');
            $statut        = strtoupper(trim($_POST['statut'] ?? 'ACTIF'));

            if ($id <= 0 || $identifiant === '' || $specialite === '') {
                throw new Exception("Données de modification invalides.");
            }

            $chk = $pdo->prepare("SELECT COUNT(*) FROM formateur WHERE identifiant = ? AND id <> ?");
            $chk->execute([$identifiant, $id]);
            if ((int) $chk->fetchColumn() > 0) {
                throw new Exception("Cet identifiant est déjà utilisé.");
            }

            $pdo->beginTransaction();

            $updF = $pdo->prepare("UPDATE formateur SET identifiant = ?, specialite = ?, statut = ? WHERE id = ?");
            $updF->execute([$identifiant, $specialite, $statut, $id]);

            if ($idUtilisateur > 0) {
                $updU = $pdo->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id = ?");
                $updU->execute([$nom, $prenom, $email, $idUtilisateur]);
            }

            $pdo->commit();
            $message = "Formateur mis à jour avec succès.";
            $formateurAEditer = null;
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erreur = $e->getMessage();
    }
}

$sqlList = "
    SELECT f.id, f.id_utilisateur, f.identifiant, f.specialite, f.statut, f.rib,
           u.nom, u.prenom, u.email
    FROM formateur f
    LEFT JOIN utilisateur u ON f.id_utilisateur = u.id
    ORDER BY f.id DESC
";
$formateurs = $pdo->query($sqlList)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Formateurs - Centre Formation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style_formateur.css">
</head>
<body>

  <header class="top-header">
    <div class="header-left">
      <a href="dashboard.php" class="btn-back" title="Retour aux cartes">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"></line>
          <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>Retour</span>
      </a>
      <h1 class="page-title">Gestion des Formateurs</h1>
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

  <main class="main-container">
    <div class="glass-panel">

      <?php if ($erreur): ?>
        <p style="color: #dc2626; font-weight: 600; margin-bottom: 12px;"><?= htmlspecialchars($erreur) ?></p>
      <?php endif; ?>

      <?php if ($message): ?>
        <p style="color: #16a34a; font-weight: 600; margin-bottom: 12px;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <div class="toolbar">
        <div class="search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" id="searchInput" placeholder="Rechercher un formateur...">
        </div>

        <a href="#addModal" class="btn-primary">+ Ajouter un formateur</a>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Identifiant</th>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Email</th>
              <th>Spécialité</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($formateurs as $f): ?>
              <?php
                $st = strtoupper((string) ($f['statut'] ?? 'ACTIF'));
                $stLabel = ($st === 'ACTIF') ? 'Actif' : 'Inactif';
              ?>
              <tr>
                <td><strong><?= htmlspecialchars((string) ($f['identifiant'] ?? '')) ?></strong></td>
                <td><?= htmlspecialchars((string) ($f['nom'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string) ($f['prenom'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string) ($f['email'] ?? '-')) ?></td>
                <td><?= htmlspecialchars((string) ($f['specialite'] ?? '')) ?></td>
                <td><span class="badge-status <?= $st === 'ACTIF' ? 'active' : '' ?>"><?= htmlspecialchars($stLabel) ?></span></td>
                <td>
                  <a href="?edit=<?= (int) $f['id'] ?>#editModal" class="btn-edit">Modifier</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <div class="modal-backdrop" id="addModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Ajouter un formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
        <input type="hidden" name="action" value="ajouter">
        <div class="form-grid">
          <div class="form-group">
            <label>Identifiant</label>
            <input type="text" name="identifiant" placeholder="Ex: FOR2026003" required>
          </div>
          <div class="form-group">
            <label>Spécialité</label>
            <input type="text" name="specialite" placeholder="Ex: Développement Digital" required>
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" placeholder="Nom du formateur" required>
          </div>
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" placeholder="Prénom du formateur" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="email@centre.ma" required>
          </div>
          <div class="form-group">
            <label>Date de naissance</label>
            <input type="date" name="date_naissance" value="1995-01-01" required>
          </div>
          <div class="form-group">
            <label>Mot de passe</label>
            <input type="text" name="mot_de_passe" value="Formateur@123" required>
          </div>
          <div class="form-group">
            <label>Statut</label>
            <select name="statut">
              <option value="ACTIF">Actif</option>
              <option value="INACTIF">Inactif</option>
            </select>
          </div>
          <div class="form-group full-width">
            <label>RIB</label>
            <input type="text" name="rib" placeholder="Ex: TEST-RIB-FORM-003">
          </div>
        </div>
        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal-backdrop" id="editModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier le formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id" value="<?= (int) ($formateurAEditer['id'] ?? 0) ?>">
        <input type="hidden" name="id_utilisateur" value="<?= (int) ($formateurAEditer['id_utilisateur'] ?? 0) ?>">
        <div class="form-grid">
          <div class="form-group">
            <label>Identifiant</label>
            <input type="text" name="identifiant" value="<?= htmlspecialchars((string) ($formateurAEditer['identifiant'] ?? '')) ?>" required>
          </div>
          <div class="form-group">
            <label>Spécialité</label>
            <input type="text" name="specialite" value="<?= htmlspecialchars((string) ($formateurAEditer['specialite'] ?? '')) ?>" required>
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars((string) ($formateurAEditer['nom'] ?? '')) ?>" required>
          </div>
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" name="prenom" value="<?= htmlspecialchars((string) ($formateurAEditer['prenom'] ?? '')) ?>" required>
          </div>
          <div class="form-group full-width">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars((string) ($formateurAEditer['email'] ?? '')) ?>" required>
          </div>
          <div class="form-group full-width">
            <label>Statut</label>
            <?php $editStatut = strtoupper((string) ($formateurAEditer['statut'] ?? 'ACTIF')); ?>
            <select name="statut">
              <option value="ACTIF" <?= $editStatut === 'ACTIF' ? 'selected' : '' ?>>Actif</option>
              <option value="INACTIF" <?= $editStatut === 'INACTIF' ? 'selected' : '' ?>>Inactif</option>
            </select>
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