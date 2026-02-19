# Descargar facturas en PDF (sin abrir vista HTML)

Para que al hacer clic en **PDF** se abra solo el cuadro **"Guardar como"** y no la página de la factura, hace falta instalar la librería DomPDF.

## Pasos (usar PHP 8.2)

1. Abre **Laragon**.
2. En el menú **PHP** elige la versión **8.2** (o la que tengas ≥ 8.2).
3. Abre **Terminal** en Laragon (o CMD/PowerShell en la carpeta del proyecto).
4. Ejecuta:
   ```bash
   cd C:\laragon\www\sistema de cafe\AgroCafe
   composer update dompdf/dompdf
   ```
5. Si todo va bien, al hacer clic en el botón PDF de una factura (admin o campesino) se descargará el archivo y se abrirá el cuadro para elegir **dónde guardar el PDF** en tu PC, sin abrir ninguna vista HTML.

Si no tienes PHP 8.2 en Laragon, instala o activa esa versión desde el menú de Laragon.
