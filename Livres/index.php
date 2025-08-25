<?php
declare(strict_types=1);

/**
 * Front controller de Bouq’iStore.
 *
 * Responsabilités :
 *  - Initialiser le contexte (affichage des erreurs, session, CSRF).
 *  - Router les requêtes vers le LivreController selon le paramètre ?action=...
 *
 * Remarques :
 *  - Les actions d’écriture (create/store/update/destroy) passent par des requêtes POST.
 *  - Le token CSRF est stocké en session et doit être transmis par les formulaires.
 *  - Fichier conçu pour IBM i (DB2) avec PHP 8+.
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// --- Session + CSRF ---------------------------------------------------------
session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

/**
 * Exige la méthode POST pour une action donnée.
 * Renvoie 405 si la méthode n’est pas autorisée.
 *
 * @return void
 */
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405); // Method Not Allowed
        header('Allow: POST');
        exit('Méthode non autorisée (POST requis).');
    }
}

// --- Dispatch ---------------------------------------------------------------
require_once __DIR__ . '/controllers/LivreController.php';

$controller = new LivreController();
/** @var string $action Action demandée (par défaut : liste des livres). */
$action = isset($_GET['action']) ? (string) $_GET['action'] : 'livres';

// Router minimal
switch ($action) {
    case 'livres':              // GET : liste (+ recherche)
        $controller->index();
        break;

    case 'livre_create':        // GET : formulaire de création
        $controller->create();
        break;

    case 'livre_store':         // POST : création
        require_post();
        $controller->store($_POST);
        break;

    case 'livre_edit':          // GET : formulaire d’édition
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $controller->edit($id);
        break;

    case 'livre_update':        // POST : mise à jour
        require_post();
        $controller->update($_POST);
        break;

    case 'livre_delete':        // POST : suppression
        require_post();
        $controller->destroy($_POST);
        break;

    default:                    // 404 si action inconnue
        http_response_code(404);
        echo 'Action inconnue.';
}
