<?php

class Etudiant
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listerParGroupe($idGroupe)
    {
        $sql = "SELECT e.id, e.matricule, e.telephone, e.statut,
                       u.nom, u.prenom, u.email
                FROM etudiant e
                INNER JOIN utilisateur u ON u.id = e.id_utilisateur
                WHERE e.id_groupe = ?
                ORDER BY u.nom, u.prenom";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idGroupe]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listerSansGroupe()
    {
        $sql = "SELECT e.id, e.matricule, u.nom, u.prenom
                FROM etudiant e
                INNER JOIN utilisateur u ON u.id = e.id_utilisateur
                WHERE e.id_groupe IS NULL
                ORDER BY u.nom, u.prenom";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterAuGroupe($idEtudiant, $idGroupe)
    {
        $stmt = $this->pdo->prepare("UPDATE etudiant SET id_groupe = ? WHERE id = ?");
        $stmt->execute([$idGroupe, $idEtudiant]);
    }

    public function retirerDuGroupe($idEtudiant)
    {
        $stmt = $this->pdo->prepare("UPDATE etudiant SET id_groupe = NULL WHERE id = ?");
        $stmt->execute([$idEtudiant]);
    }
}
