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
      <h1 class="page-title">Gestion des Affectations</h1>
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


  <main class="main-container">
    <div class="glass-panel">
      
      <!-- here goes the php code -->
      <div class="toolbar">
        <div class="search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Rechercher par module, formateur ou groupe...">
        </div>

        <a href="#addAffectationModal" class="btn-primary">+ Nouvelle affectation</a>
      </div>

      <!-- list of every affectation -->
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Module</th>
              <th>Volume Horaire</th>
              <th>Formateur Affecté</th>
              <th>Groupe</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>AFF-001</strong></td>
              <td>MOD-101 — Développement Web Statique</td>
              <td>60h</td>
              <td>Bennani Karim</td>
              <td>DEV-101</td>
              <td><span class="badge-status active">Assigné</span></td>
              <td>
                <a href="#editAffectationModal" class="btn-edit">Modifier</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>
<!-- for modify this goes in a php page -->
  <div class="modal-backdrop" id="addAffectationModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Affecter un module à un formateur</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group full-width">
            <label>Sélectionner le Module</label>
            <select required>
              <option value="">-- Choisir un module --</option>
              <option value="MOD-101">MOD-101 — Développement Web Statique (60h)</option>
              <option value="MOD-102">MOD-102 — UI/UX Design &amp; Figma (40h)</option>
              <option value="MOD-103">MOD-103 — Bases de Données &amp; SQL (45h)</option>
              <option value="MOD-104">MOD-104 — Programmation Orientée Objet Python (50h)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Associer un Groupe</label>
            <select required>
              <option value="DEV-101">DEV-101</option>
              <option value="DEV-102">DEV-102</option>
              <option value="UIX-201">UIX-201</option>
            </select>
          </div>

          <div class="form-group">
            <label>Statut</label>
            <select>
              <option value="Assigné">Assigné</option>
              <option value="En attente">En attente</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <a href="#" class="btn-cancel">Annuler</a>
          <button type="submit" class="btn-primary">Valider l'affectation</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Modifier Affectation -->
  <div class="modal-backdrop" id="editAffectationModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2>Modifier l'affectation</h2>
        <a href="#" class="close-btn">&times;</a>
      </div>
      <form class="modal-form">
        <div class="form-grid">
          <div class="form-group full-width">
            <label>Module</label>
            <select>
              <option value="MOD-101" selected>MOD-101 — Développement Web Statique (60h)</option>
              <option value="MOD-102">MOD-102 — UI/UX Design &amp; Figma (40h)</option>
              <option value="MOD-103">MOD-103 — Bases de Données &amp; SQL (45h)</option>
            </select>
          </div>

          <div class="form-group full-width">
            <label>Formateur Affecté</label>
            <select>
              <option value="FRM-001" selected>FRM-001 — Bennani Karim</option>
              <option value="FRM-002">FRM-002 — El Idrissi Salma</option>
              <option value="FRM-003">FRM-003 — Tazi Youssef</option>
            </select>
          </div>

          <div class="form-group">
            <label>Groupe</label>
            <select>
              <option value="DEV-101" selected>DEV-101</option>
              <option value="DEV-102">DEV-102</option>
              <option value="UIX-201">UIX-201</option>
            </select>
          </div>

          <div class="form-group">
            <label>Statut</label>
            <select>
              <option value="Assigné" selected>Assigné</option>
              <option value="En attente">En attente</option>
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