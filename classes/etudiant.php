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

}
