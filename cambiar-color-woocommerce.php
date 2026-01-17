<?php
/**
 * Script para cambiar el color primario de WooCommerce
 * De #96598A a #50c1c1
 * 
 * INSTRUCCIONES:
 * 1. Sube este archivo a la raíz de tu sitio
 * 2. Accede a: https://tu-sitio.com/cambiar-color-woocommerce.php
 * 3. Verás un resumen de todos los cambios realizados
 * 4. ELIMINA este archivo después de ejecutarlo
 */

// Cargar WordPress
require_once(dirname(__FILE__) . '/wp-load.php');

// Solo permitir a administradores
if (!current_user_can('manage_options')) {
    die('Acceso denegado. Debes ser administrador.');
}

// Colores
$old_color = '#96598a';
$new_color = '#50c1c1';
$new_color_dark = '#3da8a8'; // Versión oscura para hover/gradientes

$cambios = [];

// PRIMERO: Actualizar las variables CSS en ctc-style.css
$css_file_path = dirname(__FILE__) . '/wp-content/themes/astra-child-camisetas/ctc-style.css';
if (file_exists($css_file_path)) {
    $css_content = file_get_contents($css_file_path);
    $original_css = $css_content;
    
    // Actualizar variables CSS si existen
    $css_content = preg_replace(
        '/--secondary-color:\s*#[0-9a-fA-F]{6};/',
        '--secondary-color: ' . $new_color . ';',
        $css_content
    );
    $css_content = preg_replace(
        '/--secondary-color-dark:\s*#[0-9a-fA-F]{6};/',
        '--secondary-color-dark: ' . $new_color_dark . ';',
        $css_content
    );
    
    if ($css_content !== $original_css) {
        file_put_contents($css_file_path, $css_content);
        $cambios[] = "✅ Actualizado: Variables CSS en <code>ctc-style.css</code> (--secondary-color y --secondary-color-dark)";
    }
}

$cambios = [];

