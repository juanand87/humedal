<?php
// Script de migración para producción
// Ejecutar UNA SOLA VEZ y luego ELIMINAR este archivo
include 'config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Migración</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<style>body{padding:30px; font-family:monospace;}</style></head><body>";
echo "<h2>🔧 Ejecutando migración de tabla <code>slides</code></h2>";
echo "<pre>";

$columns_to_add = [
    ['name' => 'title_line2', 'def' => "VARCHAR(255) DEFAULT NULL"],
    ['name' => 'title_highlight', 'def' => "VARCHAR(255) DEFAULT NULL"],
    ['name' => 'feature1_title', 'def' => "VARCHAR(100) DEFAULT NULL"],
    ['name' => 'feature1_text', 'def' => "VARCHAR(255) DEFAULT NULL"],
    ['name' => 'feature1_icon', 'def' => "VARCHAR(50) DEFAULT 'fa-check'"],
    ['name' => 'feature2_title', 'def' => "VARCHAR(100) DEFAULT NULL"],
    ['name' => 'feature2_text', 'def' => "VARCHAR(255) DEFAULT NULL"],
    ['name' => 'feature2_icon', 'def' => "VARCHAR(50) DEFAULT 'fa-file-contract'"],
    ['name' => 'feature3_title', 'def' => "VARCHAR(100) DEFAULT NULL"],
    ['name' => 'feature3_text', 'def' => "VARCHAR(255) DEFAULT NULL"],
    ['name' => 'feature3_icon', 'def' => "VARCHAR(50) DEFAULT 'fa-user-friends'"],
    ['name' => 'button1_text', 'def' => "VARCHAR(100) DEFAULT NULL"],
    ['name' => 'button1_url', 'def' => "VARCHAR(500) DEFAULT NULL"],
    ['name' => 'button1_style', 'def' => "VARCHAR(50) DEFAULT 'success'"],
    ['name' => 'button2_text', 'def' => "VARCHAR(100) DEFAULT NULL"],
    ['name' => 'button2_url', 'def' => "VARCHAR(500) DEFAULT NULL"],
    ['name' => 'button2_style', 'def' => "VARCHAR(50) DEFAULT 'outline-light'"],
    ['name' => 'background_type', 'def' => "ENUM('color', 'image') DEFAULT 'color'"],
];

// Obtener columnas existentes
$existing = [];
try {
    $rows = $pdo->query("SHOW COLUMNS FROM slides")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $existing[] = $row['Field'];
    }
    echo "✓ Columnas existentes en 'slides': " . count($existing) . "\n\n";
} catch (Exception $e) {
    echo "✗ Error leyendo tabla: " . $e->getMessage() . "\n";
    exit;
}

$added = 0;
$skipped = 0;
foreach ($columns_to_add as $col) {
    if (in_array($col['name'], $existing)) {
        echo "  ⊘ Saltar (ya existe): {$col['name']}\n";
        $skipped++;
    } else {
        try {
            $sql = "ALTER TABLE slides ADD COLUMN `{$col['name']}` {$col['def']}";
            $pdo->exec($sql);
            echo "  ✓ Agregada: {$col['name']} {$col['def']}\n";
            $added++;
        } catch (Exception $e) {
            echo "  ✗ Error en {$col['name']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Resumen ===\n";
echo "Columnas agregadas: $added\n";
echo "Columnas omitidas (ya existían): $skipped\n";

// Mostrar estructura final
echo "\n=== Estructura final de la tabla 'slides' ===\n";
$final = $pdo->query("DESCRIBE slides")->fetchAll(PDO::FETCH_ASSOC);
foreach ($final as $col) {
    echo sprintf("  %-25s %s\n", $col['Field'], $col['Type']);
}

echo "\n✅ Migración completada.\n";
echo "<strong style='color:red'>⚠️ IMPORTANTE: ELIMINA este archivo (panel/migrate_columns.php) por seguridad.</strong>\n";
echo "</pre></body></html>";
