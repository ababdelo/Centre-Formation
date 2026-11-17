<?php
class Affectation
{
    private  PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function exist($gp_id, $id_module, $ann)
    {
        $stmt = $this->pdo->prepare("select count(*) from affectation where id_groupe = ? and id_module = ? and annee = ?");
        $stmt->execute([$gp_id, $id_module, $ann]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function ajouter($gp_id, $id_mod, $id_form, $an)
    {
        if ($this->exist($gp_id, $id_mod, $an)) {
            throw new Exception("ce module est deja affectera ce groupe cette annees");
        }
        $stmt = $this->pdo->prepare("insert into affectation(id_groupe, id_module, id_formateur , annee) values(?,?,?,?)");
        $stmt->execute([$gp_id, $id_mod, $id_form, $an]);
    }
}
