<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Entradas;
use App\Models\Salidas;
use App\Models\Inventario;
use App\Support\Response;

final class KardexController
{
    private Entradas $entradas;
    private Salidas $salidas;
    private Inventario $inventario;

    public function __construct()
    {
        $this->entradas = new Entradas();
        $this->salidas = new Salidas();
        $this->inventario = new Inventario();
    }

    public function index(): Response
    {
        // Obtener todas las entradas
        $todasEntradas = $this->getAllEntradas();
        
        // Obtener todas las salidas
        $todasSalidas = $this->getAllSalidas();

        return Response::html(view('kardex/index', [
            'title' => 'Kardex General',
            'active' => 'kardex',
            'entradas' => $todasEntradas,
            'salidas' => $todasSalidas,
        ]));
    }

    private function getAllEntradas(): array
    {
        // Se asume que hay un método para obtener todas las entradas
        // De lo contrario, se puede hacer una consulta directa a la BD
        $db = \App\Support\Database::pdo();
        $stmt = $db->query("SELECT e.*, i.codigo, i.nombre, i.unidad FROM entradas e
                           LEFT JOIN inventario i ON e.inventario_id = i.id
                           ORDER BY e.created_at DESC");
        return $stmt->fetchAll() ?: [];
    }

    private function getAllSalidas(): array
    {
        // Se asume que hay un método para obtener todas las salidas
        $db = \App\Support\Database::pdo();
        $stmt = $db->query("SELECT s.*, i.codigo, i.nombre, i.unidad FROM salidas s
                           LEFT JOIN inventario i ON s.inventario_id = i.id
                           ORDER BY s.created_at DESC");
        return $stmt->fetchAll() ?: [];
    }
}
