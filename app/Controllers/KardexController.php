<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Entradas;
use App\Models\Salidas;
use App\Support\Response;

final class KardexController
{
    private Entradas $entradas;
    private Salidas $salidas;

    public function __construct()
    {
        $this->entradas = new Entradas();
        $this->salidas = new Salidas();
    }

    public function index(): Response
    {
        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'orden' => trim((string)($_GET['orden'] ?? '')),
            'desde' => trim((string)($_GET['desde'] ?? '')),
            'hasta' => trim((string)($_GET['hasta'] ?? '')),
        ];
        $tipo = trim((string)($_GET['tipo'] ?? 'ambos'));
        if (!in_array($tipo, ['ambos', 'entradas', 'salidas'], true)) {
            $tipo = 'ambos';
        }

        $todasEntradas = ($tipo === 'salidas') ? [] : $this->entradas->searchAll($filters);
        $todasSalidas = ($tipo === 'entradas') ? [] : $this->salidas->searchAll($filters);

        return Response::html(view('kardex/index', [
            'title' => 'Kardex General',
            'active' => 'kardex',
            'entradas' => $todasEntradas,
            'salidas' => $todasSalidas,
            'filters' => $filters,
            'tipo' => $tipo,
        ]));
    }
}
