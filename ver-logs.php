<?php
/**
 * Visor de logs de WordPress
 * USAR SOLO EN DESARROLLO - ELIMINAR EN PRODUCCIÓN
 */

// Seguridad básica
if (!isset($_GET['ver']) || $_GET['ver'] !== 'logs') {
    die('Acceso denegado');
}

$log_file = __DIR__ . '/wp-content/debug.log';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WordPress Debug Logs</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: monospace;
            background: #1e1e1e;
            color: #d4d4d4;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 20px;
        }
        .controls {
            margin-bottom: 20px;
            padding: 15px;
            background: #252526;
            border-radius: 4px;
        }
        button {
            padding: 10px 20px;
            margin-right: 10px;
            background: #0e639c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #1177bb;
        }
        .log-container {
            background: #252526;
            padding: 20px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .log-line {
            padding: 4px 0;
            border-bottom: 1px solid #3e3e42;
        }
        .log-line.error {
            color: #f48771;
        }
        .log-line.warning {
            color: #dcdcaa;
        }
        .log-line.debug {
            color: #4ec9b0;
        }
        .log-line.cart {
            background: #1e3a5f;
        }
        .log-line.thumbnail {
            background: #5f1e1e;
        }
        .highlight {
            background: #2d2d30;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 WordPress Debug Logs</h1>
        
        <div class="controls">
            <button onclick="location.reload()">🔄 Actualizar</button>
            <button onclick="clearLogs()">🗑️ Limpiar Logs</button>
            <button onclick="filterLogs('all')">Todos</button>
            <button onclick="filterLogs('cart')">ADD TO CART</button>
            <button onclick="filterLogs('thumbnail')">THUMBNAIL</button>
        </div>
        
        <div class="log-container">
            <?php
            if (file_exists($log_file)) {
                $logs = file($log_file);
                $logs = array_reverse(array_slice($logs, -200)); // Últimas 200 líneas
                
                foreach ($logs as $line) {
                    $line = htmlspecialchars($line);
                    $class = 'log-line';
                    
                    if (stripos($line, 'ADD TO CART DEBUG') !== false) {
                        $class .= ' debug cart';
                    } elseif (stripos($line, 'THUMBNAIL DEBUG') !== false) {
                        $class .= ' debug thumbnail';
                    } elseif (stripos($line, 'error') !== false || stripos($line, 'ERROR') !== false) {
                        $class .= ' error';
                    } elseif (stripos($line, 'warning') !== false || stripos($line, 'WARNING') !== false) {
                        $class .= ' warning';
                    }
                    
                    echo '<div class="' . $class . '">' . $line . '</div>';
                }
            } else {
                echo '<div class="log-line warning">⚠️ El archivo debug.log no existe todavía. Añade productos al carrito para generar logs.</div>';
            }
            ?>
        </div>
    </div>
    
    <script>
        function clearLogs() {
            if (confirm('¿Limpiar todos los logs?')) {
                fetch('?ver=logs&action=clear')
                    .then(() => location.reload());
            }
        }
        
        function filterLogs(type) {
            var lines = document.querySelectorAll('.log-line');
            lines.forEach(function(line) {
                if (type === 'all') {
                    line.style.display = 'block';
                } else if (type === 'cart') {
                    line.style.display = line.classList.contains('cart') ? 'block' : 'none';
                } else if (type === 'thumbnail') {
                    line.style.display = line.classList.contains('thumbnail') ? 'block' : 'none';
                }
            });
        }
        
        // Auto-scroll al final
        window.scrollTo(0, document.body.scrollHeight);
    </script>
</body>
</html>

<?php
// Limpiar logs si se solicita
if (isset($_GET['action']) && $_GET['action'] === 'clear' && file_exists($log_file)) {
    file_put_contents($log_file, '');
    echo 'Logs limpiados';
    exit;
}
?>
