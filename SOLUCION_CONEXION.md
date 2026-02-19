# Solución de Problemas de Conexión

## Error: "Se agotó el tiempo de espera del semáforo"

Este error significa que la app Flutter no puede conectarse a Laravel. Sigue estos pasos:

### ✅ Paso 1: Verificar que Laravel esté corriendo

Abre una terminal en la carpeta `AgroCafe` y ejecuta:

```bash
php artisan serve
```

Deberías ver:
```
Server running on http://127.0.0.1:8000
```

**IMPORTANTE:** Deja esta terminal abierta mientras uses la app.

---

### ✅ Paso 2: Verificar que Laravel sea accesible

Abre tu navegador y ve a:
- `http://127.0.0.1:8000/api/login` (debería dar error de método, pero no timeout)
- O `http://10.0.2.2:8000/api/login` si estás probando desde emulador

Si no carga, Laravel no está corriendo o hay un problema de red.

---

### ✅ Paso 3: Verificar la configuración en Flutter

Abre `agroapp/lib/utils/constants.dart` y verifica:

**Para Emulador Android:**
```dart
static const String baseUrl = 'http://10.0.2.2:8000/api';
```

**Para Dispositivo Físico:**
```dart
// Reemplaza con tu IP local
static const String baseUrl = 'http://192.168.1.XXX:8000/api';
```

---

### ✅ Paso 4: Verificar Firewall de Windows

1. Abre "Firewall de Windows Defender"
2. Ve a "Configuración avanzada"
3. Verifica que el puerto 8000 esté permitido
4. O temporalmente desactiva el firewall para probar

---

### ✅ Paso 5: Verificar que estén en la misma red

Si usas dispositivo físico:
- Tu computadora y tu teléfono deben estar en la misma red WiFi
- No uses datos móviles en el teléfono

---

### ✅ Paso 6: Probar con Postman o navegador

Prueba hacer una petición POST a:
```
http://127.0.0.1:8000/api/login
```

Con este body:
```json
{
  "email": "admin@agrocafe.com",
  "password": "admin123"
}
```

Si funciona en Postman pero no en la app, el problema es de configuración de la app.

---

## Solución Rápida

1. **Abre 2 terminales:**

   **Terminal 1 (Laravel):**
   ```bash
   cd AgroCafe
   php artisan serve --host=0.0.0.0 --port=8000
   ```

   **Terminal 2 (Flutter):**
   ```bash
   cd agroapp
   flutter run
   ```

2. **Verifica que Laravel responda:**
   - Abre navegador: `http://127.0.0.1:8000`
   - Debería mostrar la página de Laravel

3. **Si usas dispositivo físico, encuentra tu IP:**
   ```bash
   ipconfig
   ```
   Busca "IPv4 Address" y úsala en `constants.dart`

---

## Comandos de Verificación

```bash
# Verificar que Laravel esté corriendo
curl http://127.0.0.1:8000/api/login

# Verificar desde emulador (si usas Android)
adb reverse tcp:8000 tcp:8000
```

---

## Si nada funciona

1. Reinicia Laravel: `Ctrl+C` y luego `php artisan serve` de nuevo
2. Reinicia la app Flutter: `flutter run` de nuevo
3. Verifica los logs de Laravel en `storage/logs/laravel.log`
4. Verifica que no haya otro proceso usando el puerto 8000

