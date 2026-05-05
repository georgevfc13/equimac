<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Estantes;
use App\Models\Inventario;
use App\Support\Response;

final class EstantesController
{
    private Estantes $estantes;
    private Inventario $inv;

    public function __construct()
    {
        $this->estantes = new Estantes();
        $this->inv = new Inventario();
    }

    public function index(): Response
    {
        $flash = trim((string)($_GET['flash'] ?? ''));

        $estantes = $this->estantes->all();
        $items = $this->inv->all('');
        $map = [];
        foreach ($items as $p) {
            $e = (int)$p['estante'];
            $f = (int)$p['entrepaño'];
            $pos = (int)$p['posicion'];
            if ($e <= 0 || $f <= 0 || $pos <= 0) {
                continue;
            }
            $map[$e][$f][$pos] = $p;
        }

        return Response::html(view('estantes/index', [
            'title' => 'Estantes',
            'active' => 'estantes',
            'estantes' => $estantes,
            'map' => $map,
            'flash' => $flash,
            'form_errors' => [],
            'old_estante' => [],
        ]));
    }

    public function store(): void
    {
        $data = [
            'numero' => (int)($_POST['numero'] ?? 0),
            'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
            'ubicacion' => trim((string)($_POST['ubicacion'] ?? '')),
            'filas' => (int)($_POST['filas'] ?? 0),
            'columnas' => (int)($_POST['columnas'] ?? 0),
        ];

        $errors = $this->validateEstante($data);
        if ($errors) {
            $estantes = $this->estantes->all();
            $items = $this->inv->all('');
            $map = $this->buildMap($items);
            Response::html(view('estantes/index', [
                'title' => 'Estantes',
                'active' => 'estantes',
                'estantes' => $estantes,
                'map' => $map,
                'flash' => '',
                'form_errors' => $errors,
                'old_estante' => $data,
            ]), 422)->send();
        }

        $this->estantes->create($data);
        redirect('/estantes?flash=creado');
    }

    public function destroy(string $id): void
    {
        $eid = (int)$id;
        $row = $this->estantes->find($eid);
        if (!$row) {
            redirect('/estantes?flash=no_encontrado');
        }

        $numero = (int)$row['numero'];
        if ($this->estantes->countInventarioByEstanteNumero($numero) > 0) {
            redirect('/estantes?flash=ocupado');
        }

        $this->estantes->delete($eid);
        redirect('/estantes?flash=eliminado');
    }

    /** @param array<int, array<string, mixed>> $items */
    private function buildMap(array $items): array
    {
        $map = [];
        foreach ($items as $p) {
            $e = (int)$p['estante'];
            $f = (int)$p['entrepaño'];
            $pos = (int)$p['posicion'];
            if ($e <= 0 || $f <= 0 || $pos <= 0) {
                continue;
            }
            $map[$e][$f][$pos] = $p;
        }
        return $map;
    }

    private function validateEstante(array $data): array
    {
        $errors = [];
        if ($data['numero'] <= 0) {
            $errors['numero'] = 'Escribe un número de estante válido (mayor que cero).';
        } elseif ($this->estantes->numeroExists($data['numero'])) {
            $errors['numero'] = 'Ya existe un estante con ese número.';
        }
        if ($data['filas'] < 1 || $data['filas'] > 20) {
            $errors['filas'] = 'Las filas deben estar entre 1 y 20.';
        }
        if ($data['columnas'] < 1 || $data['columnas'] > 20) {
            $errors['columnas'] = 'Las posiciones por fila deben estar entre 1 y 20.';
        }
        return $errors;
    }
}

