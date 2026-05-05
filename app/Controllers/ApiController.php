<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inventario;

final class ApiController
{
    public function posicionesOcupadas(): void
    {
        $estante = (int)($_GET['estante'] ?? 0);
        $entrepaño = (int)($_GET['entrepaño'] ?? 0);
        if ($estante <= 0 || $entrepaño <= 0) {
            json(['ok' => false, 'message' => 'Parámetros inválidos'], 422);
        }

        $items = (new Inventario())->posicionesOcupadas($estante, $entrepaño);
        json(['ok' => true, 'items' => $items]);
    }

    public function buscarInventario(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $items = (new Inventario())->all($q);
        json(['ok' => true, 'items' => $items]);
    }

    public function eliminarInventario(): void
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) json(['ok' => false, 'message' => 'ID inválido'], 422);
        (new Inventario())->delete($id);
        json(['ok' => true]);
    }
}

