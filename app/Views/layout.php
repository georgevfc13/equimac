<?php
/** @var string $title */
/** @var string $content */
/** @var string $active */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($title ?? 'EQUIMAC') ?></title>
  <link rel="stylesheet" href="<?= e(url('assets/css/app.css')) ?>" />
</head>
<body>
  <div class="app-shell">
    <header class="topbar">
      <div class="container">
        <div class="topbar-inner">
          <a class="brand" href="<?= e(url('inventario')) ?>">
            <span class="logo" aria-hidden="true"></span>
            <span>
              <h1>EQUIMAC</h1>
              <small>Inventario local · UI vNext</small>
            </span>
          </a>
          <nav class="nav" aria-label="Principal">
            <a class="chip <?= ($active ?? '') === 'inventario' ? 'is-active' : '' ?>" href="<?= e(url('inventario')) ?>">Inventario</a>
            <a class="chip <?= ($active ?? '') === 'estantes' ? 'is-active' : '' ?>" href="<?= e(url('estantes')) ?>">Estantes</a>
            <a class="chip <?= ($active ?? '') === 'salida' ? 'is-active' : '' ?>" href="<?= e(url('salida')) ?>">Salida</a>
            <a class="chip" href="<?= e(url('inventario/nuevo')) ?>">+ Nuevo</a>
          </nav>
        </div>
      </div>
    </header>

    <main>
      <div class="container">
        <?= $content ?? '' ?>
      </div>
    </main>

    <footer>
      <div class="container">
        <div class="row" style="justify-content:space-between">
          <div>Localhost only · Sin auth · 2026</div>
          <div class="muted">Tip: usa búsqueda instantánea y acciones rápidas</div>
        </div>
      </div>
    </footer>
  </div>

  <script>
    window.__EQUIMAC_BASE__ = <?= json_encode(base_url(), JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="<?= e(url('assets/js/app.js')) ?>"></script>
</body>
</html>

