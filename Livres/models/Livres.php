<?php
declare(strict_types=1);

/**
 * Modèle métier représentant un livre du catalogue.
 *
 * Champs correspondants (ex. table QF_PHP.LIVRES) :
 *  - ID_LIVRE     → $id
 *  - TITRE        → $titre
 *  - AUTEUR       → $auteur
 *  - PRIX         → $prix
 *  - STOCK        → $stock
 *  - IMAGE_COUV   → $image (nom de fichier ou URL d'image, nullable)
 *
 * Invariants recommandés :
 *  - $id   ≥ 0
 *  - $prix ≥ 0.0
 *  - $stock ≥ 0
 */
class Livre
{
    /** @var int Identifiant unique du livre (clé primaire). */
    public int $id;

    /** @var string Titre du livre. */
    public string $titre;

    /** @var string Nom de l’auteur. */
    public string $auteur;

    /** @var float Prix TTC du livre. */
    public float $prix;

    /** @var int Quantité disponible en stock. */
    public int $stock;

    /**
     * @var string|null
     * Nom de fichier (ex. "le-capital-l1.jpg") ou URL absolue d'image.
     * Peut être null si aucune couverture n'est disponible.
     */
    public ?string $image = null;

    /**
     * Constructeur du modèle Livre.
     *
     * @param int         $id     Identifiant unique.
     * @param string      $titre  Titre du livre.
     * @param string      $auteur Auteur du livre.
     * @param float       $prix   Prix TTC.
     * @param int         $stock  Quantité en stock.
     * @param string|null $image  Nom de fichier ou URL de la couverture (optionnel).
     */
    public function __construct(
        int $id,
        string $titre,
        string $auteur,
        float $prix,
        int $stock,
        ?string $image = null
    ) {
        $this->id     = $id;
        $this->titre  = $titre;
        $this->auteur = $auteur;
        $this->prix   = $prix;
        $this->stock  = $stock;
        $this->image  = $image;
    }
}
