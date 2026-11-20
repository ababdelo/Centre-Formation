<?php

class Etudiant
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "
            SELECT
                e.id,
                e.id_utilisateur,
                u.nom,
                u.prenom,
                u.email,
                u.date_naissance,
                e.matricule,
                e.telephone,
                e.statut,
                e.id_groupe,
                g.code AS groupe_code,
                g.nom AS groupe_nom
            FROM etudiant e
            INNER JOIN utilisateur u ON u.id = e.id_utilisateur
            LEFT JOIN groupe g ON g.id = e.id_groupe
            WHERE u.role = 'ETUDIANT'
            ORDER BY u.nom, u.prenom
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?object
    {
        $sql = "
            SELECT
                e.id,
                e.id_utilisateur,
                u.nom,
                u.prenom,
                u.email,
                u.date_naissance,
                e.matricule,
                e.telephone,
                e.statut,
                e.id_groupe,
                g.code AS groupe_code,
                g.nom AS groupe_nom
            FROM etudiant e
            INNER JOIN utilisateur u ON u.id = e.id_utilisateur
            LEFT JOIN groupe g ON g.id = e.id_groupe
            WHERE e.id = :id
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $student = $statement->fetch();

        return $student === false ? null : $student;
    }

    public function findByMatricule(string $matricule): ?object
    {
        $sql = "
            SELECT
                e.id,
                e.id_utilisateur,
                u.nom,
                u.prenom,
                u.email,
                u.date_naissance,
                e.matricule,
                e.telephone,
                e.statut,
                e.id_groupe,
                g.code AS groupe_code,
                g.nom AS groupe_nom
            FROM etudiant e
            INNER JOIN utilisateur u ON u.id = e.id_utilisateur
            LEFT JOIN groupe g ON g.id = e.id_groupe
            WHERE e.matricule = :matricule
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['matricule' => $matricule]);

        $student = $statement->fetch();

        return $student === false ? null : $student;
    }

    public function create(array $data): int
    {
        $this->validate($data);

        $this->pdo->beginTransaction();

        try {
            $password = $data['mot_de_passe'] ?? bin2hex(random_bytes(8));

            $userSql = "
                INSERT INTO utilisateur
                    (nom, prenom, email, mot_de_passe, date_naissance, role)
                VALUES
                    (:nom, :prenom, :email, :mot_de_passe, :date_naissance, 'ETUDIANT')
            ";

            $userStatement = $this->pdo->prepare($userSql);
            $userStatement->execute([
                'nom' => trim($data['nom']),
                'prenom' => trim($data['prenom']),
                'email' => trim($data['email']),
                'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
                'date_naissance' => $data['date_naissance'],
            ]);

            $idUtilisateur = (int) $this->pdo->lastInsertId();

            $studentSql = "
                INSERT INTO etudiant
                    (id_utilisateur, matricule, telephone, statut, id_groupe)
                VALUES
                    (:id_utilisateur, :matricule, :telephone, :statut, :id_groupe)
            ";

            $studentStatement = $this->pdo->prepare($studentSql);
            $matricule = $this->generateMatricule($data['date_naissance']);

            $studentStatement->execute([
                'id_utilisateur' => $idUtilisateur,
                'matricule' => $matricule,
                'telephone' => trim($data['telephone']),
                'statut' => $data['statut'] ?? 'ACTIF',
                'id_groupe' => $this->nullableId($data['id_groupe'] ?? null),
            ]);

            $idEtudiant = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();

            return $idEtudiant;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function update(int $id, array $data): bool
    {
        $student = $this->findById($id);

        if ($student === null) {
            return false;
        }

        $this->validate($data, true);
        $this->pdo->beginTransaction();

        try {
            $userSql = "
                UPDATE utilisateur
                SET
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    date_naissance = :date_naissance
                WHERE id = :id
            ";

            $userStatement = $this->pdo->prepare($userSql);
            $userStatement->execute([
                'nom' => trim($data['nom']),
                'prenom' => trim($data['prenom']),
                'email' => trim($data['email']),
                'date_naissance' => $data['date_naissance'],
                'id' => $student->id_utilisateur,
            ]);

            $studentSql = "
                UPDATE etudiant
                SET
                    telephone = :telephone,
                    statut = :statut,
                    id_groupe = :id_groupe
                WHERE id = :id
            ";

            $studentStatement = $this->pdo->prepare($studentSql);
            $studentStatement->execute([
                'telephone' => trim($data['telephone']),
                'statut' => $data['statut'] ?? 'ACTIF',
                'id_groupe' => $this->nullableId($data['id_groupe'] ?? null),
                'id' => $id,
            ]);

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        $student = $this->findById($id);

        if ($student === null) {
            return false;
        }

        $statement = $this->pdo->prepare(
            'DELETE FROM utilisateur WHERE id = :id'
        );
        $statement->execute(['id' => $student->id_utilisateur]);

        return $statement->rowCount() > 0;
    }

    public function desactiver(int $id): bool
    {
        $student = $this->findById($id);

        if ($student === null) {
            return false;
        }

        if ($student->statut === 'SUSPENDU') {
            return true;
        }

        if ($student->statut !== 'ACTIF') {
            throw new InvalidArgumentException(
                'Seuls les étudiants actifs peuvent être désactivés.'
            );
        }

        $statement = $this->pdo->prepare(
            "UPDATE etudiant SET statut = 'SUSPENDU' WHERE id = :id AND statut = 'ACTIF'"
        );
        $statement->execute(['id' => $id]);

        if ($statement->rowCount() > 0) {
            return true;
        }

        $student = $this->findById($id);

        return $student !== null && $student->statut === 'SUSPENDU';
    }

    private function validate(array $data, bool $updating = false): void
    {
        $required = ['nom', 'prenom', 'email', 'date_naissance', 'telephone'];
        $labels = [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'adresse e-mail',
            'date_naissance' => 'date de naissance',
            'telephone' => 'téléphone',
        ];

        foreach ($required as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new InvalidArgumentException(
                    'Le champ ' . $labels[$field] . ' est obligatoire.'
                );
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Adresse email invalide.');
        }

        $allowedStatuses = ['ACTIF', 'SUSPENDU', 'ABANDONNE', 'TERMINE'];
        $status = $data['statut'] ?? 'ACTIF';

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException('Statut étudiant invalide.');
        }

        if (!$updating && isset($data['mot_de_passe']) && strlen($data['mot_de_passe']) < 8) {
            throw new InvalidArgumentException('Le mot de passe doit contenir au moins 8 caractères.');
        }
    }

    private function generateMatricule(string $dateNaissance): string
    {
        $birthDate = str_replace('-', '', $dateNaissance);

        do {
            $matricule = substr($birthDate, 0, 8) . random_int(10000, 99999);
            $statement = $this->pdo->prepare(
                'SELECT 1 FROM etudiant WHERE matricule = :matricule'
            );
            $statement->execute(['matricule' => $matricule]);
        } while ($statement->fetch() !== false);

        return $matricule;
    }

    private function nullableId(mixed $id): ?int
    {
        if ($id === null || $id === '') {
            return null;
        }

        return (int) $id;
    }
}
