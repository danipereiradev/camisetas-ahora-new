# Camisetas Custom - Tienda WooCommerce

Tienda de camisetas personalizadas con sistema avanzado de subida de diseños y gestión de variaciones.

## 🎯 Características Principales

### Sistema de Personalización
- ✅ Subida de diseños (imágenes) para personalización
- ✅ Editor de imágenes integrado (recorte, rotación, etc.)
- ✅ Vista previa en vivo del diseño sobre el producto
- ✅ Múltiples campos de diseño por producto

### Gestión de Variaciones Mejorada
- ✅ Preservación automática de imágenes subidas al cambiar variaciones
- ✅ Mantenimiento de color/talla al cambiar entre variaciones
- ✅ Persistencia de imágenes al añadir productos al carrito
- ✅ Preservación de variaciones al cambiar cantidad

## 🛠️ Stack Técnico

### WordPress & WooCommerce
- **WordPress:** Última versión
- **WooCommerce:** Sistema de ecommerce
- **PHP:** 7.0+

### Tema
- **Astra:** Tema base (padre)
- **Astra Child (camisetas-custom):** Tema hijo con personalizaciones

### Plugins Principales

#### Productos Personalizados
- **Advanced Product Fields Extended:** Campos personalizados en productos
- **APF Add-on: Image Upload (wapf-aiu):** Subida y edición de imágenes
- **APF Add-on: Live Content Preview (wapf-lcp):** Vista previa en vivo

#### WooCommerce
- **Variation Swatches for WooCommerce:** Selectores visuales de variaciones
- **Checkout Plugins - Stripe for WooCommerce:** Pagos con Stripe

#### Optimización
- **Really Simple SSL:** Seguridad SSL
- **Advanced Woo Search:** Búsqueda mejorada

## 📂 Estructura del Proyecto

```
/wp-content/
├── themes/
│   └── astra-child-camisetas/          # Tema hijo con personalizaciones
│       ├── functions.php               # Soluciones implementadas
│       ├── INSTRUCCIONES-PRUEBA.md     # Guía rápida de pruebas
│       ├── SOLUCION-VARIACIONES-README.md
│       ├── WAPF-PRESERVE-UPLOADS-README.md
│       └── SOLUCIONES-ADICIONALES.md   # Últimas soluciones
│
├── plugins/
│   ├── wapf-aiu/                       # Add-on de subida de imágenes
│   └── wapf-lcp/                       # Add-on de vista previa
│
└── uploads/                            # Archivos subidos (no versionado)
```

## 🚀 Soluciones Implementadas

### 1. Preservación de Imágenes al Cambiar Variaciones
**Problema:** Las imágenes subidas se borraban al cambiar color o talla.
**Solución:** Sistema de caché JavaScript que guarda y restaura imágenes automáticamente.

### 2. Preservación de Variaciones entre Sí
**Problema:** Al cambiar talla se quitaba el color (y viceversa).
**Solución:** Sistema de memoria que mantiene todas las variaciones seleccionadas.

### 3. Persistencia de Imágenes al Añadir al Carrito
**Problema:** Las imágenes se limpiaban al añadir productos al carrito.
**Solución:** Interceptación del evento `added_to_cart` para mantener las imágenes.

### 4. Preservación de Variaciones al Cambiar Cantidad
**Problema:** Color y talla se deseleccionaban al cambiar unidades.
**Solución:** Guardar/restaurar variaciones en cambios de cantidad.

## 📖 Documentación

### Para Desarrolladores
- 📄 `SOLUCION-VARIACIONES-README.md` - Detalles técnicos de variaciones
- 📄 `WAPF-PRESERVE-UPLOADS-README.md` - Sistema de preservación de imágenes
- 📄 `SOLUCIONES-ADICIONALES.md` - Últimos problemas resueltos

### Para Testing
- 📋 `INSTRUCCIONES-PRUEBA.md` - Guía rápida de pruebas

## 🧪 Cómo Probar

1. **Navega a un producto con variaciones**
2. **Sube diseños** (2 imágenes)
3. **Selecciona:** Color Rojo, Talla M
4. **Cambia cantidad** a 3 → Todo se mantiene ✅
5. **Añade al carrito** → Imágenes NO se borran ✅
6. **Cambia a:** Azul, L → Imágenes persisten ✅
7. **Añade al carrito** → Todo correcto ✅

## 🔧 Configuración de Desarrollo

### Requisitos
- PHP 7.0 o superior
- MySQL 5.6 o superior
- WordPress última versión
- WooCommerce activo

### Instalación Local

```bash
# Clonar el repositorio
git clone <repo-url> camisetas-custom

# Importar base de datos (no incluida en repo)
# Configurar wp-config.php con credenciales locales

# Instalar dependencias de WordPress (si aplica)
composer install

# Activar tema hijo
# Activar plugins necesarios
```

### Variables de Entorno

Crear archivo `wp-config.php` (no versionado) con:
```php
define('DB_NAME', 'tu_base_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASSWORD', 'tu_password');
define('DB_HOST', 'localhost');
```

## 🔒 Seguridad

- ✅ SSL implementado (Really Simple SSL)
- ✅ Archivos sensibles excluidos del repositorio
- ✅ `wp-config.php` no versionado
- ✅ Subidas de archivos validadas
- ✅ Headers de seguridad configurados

## 📝 Notas de Versión

### Versión 2.0 (2025-11-24)
- ✅ Preservación de imágenes al añadir al carrito
- ✅ Preservación de variaciones al cambiar cantidad
- ✅ Documentación completa actualizada

### Versión 1.0 (2025-11-24)
- ✅ Preservación de imágenes al cambiar variaciones
- ✅ Preservación de variaciones (color ↔ talla)
- ✅ Documentación inicial

## 🤝 Contribución

Este es un proyecto privado. Para cambios:
1. Crear rama feature
2. Implementar cambios
3. Probar exhaustivamente
4. Documentar en archivos MD
5. Crear PR

## 📞 Soporte

Para problemas o dudas:
1. Revisar documentación en tema hijo
2. Verificar consola del navegador (F12)
3. Consultar logs de WordPress

## 📄 Licencia

Proyecto privado. Todos los derechos reservados.

---

**Última actualización:** 2025-11-24  
**Versión:** 2.0  
**Estado:** ✅ Producción

