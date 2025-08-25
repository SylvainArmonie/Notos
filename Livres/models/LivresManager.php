<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Livres.php';

/**
 * DAO/Repository pour la table QF_PHP.LIVRES (DB2 for i).
 *
 * - Fournit les opérations CRUD et la recherche.
 * - Retourne des objets métier {@see Livre}.
 * - S'appuie sur PDO en ERRMODE_EXCEPTION (cf. DB::getConnection()).
 */
class LivresManager
{
    /** Connexion PDO vers DB2 for i. */
    private \PDO $db;

    /**
     * Constructeur : récupère la connexion partagée.
     *
     * @throws \PDOException Si l'obtention de la connexion échoue.
     */
    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    /**
     * Retourne l’ensemble des livres triés par titre.
     *
     * @return Livre[] Liste d’objets Livre.
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function getAll(): array
    {
        $sql = "SELECT ID_LIVRE, TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV
                  FROM QF_PHP.LIVRES
                 ORDER BY TITRE";

        $stmt = $this->db->query($sql);

        /** @var Livre[] $list */
        $list = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    /**
     * Récupère un livre par son identifiant.
     *
     * @param int $id Identifiant du livre.
     * @return Livre|null Le livre trouvé, sinon null.
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function find(int $id): ?Livre
    {
        $sql = "SELECT ID_LIVRE, TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV
                  FROM QF_PHP.LIVRES
                 WHERE ID_LIVRE = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    /**
     * Crée un livre.
     *
     * Si ID_LIVRE est une colonne IDENTITY, l’ID est récupéré via IDENTITY_VAL_LOCAL().
     *
     * @param Livre $l Livre à créer.
     * @return int ID généré.
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function create(Livre $l): int
    {
        $sql = "INSERT INTO QF_PHP.LIVRES (TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$l->titre, $l->auteur, $l->prix, $l->stock, $l->image]);

        // DB2 for i : IDENTITY_VAL_LOCAL() renvoie la dernière valeur IDENTITY du job.
        $id = (int) $this->db
            ->query("SELECT IDENTITY_VAL_LOCAL() AS ID FROM SYSIBM.SYSDUMMY1")
            ->fetch()['ID'];

        return $id;
    }

    /**
     * Met à jour un livre existant.
     *
     * @param Livre $l Livre avec ID existant.
     * @return void
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function update(Livre $l): void
    {
        $sql = "UPDATE QF_PHP.LIVRES
                   SET TITRE = ?, AUTEUR = ?, PRIX = ?, STOCK = ?, IMAGE_COUV = ?
                 WHERE ID_LIVRE = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$l->titre, $l->auteur, $l->prix, $l->stock, $l->image, $l->id]);
    }

    /**
     * Supprime un livre par son identifiant.
     *
     * @param int $id Identifiant du livre à supprimer.
     * @return void
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function delete(int $id): void
    {
        $sql = "DELETE FROM QF_PHP.LIVRES WHERE ID_LIVRE = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    /**
     * Recherche libre sur titre, auteur ou ID (contient, insensible à la casse).
     *
     * @param string $q Terme recherché.
     * @return Livre[] Résultats correspondants.
     * @throws \PDOException En cas d’erreur SQL.
     */
    public function search(string $q): array
    {
        // Échapper % et _ pour LIKE, et autoriser l’ESCAPE '\'
        $like = '%' . strtr($q, [
            '\\' => '\\\\',
            '%'  => '\%',
            '_'  => '\_',
        ]) . '%';

        $sql = "SELECT ID_LIVRE, TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV
                  FROM QF_PHP.LIVRES
                 WHERE UPPER(TITRE)  LIKE UPPER(?) ESCAPE '\\'
                    OR UPPER(AUTEUR) LIKE UPPER(?) ESCAPE '\\'
                    OR CHAR(ID_LIVRE) LIKE ?
                 ORDER BY TITRE";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like, $like]);

        /** @var Livre[] $list */
        $list = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $list[] = $this->hydrate($row);
        }
        return $list;
    }

    /**
     * Hydrate un objet Livre depuis une ligne SQL.
     *
     * @param array<string,mixed> $row Ligne SQL (colonnes attendues : ID_LIVRE, TITRE, AUTEUR, PRIX, STOCK, IMAGE_COUV).
     * @return Livre Objet métier hydraté.
     */
    private function hydrate(array $row): Livre
    {
        return new Livre(
            (int) $row['ID_LIVRE'],
            (string) $row['TITRE'],
            (string) $row['AUTEUR'],
            (float) $row['PRIX'],
            (int) $row['STOCK'],
            $row['IMAGE_COUV'] !== null ? (string) $row['IMAGE_COUV'] : null
        );
    }
}
