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
    public function estAffecte($id_module)
    {
        $stmt = $this->pdo->prepare("select count(*) from affectation where id_module = ?");
        $stmt->execute([$id_module]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function ModifieModule($id, $code, $intitule, $description, $VolHor)
    {
        if ($this->estAffecte($id)) {
            throw new Exception("Ce module est déjà affecté, il ne peut plus être modifié.");
        }

        $stmt = $this->pdo->prepare("update module set code= ? , intitule = ? ,description = ? , volume_horaire= ? where id = ?");
        $stmt->execute([$code, $intitule, $description, $VolHor, $id]);
    }
}
