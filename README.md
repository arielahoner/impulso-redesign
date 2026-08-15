# impulsoestrategicocx.com

Sitio estático de Impulso Estratégico CX & AI. HTML, CSS y JavaScript sin build:
lo que está en el repositorio es exactamente lo que se sirve.

Alojado en Hostinger. Formulario de contacto vía Resend. Analítica con GA4.

---

## Estructura

```
index.html                          Home
servicios.html                      Modelo de entrega, catálogo, industrias, FAQ
casos.html                          Los 5 casos
nosotros.html                       Equipo, misión, valores
contacto.html                       Formulario
privacidad.html  terminos.html      Legales
404.html

caso-*.html                         5 páginas de caso (una por proyecto)
ia-energia-solar.html               5 páginas de industria
automatizacion-ventas-b2b.html
software-comercio-ecommerce.html
automatizacion-head-hunting.html
software-agroindustria.html

api/contacto.php                    Endpoint del formulario (llama a Resend)
api/config.example.php              Plantilla de configuración
assets/css/styles.css               Toda la hoja de estilos
assets/js/main.js                   Tema, menú, formulario, eventos
assets/js/analytics.js              GA4 — pegar aquí el Measurement ID
assets/img/                         Logo, favicon, fotos, imagen OG
.htaccess                           HTTPS, cabeceras de seguridad, CSP, caché
sitemap.xml  robots.txt
GUIA-INSTALACION.md                 Pasos de Resend, GA4 y Search Console
```

## Dos archivos que requieren atención

**`api/config.php`** no está en el repositorio y nunca debe estarlo: contiene la
API key de Resend. Se sube una vez por FTP al servidor, se queda ahí y los
deploys posteriores no lo tocan. La plantilla es `api/config.example.php`.

**`.htaccess`** sí tiene que llegar al servidor. Fuerza HTTPS, aplica las
cabeceras de seguridad y define el Content-Security-Policy. Si el deploy lo
omite, el formulario y Google Analytics quedan bloqueados por el navegador.

## Al modificar el script inline del tema

Cada página lleva en el `<head>` un script inline de una línea que aplica el
tema claro/oscuro antes del primer render. Su hash sha256 está declarado en el
CSP del `.htaccess`. **Si se edita ese script, hay que regenerar el hash** o
todas las páginas dejan de cargar. Lo más simple es no tocarlo.

## Requisitos del servidor

- PHP 8.0 o superior con cURL (Hostinger lo trae por defecto)
- HTTPS activo
- `mod_headers`, `mod_rewrite` y `mod_expires` habilitados

## Publicar

Ver la sección de deploy en `GUIA-INSTALACION.md`.
