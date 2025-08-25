<?php
declare(strict_types=1);

/**
 * Accès DB2 sur IBM i via PDO (driver pdo_ibm) avec un mini-singleton.
 *
 * - Utilise le DSN `ibm:*LOCAL` pour cibler la base locale (entrée *LOCAL du WRKRDBDIRE).
 * - Si *LOCAL n’est pas résolu, remplace par le nom RDB réel (ex. `ibm:AGRION`).
 * - Pré-requis : extension PDO + pdo_ibm activées.
 *
 * Exemple d’usage :
 *   $pdo = DB::getConnection();
 *   $row = $pdo->query("SELECT CURRENT_DATE AS J FROM SYSIBM.SYSDUMMY1")->fetch();
 */
final class DB
{
    /**
     * Instance PDO partagée (pattern singleton).
     * @var \PDO|null
     */
    private static ?\PDO $instance = null;

    /**
     * Retourne une connexion PDO vers DB2 for i (unique pour le process).
     *
     * @return \PDO Connexion PDO prête à l’emploi (mode erreur = exceptions, fetch = assoc).
     *
     * @throws \PDOException Si l’ouverture de la connexion échoue (identifiants, DSN, etc.).
     *
     * Remarques IBM i :
     * - DSN recommandé : 'ibm:*LOCAL' ou 'ibm:<NOM_RDB>'.
     * - La gestion de l’UTF-8 dépend du CCSID du job Apache/PHP (CHGJOB CCSID 1208 au besoin).
     */
    public static function getConnection(): \PDO
    {
        if (self::$instance === null) {
            // DSN IBM i (pdo_ibm). Alternatives :
            //   $dsn = 'ibm:*LOCAL';            // utilise l’entrée *LOCAL
            //   $dsn = 'ibm:AGRION';           // nom RDB exact depuis WRKRDBDIRE
            $dsn  = 'ibm:*LOCAL';

            // Idem : mettez un profil technique avec autorisations minimales
            $user = ' '; // Exemple : Toto
            $pass = ' '; // Exemple : Motdepassetropfort

            self::$instance = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        }
        return self::$instance;
    }
}
