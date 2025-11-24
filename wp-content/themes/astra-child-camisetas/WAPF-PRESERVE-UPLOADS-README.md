# Solución: Preservar Imágenes Subidas en WAPF al Cambiar Variaciones

## Problema
Cuando los clientes suben diseños en los campos de imagen del plugin "Advanced Product Fields" y luego seleccionan diferentes variaciones (color y talla), las imágenes subidas se borran. Esto impide que los clientes puedan hacer pedidos de diferentes colores y tallas con el mismo diseño.

## Solución Implementada
Se ha añadido código JavaScript personalizado en el archivo `functions.php` del tema hijo `astra-child-camisetas` que:

1. **Guarda automáticamente** el estado de las imágenes subidas cuando se detecta un cambio en las variaciones
2. **Restaura las imágenes** después de que se seleccione una nueva variación
3. **Mantiene sincronizado** el cache cuando se suben o eliminan nuevas imágenes

## Cómo Funciona

### Eventos Monitoreados
- `woocommerce_variation_select_change`: Se guarda el estado antes del cambio
- `found_variation`: Se restauran las imágenes después de seleccionar variación
- `wapf/file_uploaded`: Se actualiza el cache cuando se sube un nuevo archivo
- `wapf/file_deleted`: Se actualiza el cache cuando se elimina un archivo

### Almacenamiento Temporal
El sistema crea un cache temporal (`wapfUploadCache`) que almacena:
- El valor del campo de texto (rutas de archivos)
- Información completa de cada archivo en Dropzone (nombre, tamaño, tipo, vista previa)
- Estado de cada archivo (completado, procesando, etc.)

### Proceso de Restauración
1. Detecta el cambio de variación
2. Guarda todos los archivos actuales en memoria
3. Espera a que WooCommerce cargue la nueva variación (300ms)
4. Restaura los archivos guardados en los campos
5. Recrea las vistas previas en Dropzone
6. Oculta el mensaje "Arrastra archivos aquí" si hay archivos

## Pruebas

### Cómo Probar la Solución

1. **Ir a un producto con variaciones** (ej: camiseta personalizada)
   
2. **Subir dos diseños** en los campos de imagen

3. **Seleccionar color y talla inicial**

4. **Cambiar el color o la talla**

5. **Verificar que las imágenes permanecen** en los campos de subida

6. **Añadir al carrito** con las variaciones y diseños seleccionados

7. **Repetir para diferentes colores/tallas** con el mismo diseño

### Consola del Navegador
Para depuración, el sistema muestra mensajes en la consola del navegador:
- "WAPF: Sistema de preservación de imágenes inicializado"
- "WAPF: Guardando estado de imágenes subidas..."
- "WAPF: Variación encontrada, restaurando archivos..."
- "WAPF: Restauración completada"

Para ver estos mensajes:
1. Presiona F12 o Cmd+Option+I (Mac)
2. Ve a la pestaña "Console"
3. Realiza las acciones de cambio de variación

## Compatibilidad

### Plugins Compatibles
- ✅ Advanced Product Fields Extended for WooCommerce (v3.1.2)
- ✅ Advanced Product Fields add-on: Image Upload (wapf-aiu)
- ✅ Advanced Product Fields add-on: Live Content Preview (wapf-lcp)
- ✅ WooCommerce (variable products)
- ✅ Variation Swatches for WooCommerce

### Temas Compatibles
- ✅ Astra (tema padre)
- ✅ Astra Child (tema hijo personalizado)

## Mantenimiento

### Si Necesitas Desactivar la Funcionalidad
Comenta o elimina el código en `functions.php` entre las líneas:
```php
/**
 * Preservar imágenes subidas de WAPF cuando se cambian variaciones
 */
```
hasta:
```php
add_action( 'wp_footer', 'wapf_preserve_uploads_on_variation_change', 999 );
```

### Si Necesitas Modificar el Tiempo de Espera
Si las imágenes no se restauran correctamente, puedes aumentar el tiempo de espera en la línea:
```javascript
setTimeout(function() {
    restoreUploadedFiles();
}, 300); // Cambiar 300 a 500 o más (milisegundos)
```

## Notas Técnicas

### Por Qué se Necesitaba Esta Solución
El plugin WAPF no tiene funcionalidad nativa para preservar campos al cambiar variaciones. WooCommerce dispara eventos que pueden causar que los campos personalizados se reinicien o limpien.

### Enfoque Utilizado
En lugar de prevenir el comportamiento de limpieza (que podría causar conflictos), la solución:
1. Permite que el sistema funcione normalmente
2. Guarda el estado antes del cambio
3. Restaura el estado después del cambio
4. Es no-invasivo y compatible con futuras actualizaciones

## Soporte

Si encuentras problemas:
1. Verifica la consola del navegador para mensajes de error
2. Asegúrate de que los plugins WAPF estén actualizados
3. Prueba con un solo archivo primero
4. Verifica que el producto esté configurado como "Variable"
5. Comprueba que los campos WAPF estén correctamente configurados

## Changelog

### Versión 1.0 (2025-11-24)
- ✅ Implementación inicial
- ✅ Guardado automático de estado
- ✅ Restauración automática al cambiar variaciones
- ✅ Soporte para múltiples archivos por campo
- ✅ Logging en consola para depuración
- ✅ Prevención de loops infinitos
- ✅ Sincronización con eventos de subida/eliminación

