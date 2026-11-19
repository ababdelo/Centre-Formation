<?php

class Affectation
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exist($gp_id, $id_module, $annee)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM affectation WHERE id_groupe = ? AND id_module = ? AND annee = ?"
        );
        $stmt->execute([$gp_id, $id_module, $annee]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function ajouter($group_id, $id_module, $id_formateur, $annee)
    {
        if ($this->exist($group_id, $id_module, $annee)) {
            throw new Exception("Ce module est déjà affecté à ce groupe pour cette année.");
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO affectation (id_groupe, id_module, id_formateur, annee) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$group_id, $id_module, $id_formateur, $annee]);
        return (int) $this->pdo->lastInsertId();
    }

    private function selectBase()
    {
        return "SELECT a.id, a.id_groupe, a.id_module, a.id_formateur, a.annee,
                       g.code AS groupe_code, g.nom AS groupe,
                       m.code AS module_code, m.intitule AS module, m.volume_horaire,
                       CONCAT(u.prenom, ' ', u.nom) AS formateur
                FROM affectation a
                JOIN groupe g      ON g.id = a.id_groupe
                JOIN module m      ON m.id = a.id_module
                JOIN formateur f   ON f.id = a.id_formateur
                JOIN utilisateur u ON u.id = f.id_utilisateur";
    }

    public function lister_Affectations($id_groupe = null, $id_formateur = null)
    {
        $conditions = [];
        $params = [];

        if ($id_groupe !== null) {
            $conditions[] = "a.id_groupe = ?";
            $params[] = $id_groupe;
        }
        if ($id_formateur !== null) {
            $conditions[] = "a.id_formateur = ?";
            $params[] = $id_formateur;
        }

        $sql = $this->selectBase();
        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY a.annee DESC, g.nom, m.code";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    
}