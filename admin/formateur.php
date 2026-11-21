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

  <!-- Header -->
  <header class="top-header">
    <div class="header-left">
      <a href="admin_dashboard.html" class="btn-back" title="Retour aux cartes">
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
          <span class="admin-name">Admin Principal</span>
          <span class="admin-role">Administrateur</span>
        </div>
      </div>
      <a href="index.html" class="btn-logout">Déconnexion</a>
    </div>
  </header>

  <!-- Main Container -->
  <main class="main-container">
    <div class="glass-panel">
      
      <!-- Toolbar: Rechercher + Ajouter -->
      <div class="toolbar">
        <div class="search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Rechercher un formateur...">
        </div>

        <a href="#addModal" class="btn-primary">+ Ajouter un formateur</a>
      </div>

      <!-- Liste des Formateurs -->
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
            <tr>
              <td><strong>FRM-001</strong></td>
              <td>Bennani</td>
              <td>Karim</td>
              <td>k.bennani@centre.ma</td>
              <td>Développement Full Stack</td>
              <td><span class="badge-status active">Actif</span></td>
              <td>
                <a href="#editModal" class="btn-edit">Modifier</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- for adding Formateur -->
  <div class="modal-backdrop" id="addModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Ajouter un formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group">
            <label>Identifiant</label>
            <input type="text" placeholder="Ex: FRM-004" required>
          </div>
          <div class="form-group">
            <label>Spécialité</label>
            <input type="text" placeholder="Ex: Python / Web" required>
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" placeholder="Nom du formateur" required>
          </div>
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" placeholder="Prénom du formateur" required>
          </div>
          <div class="form-group full-width">
            <label>Email</label>
            <input type="email" placeholder="email@centre.ma" required>
          </div>
          <div class="form-group full-width">
            <label>Statut</label>
            <select>
              <option value="Actif">Actif</option>
              <option value="Inactif">Inactif</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- for modifiy formateur -->
  <div class="modal-backdrop" id="editModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier le formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group">
            <label>Identifiant</label>
            <input type="text" value="FRM-001" readonly>
          </div>
          <div class="form-group">
            <label>Spécialité</label>
            <input type="text" value="Développement Full Stack" required>
          </div>
          <div class="form-group">
            <label>Nom</label>
            <input type="text" value="Bennani" required>
          </div>
          <div class="form-group">
            <label>Prénom</label>
            <input type="text" value="Karim" required>
          </div>
          <div class="form-group full-width">
            <label>Email</label>
            <input type="email" value="k.bennani@centre.ma" required>
          </div>
          <div class="form-group full-width">
            <label>Statut</label>
            <select>
              <option value="Actif">Actif</option>
              <option value="Inactif">Inactif</option>
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

</body>
</html>