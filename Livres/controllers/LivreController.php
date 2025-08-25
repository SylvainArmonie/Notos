<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/LivresManager.php';
require_once __DIR__ . '/../models/Livres.php'; // classe Livre

/**
 * Contrôleur des livres (CRUD + recherche).
 *
 * Responsabilités :
 *  - Orchestrer les opérations du catalogue (liste, création, édition, suppression).
 *  - Déléguer l'accès aux données au {@see LivresManager}.
 *  - Préparer les données et appeler les vues appropriées.
 *
 * Remarques :
 *  - Nécessite une session démarrée en amont (index.php) pour le token CSRF.
 *  - Les redirections sont réalisées via {@see LivreController::redirect()}.
 */
final class LivreController
{
    /** Gestionnaire d'accès aux données. */
    private LivresManager $manager;

    /**
     * Constructeur : instancie le manager DAO.
     *
     * @throws \PDOException Si la connexion DB échoue dans le manager.
     */
    public function __construct()
    {
        $this->manager = new LivresManager();
    }

    /**
     * Liste des livres (avec recherche facultative).
     *
     * Lit le paramètre GET 'q' pour filtrer sur titre, auteur ou ID.
     * Passe $q à la vue pour le pré-remplissage du champ de recherche.
     *
     * @return void
     */
    public function index(): void
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';

        if ($q !== '') {
            $livres = $this->manager->search($q);
        } else {
            $livres = $this->manager->getAll();
        }

        require __DIR__ . '/../views/livres/index.php';
    }

    /**
     * Affiche le formulaire de création d'un livre.
     *
     * @return void
     */
    public function create(): void
    {
        $isEdit = false;
        $livre  = new Livre(0, '', '', 0.0, 0, null);
        require __DIR__ . '/../views/livres/form.php';
    }

    /**
     * Traite la soumission du formulaire de création (POST).
     *
     * @param array<string,mixed> $post Données POST.
     * @return void
     */
    public function store(array $post): void
    {
        $this->assertCsrf($post['csrf'] ?? '');
        [$titre, $auteur, $prix, $stock, $image] = $this->sanitize($post);

        $livre = new Livre(0, $titre, $auteur, $prix, $stock, $image);
        $this->manager->create($livre);

        $this->redirect('index.php?action=livres');
    }

    /**
     * Affiche le formulaire d'édition pour un livre existant.
     *
     * @param int $id Identifiant du livre à éditer.
     * @return void
     */
    public function edit(int $id): void
    {
        if ($id <= 0) {
            $this->redirect('index.php?action=livres');
        }

        $livre = $this->manager->find($id);
        if (!$livre) {
            $this->redirect('index.php?action=livres');
        }

        $isEdit = true;
        require __DIR__ . '/../views/livres/form.php';
    }

    /**
     * Traite la soumission du formulaire d'édition (POST).
     *
     * @param array<string,mixed> $post Données POST.
     * @return void
     */
    public function update(array $post): void
    {
        $this->assertCsrf($post['csrf'] ?? '');

        $id = (int) ($post['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('index.php?action=livres');
        }

        [$titre, $auteur, $prix, $stock, $image] = $this->sanitize($post);
        $livre = new Livre($id, $titre, $auteur, $prix, $stock, $image);

        $this->manager->update($livre);
        $this->redirect('index.php?action=livres');
    }

    /**
     * Supprime un livre (POST).
     *
     * @param array<string,mixed> $post Données POST (id, csrf).
     * @return void
     */
    public function destroy(array $post): void
    {
        $this->assertCsrf($post['csrf'] ?? '');

        $id = (int) ($post['id'] ?? 0);
        if ($id > 0) {
            $this->manager->delete($id);
        }

        $this->redirect('index.php?action=livres');
    }

    /* ======================== Helpers ======================== */

    /**
     * Redirige vers l'URL donnée et termine le script.
     *
     * @param string $url URL de destination.
     * @return never
     */
    private function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Valide le token CSRF (403 si invalide).
     *
     * @param string $token Jeton CSRF reçu.
     * @return void
     */
    private function assertCsrf(string $token): void
    {
        if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
            http_response_code(403);
            exit('CSRF token invalide');
        }
    }

    /**
     * Nettoie et valide les champs du formulaire.
     *
     * - Convertit le prix FR (virgule) en float (point).
     * - Force titres/auteurs non vides, prix/stock ≥ 0.
     * - Normalise l'image : chaîne vide => null.
     *
     * @param array<string,mixed> $data Données brutes du formulaire.
     * @return array{0:string,1:string,2:float,3:int,4:?string}
     */
    private function sanitize(array $data): array
    {
        $titre  = trim((string) ($data['titre'] ?? ''));
        $auteur = trim((string) ($data['auteur'] ?? ''));
        $prix   = (float) str_replace(',', '.', (string) ($data['prix'] ?? '0'));
        $stock  = (int) ($data['stock'] ?? 0);
        $image  = trim((string) ($data['image'] ?? ''));

        if ($titre === '' || $auteur === '' || $prix < 0 || $stock < 0) {
            http_response_code(422);
            exit('Champs invalides');
        }

        if ($image === '') {
            $image = null;
        }

        return [$titre, $auteur, $prix, $stock, $image];
    }
}