echo '<html><head><meta charset="UTF-8"><title>Cambio de Color WooCommerce</title>';
echo '<style>
body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 40px; background: #f5f5f5; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h1 { color: #333; border-bottom: 3px solid #50c1c1; padding-bottom: 15px; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }
.warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
.color-demo { display: inline-block; width: 30px; height: 30px; border-radius: 4px; vertical-align: middle; margin: 0 10px; border: 2px solid #ddd; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #f8f9fa; font-weight: 600; }
tr:hover { background: #f8f9fa; }
code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
.btn { display: inline-block; padding: 12px 24px; background: #50c1c1; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
.btn:hover { background: #3da8a8; }
</style></head><body>';

echo '<div class="container">';
echo '<h1>🎨 Cambio de Color WooCommerce</h1>';

echo '<div class="info">';
echo '<strong>🔄 Cambiando:</strong> ';
echo '<span class="color-demo" style="background: ' . $old_color . ';"></span>';
echo '<code>' . strtoupper($old_color) . '</code> → ';
echo '<span class="color-demo" style="background: ' . $new_color . ';"></span>';
echo '<code>' . strtoupper($new_color) . '</code>';
echo '</div>';

// 1. Opciones de Astra Theme
$astra_options = [
    'astra-settings' => 'Configuración principal de Astra'
];

foreach ($astra_options as $option_name => $description) {
    $option_value = get_option($option_name);
    if ($option_value && is_array($option_value)) {
        $changed = false;
        array_walk_recursive($option_value, function(&$value) use ($old_color, $new_color, &$changed) {
            if (is_string($value) && stripos($value, $old_color) !== false) {
                $value = str_ireplace($old_color, $new_color, $value);
                $changed = true;
            }
        });
        
        if ($changed) {
            update_option($option_name, $option_value);
            $cambios[] = "✅ Actualizado: $description (<code>$option_name</code>)";
        }
    }
}

// 2. Theme Mods (Customizer)
$theme_mods = get_theme_mods();
if ($theme_mods && is_array($theme_mods)) {
    $changed = false;
    $updated_mods = [];
    
    foreach ($theme_mods as $key => $value) {
        if (is_string($value) && stripos($value, $old_color) !== false) {
            $updated_mods[$key] = [
                'old' => $value,
                'new' => str_ireplace($old_color, $new_color, $value)
            ];
            set_theme_mod($key, $updated_mods[$key]['new']);
            $changed = true;
        }
    }
    
    if ($changed) {
        $cambios[] = "✅ Actualizado: Theme Mods del Customizer";
        foreach ($updated_mods as $key => $values) {
            $cambios[] = "  └─ <code>$key</code>: " . htmlspecialchars($values['old']) . ' → ' . htmlspecialchars($values['new']);
        }
    }
}

// 3. Custom CSS del tema
$custom_css = wp_get_custom_css();
if ($custom_css && stripos($custom_css, $old_color) !== false) {
    $new_css = str_ireplace($old_color, $new_color, $custom_css);
    wp_update_custom_css_post($new_css);
    $cambios[] = "✅ Actualizado: Custom CSS del tema";
}

// 4. Opciones de WooCommerce
$wc_options = [
    'woocommerce_primary_color',
    'woocommerce_secondary_color',
    'woocommerce_highlight_color',
    'woocommerce_content_bg_color',
];

foreach ($wc_options as $option) {
    $value = get_option($option);
    if ($value && stripos($value, $old_color) !== false) {
        $new_value = str_ireplace($old_color, $new_color, $value);
        update_option($option, $new_value);
        $cambios[] = "✅ Actualizado: <code>$option</code>";
    }
}

// 5. Buscar en todas las opciones (más agresivo)
global $wpdb;

// Buscar opciones con el color antiguo
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT option_name, option_value FROM {$wpdb->options} 
     WHERE option_value LIKE %s 
     AND option_name NOT LIKE '_transient%'
     AND option_name NOT LIKE '_site_transient%'",
    '%' . $wpdb->esc_like($old_color) . '%'
));

foreach ($results as $row) {
    $old_value = maybe_unserialize($row->option_value);
    $new_value = $old_value;
    
    if (is_string($old_value)) {
        $new_value = str_ireplace($old_color, $new_color, $old_value);
    } elseif (is_array($old_value)) {
        array_walk_recursive($new_value, function(&$value) use ($old_color, $new_color) {
            if (is_string($value) && stripos($value, $old_color) !== false) {
                $value = str_ireplace($old_color, $new_color, $value);
            }
        });
    }
    
    if ($old_value !== $new_value) {
        update_option($row->option_name, $new_value);
        $cambios[] = "✅ Actualizado: <code>{$row->option_name}</code>";
    }
}

// 6. Buscar en postmeta (estilos personalizados de posts/páginas)
$postmeta_results = $wpdb->get_results($wpdb->prepare(
    "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} 
     WHERE meta_value LIKE %s",
    '%' . $wpdb->esc_like($old_color) . '%'
));

foreach ($postmeta_results as $row) {
    $old_value = maybe_unserialize($row->meta_value);
    $new_value = $old_value;
    
    if (is_string($old_value)) {
        $new_value = str_ireplace($old_color, $new_color, $old_value);
    } elseif (is_array($old_value)) {
        array_walk_recursive($new_value, function(&$value) use ($old_color, $new_color) {
            if (is_string($value) && stripos($value, $old_color) !== false) {
                $value = str_ireplace($old_color, $new_color, $value);
            }
        });
    }
    
    if ($old_value !== $new_value) {
        update_post_meta($row->post_id, $row->meta_key, $new_value);
        $cambios[] = "✅ Actualizado: Post #{$row->post_id} meta <code>{$row->meta_key}</code>";
    }
}

// 7. Buscar en CSS personalizado de posts (Elementor, etc)
$css_posts = $wpdb->get_results(
    "SELECT ID, post_content FROM {$wpdb->posts} 
     WHERE post_type = 'custom_css' 
     OR post_content LIKE '%{$wpdb->esc_like($old_color)}%'"
);

foreach ($css_posts as $post) {
    if (stripos($post->post_content, $old_color) !== false) {
        $new_content = str_ireplace($old_color, $new_color, $post->post_content);
        $wpdb->update(
            $wpdb->posts,
            ['post_content' => $new_content],
            ['ID' => $post->ID]
        );
        $cambios[] = "✅ Actualizado: Post CSS #{$post->ID}";
    }
}

// Limpiar caché
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Mostrar resultados
echo '<h2>📋 Resumen de Cambios</h2>';

if (empty($cambios)) {
    echo '<div class="warning">';
    echo '<strong>⚠️ No se encontraron instancias del color ' . strtoupper($old_color) . '</strong><br>';
    echo 'Esto significa que:<br>';
    echo '• El color ya fue cambiado anteriormente<br>';
    echo '• El color está definido en archivos CSS del tema (no en la base de datos)<br>';
    echo '• El color se controla desde el Customizer de WordPress<br><br>';
    echo '<strong>Solución:</strong> Ve a <em>Apariencia → Personalizar → Colores</em> y cámbialo manualmente.';
    echo '</div>';
} else {
    echo '<div class="success">';
    echo '<strong>✅ Se realizaron ' . count($cambios) . ' cambios exitosamente</strong>';
    echo '</div>';
    
    echo '<table>';
    echo '<thead><tr><th>Cambio Realizado</th></tr></thead>';
    echo '<tbody>';
    foreach ($cambios as $cambio) {
        echo '<tr><td>' . $cambio . '</td></tr>';
    }
    echo '</tbody></table>';
    
    echo '<div class="info">';
    echo '<strong>🔄 Próximos pasos:</strong><br>';
    echo '1. Visita tu sitio y verifica los cambios<br>';
    echo '2. Limpia la caché del navegador (Ctrl+F5)<br>';
    echo '3. Si usas un plugin de caché, límpialo también<br>';
    echo '4. <strong>ELIMINA este archivo</strong> por seguridad';
    echo '</div>';
}

echo '<h2>🎨 Verificación Visual</h2>';
echo '<div style="display: flex; gap: 20px; margin: 20px 0;">';
echo '<div style="text-align: center;">';
echo '<div class="color-demo" style="background: ' . $old_color . '; width: 100px; height: 100px;"></div>';
echo '<p><strong>Color Antiguo</strong><br><code>' . strtoupper($old_color) . '</code></p>';
echo '</div>';
echo '<div style="text-align: center; font-size: 30px; line-height: 100px;">→</div>';
echo '<div style="text-align: center;">';
echo '<div class="color-demo" style="background: ' . $new_color . '; width: 100px; height: 100px;"></div>';
echo '<p><strong>Color Nuevo</strong><br><code>' . strtoupper($new_color) . '</code></p>';
echo '</div>';
echo '</div>';

echo '<a href="/" class="btn">← Volver al sitio</a>';
echo '</div>';

echo '</body></html>';
