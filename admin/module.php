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
      <a href="admin_dashboard.html" class="btn-back" title="Retour aux cartes">
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
      
      <!-- Toolbar: Rechercher + Créer -->
      <div class="toolbar">
        <div class="search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Rechercher un module...">
        </div>

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
              <th>Formateur</th>
              <th>Groupe</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>MOD-101</strong></td>
              <td>Développement Web Statique</td>
              <td>HTML5, CSS3, Flexbox, Grid et design responsive</td>
              <td><span class="badge-status active">60h</span></td>
              <td>Bennani Karim</td>
              <td>DEV-101</td>
              <td>
                <a href="#editModuleModal" class="btn-edit">Modifier</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>

  <!-- Modal Créer Module -->
  <div class="modal-backdrop" id="addModuleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Créer un module</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group">
            <label>Code</label>
            <input type="text" placeholder="Ex: MOD-104" required>
          </div>
          <div class="form-group">
            <label>Volume horaire</label>
            <input type="text" placeholder="Ex: 50h" required>
          </div>
          <div class="form-group full-width">
            <label>Intitulé</label>
            <input type="text" placeholder="Titre du module" required>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <input type="text" placeholder="Brève description du contenu" required>
          </div>
          <div class="form-group">
            <label>Formateur associé</label>
            <select>
              <option value="FRM-001">Bennani Karim</option>
              <option value="FRM-002">El Idrissi Salma</option>
              <option value="FRM-003">Tazi Youssef</option>
            </select>
          </div>
          <div class="form-group">
            <label>Groupe associé</label>
            <select>
              <option value="DEV-101">DEV-101</option>
              <option value="DEV-102">DEV-102</option>
              <option value="UIX-201">UIX-201</option>
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

  <!-- Modal Modifier Module -->
  <div class="modal-backdrop" id="editModuleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier le module</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group">
            <label>Code</label>
            <input type="text" value="MOD-101" readonly>
          </div>
          <div class="form-group">
            <label>Volume horaire</label>
            <input type="text" value="60h" required>
          </div>
          <div class="form-group full-width">
            <label>Intitulé</label>
            <input type="text" value="Développement Web Statique" required>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <input type="text" value="HTML5, CSS3, Flexbox, Grid et design responsive" required>
          </div>
          <div class="form-group">
            <label>Formateur associé</label>
            <select>
              <option value="FRM-001" selected>Bennani Karim</option>
              <option value="FRM-002">El Idrissi Salma</option>
              <option value="FRM-003">Tazi Youssef</option>
            </select>
          </div>
          <div class="form-group">
            <label>Groupe associé</label>
            <select>
              <option value="DEV-101" selected>DEV-101</option>
              <option value="DEV-102">DEV-102</option>
              <option value="UIX-201">UIX-201</option>
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