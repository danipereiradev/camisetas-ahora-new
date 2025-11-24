# 🎨 Instrucciones Rápidas de Prueba

## ✅ Soluciones Implementadas

He añadido código personalizado que **funciona automáticamente**:

1. **Las variaciones seleccionadas** (color y talla) - Ya no se quitan al cambiar entre ellas
2. **Las imágenes subidas** - Persisten cuando cambias variaciones
3. **Las imágenes al añadir al carrito** - Ya NO se borran
4. **Las variaciones al cambiar cantidad** - Color/talla se mantienen
5. **Imagen del carrito personalizada** - Muestra el diseño del cliente, no la imagen base (NUEVO)

## 🧪 Cómo Probar

### Prueba A: Variaciones (Color y Talla se Mantienen)

#### 1. Abre un Producto con Variaciones
- Ve a tu tienda
- Abre un producto de camiseta personalizada

#### 2. Selecciona Color
- Elige un color (ej: Rojo)

#### 3. Selecciona Talla
- Elige una talla (ej: M)
- **🎉 El color Rojo debería permanecer seleccionado**

#### 4. Cambia la Talla
- Cambia a talla L
- **🎉 El color Rojo debería seguir seleccionado**

#### 5. Cambia el Color
- Cambia a color Azul
- **🎉 La talla L debería seguir seleccionada**

✅ **Resultado Esperado:** Color y talla se mantienen al cambiar entre ellas

---

### Prueba B: Imágenes al Añadir al Carrito (NUEVO)

#### 1. Sube Diseños
- Sube 2 imágenes en los campos

#### 2. Selecciona Variaciones
- Color: Rojo
- Talla: M

#### 3. Añade al Carrito (AJAX)
- Haz clic en "Añadir al carrito"
- **🎉 Las imágenes NO deberían borrarse del dropzone**

#### 4. Cambia Variaciones
- Cambia a: Azul, L
- **🎉 Las imágenes deberían seguir ahí**

#### 5. Añade al Carrito Otra Vez
- Haz clic en "Añadir al carrito"
- **🎉 Las imágenes TODAVÍA deberían estar ahí**

✅ **Resultado Esperado:** Imágenes persisten entre añadidas al carrito

---

### Prueba C: Variaciones al Cambiar Cantidad (NUEVO)

#### 1. Selecciona Variaciones
- Color: Rojo
- Talla: M

#### 2. Verifica Selección Inicial
- Color está en Rojo ✓
- Talla está en M ✓

#### 3. Cambia la Cantidad
- Cambia de 1 a 3 unidades
- **🎉 Color debería seguir siendo Rojo**
- **🎉 Talla debería seguir siendo M**

#### 4. Cambia Cantidad Otra Vez
- Cambia de 3 a 5 unidades
- **🎉 Todo debería mantenerse**

✅ **Resultado Esperado:** Variaciones permanecen al cambiar cantidad

---

### Prueba D: Imagen Personalizada en Carrito (NUEVO)

#### 1. Sube un Diseño Personalizado
- Sube una imagen distintiva (logo, diseño, etc.)

#### 2. Selecciona Variaciones y Añade
- Color: Rojo
- Talla: M
- Añadir al carrito

#### 3. Ve al Carrito
- Haz clic en "Ver carrito"

#### 4. Verifica la Imagen Principal (Thumbnail)
- Mira la imagen al **lado izquierdo** del producto
- **🎉 Debería mostrar TU diseño personalizado**
- ❌ NO debería mostrar la imagen genérica del producto

#### 5. Añade Otro Producto con Diferente Diseño
- Vuelve al producto
- Sube OTRA imagen diferente
- Selecciona: Azul, L
- Añadir al carrito

#### 6. Verifica Ambos en el Carrito
- **🎉 Producto 1 muestra diseño 1**
- **🎉 Producto 2 muestra diseño 2**
- Cada producto es visualmente distinguible

✅ **Resultado Esperado:** Cada producto en el carrito muestra su diseño personalizado

