<?php

$file = 'resources/views/admin/dashboard.blade.php';
$content = file_get_contents($file);

// Buscar la card de Productos y reemplazar el span.module-status
// El patrón: después de <h4>Productos</h4> y <p>Administra..., hay un span.module-status con "Próximamente"
$pattern = '/(<h4>Productos<\/h4>\s*<p>Administra\nproductos, precios y catálogo.<\/p>\s*<span class="module-status">)(Próximamente)(<\/span>)/';

$replacement = '$1<a href="'.route('admin.productos.index').'" class="module-status">Ver productos</a>$3';

$new_content = preg_replace($pattern, $replacement, $content);

if ($new_content !== $content) {
    file_put_contents($file, $new_content);
    echo "Reemplazo realizado correctamente.";
} else {
    echo "No se encontró el patrón para reemplazar.";
}

?>