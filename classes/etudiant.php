<?php

class Etudiant
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listerParGroupe(int $idGroupe): array
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

    public function listerSansGroupe(): array
    {
        $sql = "SELECT e.id, e.matricule, u.nom, u.prenom
                FROM etudiant e
                INNER JOIN utilisateur u ON u.id = e.id_utilisateur
                WHERE e.id_groupe IS NULL
                ORDER BY u.nom, u.prenom";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterAuGroupe(int $idEtudiant, int $idGroupe): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE etudiant SET id_groupe = ? WHERE id = ? AND id_groupe IS NULL"
        );
        $stmt->execute([$idGroupe, $idEtudiant]);

        return $stmt->rowCount() === 1;
    }

    public function retirerDuGroupe(int $idEtudiant): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE etudiant SET id_groupe = NULL WHERE id = ? AND id_groupe IS NOT NULL"
        );
        $stmt->execute([$idEtudiant]);

        return $stmt->rowCount() === 1;
    }
}
