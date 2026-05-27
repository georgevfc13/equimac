<?php
declare(strict_types=1);

namespace App\Support;

/**
 * Exporta tablas a Excel (SpreadsheetML) con estilos, sin dependencias externas.
 */
final class ExcelExport
{
    /** @param array<int, array<string, mixed>> $items */
    public static function inventario(array $items): void
    {
        $filename = 'inventario-' . date('Y-m-d') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $headers = [
            'Código',
            'Nombre',
            'Descripción',
            'Marca',
            'Equipo',
            'Unidad',
            'Cantidad',
            'Stock mínimo',
            'Estante',
            'Fila',
            'Posición',
            'Estado',
            'Creado',
            'Actualizado',
        ];

        $widths = [90, 140, 220, 100, 120, 70, 70, 90, 60, 50, 60, 80, 130, 130];

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";

        echo '<Styles>' . "\n";
        echo self::style('hdr', '#3B82F6', '#000000', true);
        echo self::style('body', '#F1F5F9', '#000000', false);
        echo self::style('bodyAlt', '#E2E8F0', '#000000', false);
        echo '</Styles>' . "\n";

        echo '<Worksheet ss:Name="Inventario">' . "\n";
        echo '<Table>' . "\n";

        foreach ($widths as $w) {
            echo '<Column ss:Width="' . (int)$w . '"/>' . "\n";
        }

        echo '<Row ss:StyleID="hdr" ss:Height="22">' . "\n";
        foreach ($headers as $h) {
            echo '<Cell><Data ss:Type="String">' . self::esc($h) . '</Data></Cell>' . "\n";
        }
        echo '</Row>' . "\n";

        $i = 0;
        foreach ($items as $p) {
            $style = ($i % 2 === 0) ? 'body' : 'bodyAlt';
            $i++;
            $row = [
                (string)($p['codigo'] ?? ''),
                (string)($p['nombre'] ?? ''),
                (string)($p['descripcion'] ?? ''),
                (string)($p['marca'] ?? ''),
                (string)($p['equipo'] ?? ''),
                (string)($p['unidad'] ?? ''),
                (string)($p['cantidad'] ?? ''),
                (string)($p['stock_minimo'] ?? ''),
                (string)($p['estante'] ?? ''),
                (string)($p['entrepaño'] ?? ''),
                (string)($p['posicion'] ?? ''),
                (string)($p['estado'] ?? ''),
                (string)($p['fecha_creacion'] ?? ''),
                (string)($p['fecha_actualizacion'] ?? ''),
            ];

            echo '<Row ss:StyleID="' . $style . '">' . "\n";
            foreach ($row as $cell) {
                echo '<Cell><Data ss:Type="String">' . self::esc($cell) . '</Data></Cell>' . "\n";
            }
            echo '</Row>' . "\n";
        }

        echo '</Table>' . "\n";
        echo '</Worksheet>' . "\n";
        echo '</Workbook>';
        exit;
    }

    private static function style(string $id, string $bg, string $font, bool $bold): string
    {
        $boldAttr = $bold ? ' ss:Bold="1"' : '';
        return '<Style ss:ID="' . $id . '">'
            . '<Alignment ss:Vertical="Center" ss:WrapText="1"/>'
            . '<Font ss:Color="' . $font . '" ss:Size="11"' . $boldAttr . '/>'
            . '<Interior ss:Color="' . $bg . '" ss:Pattern="Solid"/>'
            . '<Borders>'
            . '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'
            . '<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'
            . '<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'
            . '<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'
            . '</Borders>'
            . '</Style>' . "\n";
    }

    private static function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
