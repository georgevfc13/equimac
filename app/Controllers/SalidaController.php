<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inventario;
use App\Models\OrdenesSalida;
use App\Models\Salidas;
use App\Support\Database;
use App\Support\OrdenHelper;
use App\Support\Response;

final class SalidaController
{
    private Inventario $inv;

    public function __construct()
    {
        $this->inv = new Inventario();
    }

    public function form(): Response
    {
        return Response::html(view('salida/form', [
            'title' => 'Salida de material',
            'active' => 'salida',
            'errors' => [],
            'old' => $this->defaultOld(),
            'ordenLabel' => null,
        ]));
    }

    public function store(): void
    {
        $old = $this->parseOldFromPost();
        $lines = $this->parseLinesFromPost();
        $old['lines'] = $lines;

        $errors = $this->validateHeader($old);
        $lineErrors = $this->validateLines($lines);
        $errors = array_merge($errors, $lineErrors);

        if ($errors) {
            Response::html(view('salida/form', [
                'title' => 'Salida de material',
                'active' => 'salida',
                'errors' => $errors,
                'old' => $old,
                'ordenLabel' => null,
            ]), 422)->send();
        }

        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $ordenSalidaId = null;
            $ordenLabel = null;
            if (OrdenHelper::schemaReady()) {
                $orden = (new OrdenesSalida())->create([
                    'quien_recibio' => $old['quien_recibio'],
                    'quien_entrego' => $old['quien_entrego'],
                    'observaciones' => $old['observaciones'],
                    'fecha_salida' => $old['fecha_salida'],
                    'hora_salida' => $old['hora_salida'],
                ]);
                $ordenSalidaId = $orden['id'];
                $ordenLabel = $orden['label'];
            }

            $salidas = new Salidas();
            foreach ($lines as $line) {
                $producto = $this->inv->findByCodigo($line['codigo']);
                if (!$producto) {
                    throw new \RuntimeException('Producto no encontrado: ' . $line['codigo']);
                }
                if (!$this->inv->decrementCantidad((int)$producto['id'], $line['cantidad'])) {
                    throw new \RuntimeException('Stock insuficiente para ' . $line['codigo']);
                }
                $salidas->create(
                    (int)$producto['id'],
                    $line['codigo'],
                    $old['quien_recibio'],
                    $old['quien_entrego'],
                    $line['cantidad'],
                    $old['fecha_salida'] ?: null,
                    $old['hora_salida'] ?: null,
                    $old['observaciones'] ?: null,
                    $ordenSalidaId
                );
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            $errors['general'] = $e->getMessage();
            Response::html(view('salida/form', [
                'title' => 'Salida de material',
                'active' => 'salida',
                'errors' => $errors,
                'old' => $old,
                'ordenLabel' => null,
            ]), 422)->send();
        }

        $params = ['ok' => '1'];
        if ($ordenLabel) {
            $params['orden'] = $ordenLabel;
        }
        redirect('/salida?' . http_build_query($params));
    }

    /** @return array<string, mixed> */
    private function defaultOld(): array
    {
        return [
            'quien_recibio' => '',
            'quien_entrego' => '',
            'fecha_salida' => date('Y-m-d'),
            'hora_salida' => date('H:i'),
            'observaciones' => '',
            'lines' => [['codigo' => '', 'cantidad_usada' => '1']],
        ];
    }

    /** @return array<string, mixed> */
    private function parseOldFromPost(): array
    {
        return [
            'quien_recibio' => trim((string)($_POST['quien_recibio'] ?? '')),
            'quien_entrego' => trim((string)($_POST['quien_entrego'] ?? '')),
            'fecha_salida' => trim((string)($_POST['fecha_salida'] ?? '')),
            'hora_salida' => trim((string)($_POST['hora_salida'] ?? '')),
            'observaciones' => trim((string)($_POST['observaciones'] ?? '')),
        ];
    }

    /** @return list<array{codigo:string,cantidad:int}> */
    private function parseLinesFromPost(): array
    {
        $codigos = $_POST['line_codigo'] ?? [];
        $cantidades = $_POST['line_cantidad'] ?? [];
        if (!is_array($codigos)) {
            return [];
        }
        $lines = [];
        foreach ($codigos as $i => $codigo) {
            $codigo = trim((string)$codigo);
            if ($codigo === '') {
                continue;
            }
            $lines[] = [
                'codigo' => $codigo,
                'cantidad' => (int)($cantidades[$i] ?? 0),
            ];
        }
        return $lines;
    }

    /** @param array<string, mixed> $old */
    private function validateHeader(array $old): array
    {
        $errors = [];
        if ($old['quien_recibio'] === '') {
            $errors['quien_recibio'] = 'Indica quién recibió el material.';
        }
        if ($old['quien_entrego'] === '') {
            $errors['quien_entrego'] = 'Indica quién entregó el material.';
        }
        if ($old['fecha_salida'] === '') {
            $errors['fecha_salida'] = 'Indica la fecha de salida.';
        }
        if ($old['hora_salida'] === '') {
            $errors['hora_salida'] = 'Indica la hora de salida.';
        }
        return $errors;
    }

    /** @param list<array{codigo:string,cantidad:int}> $lines */
    private function validateLines(array $lines): array
    {
        $errors = [];
        if (!$lines) {
            $errors['lines'] = 'Agrega al menos un producto a la salida.';
            return $errors;
        }
        foreach ($lines as $i => $line) {
            $producto = $this->inv->findByCodigo($line['codigo']);
            if (!$producto) {
                $errors["line_{$i}"] = 'No existe producto con código «' . $line['codigo'] . '».';
                continue;
            }
            if ($line['cantidad'] <= 0) {
                $errors["line_{$i}"] = 'Cantidad inválida para «' . $line['codigo'] . '».';
                continue;
            }
            if ($line['cantidad'] > (int)$producto['cantidad']) {
                $errors["line_{$i}"] = 'Stock insuficiente para «' . $line['codigo'] . '». Disponible: '
                    . (int)$producto['cantidad'] . ' ' . $producto['unidad'] . '.';
            }
        }
        return $errors;
    }
}
