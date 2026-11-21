<?php
require_once __DIR__ . '/../classes/affectation.php';
require_once __DIR__ . '/../config/connection.php';

$affectationObj = new affectation($pdo);
$message = "";
$erreur = "";
$affAEditer = null;

$modulesList = $pdo->query("SELECT id, code, intitule, volume_horaire FROM module ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

$formateursList = [];
try {
    $formateursList = $pdo->query("
        SELECT f.id, f.identifiant, f.specialite, u.nom, u.prenom
        FROM formateur f
        LEFT JOIN utilisateur u ON f.id_utilisateur = u.id
        ORDER BY f.id
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $formateursList = $pdo->query("SELECT id, identifiant, specialite FROM formateur ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
}

$groupesList = [];
try {
    $groupesList = $pdo->query("SELECT * FROM groupe ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $groupesList = [];
}

if (isset($_GET['edit'])) {
    $idEdit = (int) $_GET['edit'];
    $stmtEdit = $pdo->prepare("SELECT id, id_groupe, id_module, id_formateur, annee FROM affectation WHERE id = ?");
    $stmtEdit->execute([$idEdit]);
    $affAEditer = $stmtEdit->fetch(PDO::FETCH_ASSOC) ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'ajouter') {
            $gpId   = (int) ($_POST['id_groupe'] ?? 0);
            $idMod  = (int) ($_POST['id_module'] ?? 0);
            $idForm = (int) ($_POST['id_formateur'] ?? 0);
            $annee  = (int) ($_POST['annee'] ?? date('Y'));

            if ($gpId <= 0 || $idMod <= 0 || $idForm <= 0 || $annee <= 0) {
                throw new Exception("Veuillez remplir tous les champs.");
            }

            $affectationObj->ajouter($gpId, $idMod, $idForm, $annee);
            $message = "Affectation ajoutée avec succès.";
        } elseif ($action === 'modifier') {
            $id     = (int) ($_POST['id'] ?? 0);
            $gpId   = (int) ($_POST['id_groupe'] ?? 0);
            $idMod  = (int) ($_POST['id_module'] ?? 0);
            $idForm = (int) ($_POST['id_formateur'] ?? 0);
            $annee  = (int) ($_POST['annee'] ?? date('Y'));

            if ($id <= 0 || $gpId <= 0 || $idMod <= 0 || $idForm <= 0 || $annee <= 0) {
                throw new Exception("Données de modification invalides.");
            }

            $sqlUpd = "UPDATE affectation SET id_groupe = ?, id_module = ?, id_formateur = ?, annee = ? WHERE id = ?";
            $stmtUpd = $pdo->prepare($sqlUpd);
            $stmtUpd->execute([$gpId, $idMod, $idForm, $annee, $id]);

            $message = "Affectation mise à jour avec succès.";
            $affAEditer = null;
        }
    } catch (Exception $e) {
        $erreur = $e->getMessage();
    }
}

$sqlList = "
    SELECT
        a.id,
        a.id_groupe,
        a.id_module,
        a.id_formateur,
        a.annee,
        m.code AS module_code,
        m.intitule AS module_intitule,
        m.volume_horaire,
        f.identifiant AS formateur_identifiant,
        f.specialite AS formateur_specialite
    FROM affectation a
    LEFT JOIN module m ON a.id_module = m.id
    LEFT JOIN formateur f ON a.id_formateur = f.id
    ORDER BY a.id DESC
";
$affectations = $pdo->query($sqlList)->fetchAll(PDO::FETCH_ASSOC);

$groupesMap = [];
foreach ($groupesList as $g) {
    $gid = (int) $g['id'];
    $groupesMap[$gid] = $g['code'] ?? $g['nom'] ?? $g['libelle'] ?? ('Groupe #' . $gid);
}

$formateursMap = [];
foreach ($formateursList as $f) {
    $fid = (int) $f['id'];
    $nomComplet = trim(($f['nom'] ?? '') . ' ' . ($f['prenom'] ?? ''));
    $formateursMap[$fid] = $nomComplet !== '' ? $nomComplet : (($f['identifiant'] ?? '') . ' — ' . ($f['specialite'] ?? ''));
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Affectations - Centre Formation</title>
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
      <h1 class="page-title">Gestion des Affectations</h1>
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
          <input type="text" id="searchInput" placeholder="Rechercher par module, formateur ou groupe...">
        </div>

        <a href="#addAffectationModal" class="btn-primary">+ Nouvelle affectation</a>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Module</th>
              <th>Volume Horaire</th>
              <th>Formateur Affecté</th>
              <th>Groupe</th>
              <th>Année</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($affectations as $aff): ?>
              <?php
                $fid = (int) $aff['id_formateur'];
                $gid = (int) $aff['id_groupe'];
                $formLabel = $formateursMap[$fid] ?? ('Formateur #' . $fid);
                $grpLabel  = $groupesMap[$gid] ?? ('Groupe #' . $gid);
              ?>
              <tr>
                <td><strong>AFF-<?= htmlspecialchars((string) $aff['id']) ?></strong></td>
                <td><?= htmlspecialchars(($aff['module_code'] ?? '') . ' — ' . ($aff['module_intitule'] ?? '')) ?></td>
                <td><span class="badge-status active"><?= htmlspecialchars((string) ($aff['volume_horaire'] ?? '')) ?>h</span></td>
                <td><?= htmlspecialchars($formLabel) ?></td>
                <td><?= htmlspecialchars($grpLabel) ?></td>
                <td><?= htmlspecialchars((string) $aff['annee']) ?></td>
                <td>
                  <a href="?edit=<?= (int) $aff['id'] ?>#editAffectationModal" class="btn-edit">Modifier</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <div class="modal-backdrop" id="addAffectationModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Affecter un module à un formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
        <input type="hidden" name="action" value="ajouter">
        <div class="form-grid">
          <div class="form-group full-width">
            <label>Module</label>
            <select name="id_module" required>
              <option value="">-- Choisir un module --</option>
              <?php foreach ($modulesList as $m): ?>
                <option value="<?= (int) $m['id'] ?>">
                  <?= htmlspecialchars($m['code'] . ' — ' . $m['intitule'] . ' (' . $m['volume_horaire'] . 'h)') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Formateur</label>
            <select name="id_formateur" required>
              <option value="">-- Choisir un formateur --</option>
              <?php foreach ($formateursList as $f): ?>
                <?php
                  $fid = (int) $f['id'];
                  $fLabel = $formateursMap[$fid] ?? ('Formateur #' . $fid);
                ?>
                <option value="<?= $fid ?>">
                  <?= htmlspecialchars($fLabel) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Groupe</label>
            <select name="id_groupe" required>
              <option value="">-- Choisir un groupe --</option>
              <?php foreach ($groupesList as $g): ?>
                <?php
                  $gid = (int) $g['id'];
                  $gLabel = $groupesMap[$gid] ?? ('Groupe #' . $gid);
                ?>
                <option value="<?= $gid ?>">
                  <?= htmlspecialchars($gLabel) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group full-width">
            <label>Année</label>
            <input type="number" name="annee" value="<?= htmlspecialchars(date('Y')) ?>" placeholder="Ex: 2026" required>
          </div>
        </div>
        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Valider l'affectation</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal-backdrop" id="editAffectationModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier l'affectation</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form" method="POST">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id" value="<?= (int) ($affAEditer['id'] ?? 0) ?>">
        <div class="form-grid">
          <div class="form-group full-width">
            <label>Module</label>
            <select name="id_module" required>
              <?php foreach ($modulesList as $m): ?>
                <?php $sel = ((int) ($affAEditer['id_module'] ?? 0) === (int) $m['id']) ? 'selected' : ''; ?>
                <option value="<?= (int) $m['id'] ?>" <?= $sel ?>>
                  <?= htmlspecialchars($m['code'] . ' — ' . $m['intitule']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Formateur Affecté</label>
            <select name="id_formateur" required>
              <?php foreach ($formateursList as $f): ?>
                <?php
                  $fid = (int) $f['id'];
                  $fLabel = $formateursMap[$fid] ?? ('Formateur #' . $fid);
                  $sel = ((int) ($affAEditer['id_formateur'] ?? 0) === $fid) ? 'selected' : '';
                ?>
                <option value="<?= $fid ?>" <?= $sel ?>>
                  <?= htmlspecialchars($fLabel) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Groupe</label>
            <select name="id_groupe" required>
              <?php foreach ($groupesList as $g): ?>
                <?php
                  $gid = (int) $g['id'];
                  $gLabel = $groupesMap[$gid] ?? ('Groupe #' . $gid);
                  $sel = ((int) ($affAEditer['id_groupe'] ?? 0) === $gid) ? 'selected' : '';
                ?>
                <option value="<?= $gid ?>" <?= $sel ?>>
                  <?= htmlspecialchars($gLabel) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group full-width">
            <label>Année</label>
            <input type="number" name="annee" value="<?= htmlspecialchars((string) ($affAEditer['annee'] ?? date('Y'))) ?>" required>
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