---

### Prueba E: Todo Integrado (Flujo Completo)

#### 1. Abre un Producto con Variaciones
- Ve a tu tienda
- Abre un producto de camiseta personalizada que tenga:
  - ✅ Campos de subida de imagen (2 diseños)
  - ✅ Variaciones de color
  - ✅ Variaciones de talla

### 2. Sube tus Diseños
- Sube la primera imagen en el primer campo
- Sube la segunda imagen en el segundo campo
- Espera a que ambas imágenes se carguen completamente

### 3. Selecciona Variaciones Iniciales
- Selecciona un **color** (ej: Rojo)
- Selecciona una **talla** (ej: M)

### 4. Cambia las Variaciones
- Cambia el **color** a otro (ej: Azul)
- Cambia la **talla** a otra (ej: L)
- **🎉 Las imágenes deberían permanecer visibles**

### 5. Añade al Carrito
- Haz clic en "Añadir al carrito"
- Verifica que el producto se añade con:
  - ✅ Las imágenes subidas
  - ✅ La variación seleccionada
- Repite el proceso con diferentes colores y tallas usando las mismas imágenes

## 🔍 Verificación de Funcionamiento

### ¿Cómo Saber si Está Funcionando?

#### Problema 1: Variaciones se Quitan

**ANTES de la solución:**
- ❌ Seleccionas color Rojo → Cambias talla a M → Color se quita
- ❌ Seleccionas talla L → Cambias color a Azul → Talla se quita

**DESPUÉS de la solución:**
- ✅ Seleccionas color Rojo → Cambias talla a M → Color permanece
- ✅ Seleccionas talla L → Cambias color a Azul → Talla permanece
- ✅ Puedes cambiar entre variaciones sin perder las selecciones previas

#### Problema 2: Imágenes se Borran

**ANTES de la solución:**
- ❌ Subes imágenes → Cambias variación → Imágenes desaparecen

**DESPUÉS de la solución:**
- ✅ Subes imágenes → Cambias variación → Imágenes permanecen
- ✅ Puedes añadir múltiples variaciones con las mismas imágenes
- ✅ Los clientes pueden hacer pedidos de diferentes colores/tallas con el mismo diseño

#### Todas las Soluciones Juntas

**Flujo Completo Funcionando:**
1. ✅ Subes 2 diseños (logo.jpg, texto.jpg)
2. ✅ Seleccionas color Rojo
3. ✅ Seleccionas talla M
4. ✅ Cambias cantidad a 3 → Color y talla se mantienen
5. ✅ Cambias a color Azul → Talla M se mantiene, imágenes intactas
6. ✅ Cambias a talla L → Color Azul se mantiene, imágenes intactas
7. ✅ Añades al carrito → Imágenes NO se borran
8. ✅ **Vas al carrito** → **VES tu logo.jpg como imagen principal** (NUEVO)
9. ✅ Todo perfecto - experiencia completa

## 🐛 Depuración (Opcional)

Si quieres ver el sistema funcionando internamente:

1. **Abre la Consola del Navegador**
   - Chrome/Firefox: Presiona `F12`
   - Mac: Presiona `Cmd + Option + I`

2. **Ve a la pestaña "Console"**

3. **Verás mensajes como:**

   **Para Variaciones:**
   ```
   Sistema de preservación de variaciones inicializado
   Guardando variación: attribute_pa_color = rojo
   Variación cambiada: attribute_pa_talla = m
   Restaurando variaciones...
   Restaurada variación: attribute_pa_color = rojo
   ```

   **Para Imágenes:**
   ```
   WAPF: Sistema de preservación de imágenes inicializado
   WAPF: Guardando estado de imágenes subidas...
   WAPF: Guardando campo 12345 : archivo1.jpg,archivo2.jpg
   WAPF: Archivos Dropzone guardados: 2
   WAPF: Variación encontrada, restaurando archivos...
   WAPF: Restaurando campo 12345
   WAPF: Archivos restaurados en Dropzone: 2
   WAPF: Restauración completada
   ```

