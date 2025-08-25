<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bouq’iStore - Liste des livres</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    >
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="/Livres/assets/style.css">
</head>
<body>

<main class="container py-4">
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
    <h1 class="h3 mb-0">Notre catalogue</h1>

    <form class="ms-md-auto" method="get" action="index.php" role="search">
      <input type="hidden" name="action" value="livres">
      <div class="input-group input-group-sm">
        <span class="input-group-text">🔎</span>
        <input class="form-control" type="search" name="q"
               placeholder="Rechercher titre, auteur, ID…"
               value="<?= htmlspecialchars($q ?? '') ?>">
        <?php if (!empty($q ?? '')): ?>
          <a class="btn btn-outline-secondary" href="index.php?action=livres">Effacer</a>
        <?php endif; ?>
        <button class="btn btn-primary" type="submit">Rechercher</button>
      </div>
    </form>

    <div class="d-flex gap-2">
      <a class="btn btn-primary btn-sm" href="index.php?action=livre_create">Ajouter un livre</a>
      <span class="text-muted small align-self-center"><?= count($livres) ?> articles</span>
    </div>
  </div>

  <?php if (!empty($q ?? '')): ?>
    <p class="text-muted">Résultats pour « <strong><?= htmlspecialchars($q) ?></strong> »</p>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Titre</th>
          <th>Auteur</th>
          <th class="text-end">Prix</th>
          <th class="text-center">Stock</th>
          <th class="text-end" style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($livres as $livre): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($livre->titre) ?></td>
          <td><?= htmlspecialchars($livre->auteur) ?></td>
          <td class="text-end"><?= number_format((float)$livre->prix, 2, ',', ' ') ?> €</td>
          <td class="text-center">
            <?php $q = (int)$livre->stock; ?>
            <span class="badge <?= $q>0?'text-bg-success':'text-bg-danger' ?>"><?= $q>0?$q:'Rupture' ?></span>
          </td>
          <td class="text-end text-nowrap">
        <div class="d-inline-flex align-items-center gap-2">
          <a class="btn btn-sm btn-outline-secondary"
            href="index.php?action=livre_edit&id=<?= $livre->id ?>">Modifier</a>

          <form action="index.php?action=livre_delete" method="post" class="m-0">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="id" value="<?= $livre->id ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
          </form>
        </div>
</td>

        </tr>
      <?php endforeach; ?>
      <?php if (empty($livres)): ?>
  <tr>
    <td colspan="5" class="text-center text-muted py-4">Aucun résultat.</td>
  </tr>
<?php endif; ?>

      </tbody>
    </table>
  </div>
</main>


<footer class="bg-light border-top mt-5">
  <div class="container py-3">
    <small class="text-muted">© 2025 Bouq’iStore — hébergé sur IBM i</small>
    <small class="text-muted">| Langage - PHP 8.4</small>
  </div>
</footer>

<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
  crossorigin="anonymous"></script>
</body>
</html>
