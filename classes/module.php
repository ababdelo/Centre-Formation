<?php
class Module
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function lister()
    {
        $sql = ("select id, code, intitule, description,volume_horaire from module order by code");
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function codeExist($code, $excludedID = null)
    {
        $sql = "select count(*) from module where code = ?";
        $params = [$code];
        if ($excludedID !== null) {
            $sql .= " AND id <> ?";
            $params[] = $excludedID;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
    public function estAffecte($id_module)
    {
        $stmt = $this->pdo->prepare("select count(*) from affectation where id_module = ?");
        $stmt->execute([$id_module]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function ModifieModule($id, $code, $intitule, $description, $volumeHoraire)
    {
        if ($this->estAffecte($id)) {
            throw new Exception("Ce module est déjà affecté, il ne peut plus être modifié.");
        }
        if ($this->codeExist($code, $id)) {
            throw new Exception("Ce code de module est déjà utilisé.");
        }

        $stmt = $this->pdo->prepare("update module set code= ? , intitule = ? ,description = ? , volume_horaire= ? where id = ?");
        $stmt->execute([$code, $intitule, $description, $volumeHoraire, $id]);
    }

    public function ajouter($code, $intitule, $description, $volumeHoraire)
    {
        if ($this->codeExist($code)) {
            throw new Exception("Ce code de module est déjà utilisé.");
        }
        $stmt = $this->pdo->prepare("insert into module(code, intitule , description, volume_horaire) values(?,?,?,?)");
        $stmt->execute([$code, $intitule, $description, $volumeHoraire]);
        return (int) $this->pdo->lastInsertId();
    }

    public function trouver($id)
    {
        $stmt = $this->pdo->prepare("select id,code, intitule ,description , volume_horaire from module where id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
