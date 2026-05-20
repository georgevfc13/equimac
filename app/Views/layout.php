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
            <img src="./public/assets/icons/logo.png" alt="" width="40" height="40" />
            <span>
              <h1>EQUIMAC</h1>
              <small>Manejo de inventario de la empresa Equimac</small>
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
          <div>Pagina local</div>
          <div class="muted">Para uso interno de Equimac</div>
        </div>
      </div>
    </footer>
  </div>

  <script>
    window.__EQUIMAC_BASE__ = <?= json_encode(base_url(), JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="<?= e(url('assets/js/app.js?v=' . time())) ?>"></script>
</body>
</html>

