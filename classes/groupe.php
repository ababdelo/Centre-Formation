<?php

class Groupe
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function ajouter($code, $nom, $filiere, $annee, $capacity, $statut)
    {
        $sql = "INSERT INTO groupe (code, nom, filiere, annee, capacity, statut)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$code, $nom, $filiere, $annee, $capacity, $statut]);
    }

    public function trouver($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM groupe WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifier($id, $code, $nom, $filiere, $annee, $capacity, $statut)
    {
        $sql = "UPDATE groupe
                SET code = ?, nom = ?, filiere = ?, annee = ?, capacity = ?, statut = ?
                WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$code, $nom, $filiere, $annee, $capacity, $statut, $id]);
    }

    public function lister()
    {
        $stmt = $this->pdo->query("SELECT * FROM groupe");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
