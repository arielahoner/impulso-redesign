# Publicar el sitio · GitHub → Hostinger

Método elegido: **Git deploy de Hostinger**. Conectas el repositorio una vez y
desde ahí cada actualización es un push. No hay llaves ni secretos que gestionar,
que es la parte donde normalmente se rompen estos flujos.

---

## Paso 1 · Limpiar el repositorio

El repositorio tiene hoy la versión antigua del sitio, y además duplicada: una
copia en la raíz y otra dentro de `site/`. Todo eso se reemplaza.

**Archivos a eliminar:**

```
index.html          (raíz — se reemplaza por el nuevo)
styles.css          (raíz)
main.js             (raíz)
sitemap.xml         (raíz — se reemplaza)
robots.txt          (raíz — se reemplaza)
brief.md
site/               (carpeta completa)
assets/abstract-cx-bg.webp
assets/ariel-saez.webp
assets/logo-impulso.png
assets/logo-impulso.svg
```

La carpeta `site/` importa especialmente: si queda, Hostinger la despliega y
`impulsoestrategicocx.com/site/` sirve una copia del sitio. Google lo trata como
contenido duplicado y decide por su cuenta cuál versión indexar.

## Paso 2 · Subir los archivos nuevos

En tu computador, dentro de la carpeta del repositorio ya clonado:

```bash
cd ruta/a/impulso-redesign

# Borrar lo viejo
git rm -r site assets/abstract-cx-bg.webp assets/ariel-saez.webp \
          assets/logo-impulso.png assets/logo-impulso.svg \
          styles.css main.js brief.md

# Copiar aquí TODO el contenido del ZIP descargado, sobrescribiendo lo que exista.
# Verifica que .htaccess y .gitignore hayan quedado: son archivos ocultos y
# el Finder de macOS no los muestra (Cmd+Shift+. los hace visibles).

git add -A
git commit -m "Rediseño completo: 100% IA aplicada, 17 páginas, Resend y GA4"
git push origin main
```

Antes del push, confirma que `api/config.php` **no** aparece en `git status`.
El `.gitignore` lo excluye, pero vale la pena mirarlo: si esa key entra a
GitHub, Resend la detecta y la revoca.

## Paso 3 · Conectar Hostinger al repositorio

1. hPanel → tu dominio → **Avanzado → Git**.
2. **Repositorio:** `https://github.com/arielahoner/impulso-redesign`
3. **Rama:** `main`
4. **Directorio de instalación:** déjalo vacío para que despliegue en
   `public_html`, que es la raíz del sitio.
5. Pulsa **Crear**. Hostinger clona el repositorio.
6. Cada vez que hagas push, entra a esa misma pantalla y pulsa **Deploy**.
   Si quieres que sea automático, copia el **webhook** que te muestra Hostinger
   y pégalo en GitHub → repo → Settings → Webhooks → Add webhook.

> Si `public_html` ya tiene archivos de una versión anterior subida a mano,
> vacíala antes del primer deploy. Un archivo huérfano de la versión vieja se
> queda servido para siempre, porque el deploy agrega y sobrescribe pero no
> borra lo que ya no existe en el repositorio.

## Paso 4 · Subir `api/config.php`

Este es el único archivo que no viaja por el repositorio.

1. hPanel → **Administrador de archivos** → `public_html/api/`
2. Sube tu `api/config.php` con la API key de Resend ya pegada.
3. Confirma que el `from` sea una dirección del dominio verificado
   (`web@impulsoestrategicocx.com` o similar). Un Gmail ahí hace fallar el envío.

Permisos: `644` para el archivo, `755` para la carpeta. Es lo que el
Administrador de archivos aplica por defecto.

## Paso 5 · Verificar en vivo

Recorre esta lista en el sitio publicado, no en local:

- [ ] `https://impulsoestrategicocx.com` carga con candado y sin advertencias
- [ ] `http://` redirige solo a `https://`
- [ ] Las 17 páginas abren: home, servicios, casos, los 5 casos, las 5 industrias, nosotros, contacto, privacidad, términos
- [ ] `impulsoestrategicocx.com/site/` devuelve 404 (si carga algo, quedó la carpeta vieja)
- [ ] `impulsoestrategicocx.com/api/config.php` devuelve 403 o página en blanco, **nunca** el contenido del archivo
- [ ] El formulario envía y llegan los dos correos: la notificación y el acuse
- [ ] El botón de tema claro/oscuro funciona (si no, el CSP está bloqueando el script inline)
- [ ] La consola del navegador no muestra errores de Content-Security-Policy
- [ ] `impulsoestrategicocx.com/sitemap.xml` lista 17 URLs

## Paso 6 · Decirle a Google

1. Search Console → **Sitemaps** → enviar `sitemap.xml`.
2. **Inspección de URLs** → pedir indexación a mano de las 10 páginas nuevas
   (5 casos + 5 industrias). Solas pueden tardar semanas.
3. Si la versión anterior tenía URLs distintas que ya estaban indexadas,
   avísame y armamos las redirecciones 301 en el `.htaccess`. Sin eso, esas
   direcciones quedan en 404 y se pierde la autoridad que hubieran acumulado.

---

## Si algo sale mal

**El sitio carga sin estilos** → el CSS no llegó, o llegó a otra ruta.
Comprueba que exista `public_html/assets/css/styles.css`.

**Las páginas no cargan y la consola menciona CSP** → se editó el script inline
del tema y el hash del `.htaccess` ya no coincide. Restaura ese script tal como
está en el repositorio.

**El formulario responde "El formulario aún no está configurado"** → falta
`api/config.php` en el servidor, o quedó en otra carpeta.

**El formulario da error de envío** → mira hPanel → Avanzado → Registro de
errores PHP. Ahí queda la respuesta exacta de Resend. Lo más común es un
remitente que no pertenece al dominio verificado.

**Las cabeceras de seguridad no se aplican** → confirma que `.htaccess` llegó al
servidor. Es un archivo oculto y es el que más se queda en el camino.