## 📝 Casos de Uso

### Escenario 1: Cliente con Mismo Diseño, Múltiples Colores
1. Cliente sube su logo en los campos de imagen
2. Selecciona color Rojo, talla M → Añade al carrito
3. **Cambia a color Azul**, talla M → Las imágenes persisten → Añade al carrito
4. **Cambia a color Negro**, talla L → Las imágenes persisten → Añade al carrito
5. ✅ Resultado: 3 productos en el carrito con el mismo diseño en diferentes colores

### Escenario 2: Cliente con Múltiples Tallas
1. Cliente sube diseño de su equipo
2. Selecciona color Blanco, talla S → Añade al carrito
3. **Cambia a talla M** → Imágenes persisten → Añade al carrito
4. **Cambia a talla L** → Imágenes persisten → Añade al carrito
5. ✅ Resultado: 3 productos con el mismo diseño en diferentes tallas

### Escenario 3: Cliente Indeciso
1. Cliente sube sus diseños
2. **Prueba diferentes colores** viendo cómo se ven → Imágenes NO se borran
3. **Prueba diferentes tallas** → Imágenes NO se borran
4. Finalmente selecciona y añade al carrito
5. ✅ Resultado: Mejor experiencia de usuario, no hay frustración

## ⚠️ Notas Importantes

### Lo que SÍ hace la solución:
- ✅ Preserva imágenes al cambiar variaciones
- ✅ Funciona con múltiples campos de imagen
- ✅ Funciona con el editor de imágenes (si está activo)
- ✅ Se sincroniza automáticamente al subir/eliminar archivos

### Lo que NO hace (comportamiento normal de WooCommerce):
- ❌ No cambia el precio del producto automáticamente
- ❌ No fusiona productos idénticos en el carrito (comportamiento estándar)
- ❌ No modifica las validaciones del plugin WAPF

## 🔧 Solución de Problemas

### Problema: Las imágenes aún se borran
**Posibles causas:**
1. Cache del navegador → Presiona `Ctrl + F5` (Windows) o `Cmd + Shift + R` (Mac)
2. Cache del sitio → Limpia el cache de WordPress/plugins de cache
3. JavaScript desactivado → Verifica que JavaScript esté activo
4. Conflicto con otro plugin → Desactiva temporalmente otros plugins de optimización

### Problema: Las imágenes se duplican
**Solución:**
- Refresca la página con `F5`
- Limpia el cache del navegador

### Problema: No veo los mensajes en la consola
**Esto es normal si:**
- Todo está funcionando correctamente
- No has abierto la consola del navegador
- Los mensajes solo aparecen cuando cambias variaciones

## 📞 Siguiente Paso

**Prueba ahora mismo:**
1. Ve a tu producto de camiseta personalizada
2. Sube dos imágenes
3. Cambia el color o talla
4. Verifica que las imágenes permanecen

**Si funciona:** ¡Perfecto! 🎉 Ya puedes informar a tus clientes que pueden hacer pedidos de múltiples variaciones con el mismo diseño.

**Si hay problemas:** Revisa la sección "Solución de Problemas" arriba o contacta con soporte técnico.

---

**Archivos Modificados:**
- ✅ `/wp-content/themes/astra-child-camisetas/functions.php` (2 funciones añadidas)

**Archivos de Documentación:**
- 📄 `SOLUCION-VARIACIONES-README.md` (por qué NO usar URL params + solución implementada)
- 📄 `WAPF-PRESERVE-UPLOADS-README.md` (documentación técnica para imágenes)
- 📄 `INSTRUCCIONES-PRUEBA.md` (este archivo - guía rápida)

**Funciones Implementadas:**
1. ✅ `preserve_variation_selections()` - Mantiene color al cambiar talla (y viceversa)
2. ✅ `wapf_preserve_uploads_on_variation_change()` - Mantiene imágenes al cambiar variaciones

