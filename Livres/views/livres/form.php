<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un livre</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous">
  <link rel="stylesheet" href="/Livres/assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark navbar-glass">
  <div class="container">
    <a class="navbar-brand" href="index.php">Bouq’iStore</a>
  </div>
</nav>

<main class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <h1 class="h5 mb-0"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un livre</h1>
        </div>
        <div class="card-body">
          <form method="post" action="index.php?action=<?= $isEdit ? 'livre_update' : 'livre_store' ?>">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <?php if ($isEdit): ?>
              <input type="hidden" name="id" value="<?= (int)$livre->id ?>">
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Titre</label>
              <input type="text" name="titre" class="form-control" required
                     value="<?= htmlspecialchars($livre->titre) ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Auteur</label>
              <input type="text" name="auteur" class="form-control" required
                     value="<?= htmlspecialchars($livre->auteur) ?>">
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Prix (€)</label>
                <input type="text" name="prix" class="form-control" inputmode="decimal" required
                       value="<?= htmlspecialchars(number_format((float)$livre->prix, 2, ',', '')) ?>">
                <div class="form-text">Utilise la virgule (ex: 9,90).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" min="0" step="1" required
                       value="<?= (int)$livre->stock ?>">
              </div>
            </div>

            <div class="mb-3 mt-3">
              <label class="form-label">Image (nom de fichier ou URL)</label>
              <input type="text" name="image" class="form-control"
                     placeholder="ex: le-capital-l1.jpg ou https://…"
                     value="<?= htmlspecialchars((string)$livre->image) ?>">
              <div class="form-text">
                Pour un fichier local, place l’image dans <code>/Livres/assets/img/livres/</code>.
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Enregistrer' : 'Ajouter' ?></button>
              <a href="index.php?action=livres" class="btn btn-outline-secondary">Annuler</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="bg-light border-top mt-5">
  <div class="container py-3">
    <small class="text-muted">© 2025 Bouq’iStore — hébergé sur IBM i</small>
  </div>
</footer>
</body>
</html>

