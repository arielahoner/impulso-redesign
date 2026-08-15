repo: arielahoner/impulso-redesign
branch: main

## Last sync

date: 2026-08-15T21:21:34Z
tree: 639c71de2f0a

### Updated in this project

- Rediseño completo hacia IA aplicada: se eliminó todo el pilar de liderazgo, cultura y equipos.
- Reescritura total del copy del frente de IA para eliminar el solapamiento textual con luuplab.com.
- Sitio ampliado de 7 a 17 páginas: 5 casos y 5 industrias con URL propia.
- Formulario migrado de Web3Forms a Resend vía endpoint PHP; GA4 y CSP actualizados.

## Estado del repositorio

Sincronizado. El push del 15 de agosto de 2026 reemplazó por completo la versión
anterior: se eliminaron los 19 archivos antiguos (incluida la carpeta `site/`
duplicada y los assets en desuso) y se subieron los 36 del rediseño.

Verificado en el repositorio: `.htaccess` presente con el CSP correcto,
`site/` eliminada, y `api/config.php` ausente por diseño (solo viaja la
plantilla `config.example.php`; la API key se sube por FTP al servidor).

El acceso a GitHub desde este proyecto es de solo lectura: los archivos se suben
con `bash publicar.sh` desde el equipo del usuario.

## Screen map

| Página del proyecto | Origen en el repositorio |
|---|---|
| index.html | reemplazó index.html y site/index.html |
| servicios.html | nueva |
| casos.html | nueva |
| caso-licitaciones-energia-solar.html | nueva |
| caso-prospeccion-b2b.html | nueva |
| caso-crm-ecommerce-comercio.html | nueva |
| caso-automatizacion-head-hunting.html | nueva |
| caso-crm-erp-agroindustria.html | nueva |
| ia-energia-solar.html | nueva |
| automatizacion-ventas-b2b.html | nueva |
| software-comercio-ecommerce.html | nueva |
| automatizacion-head-hunting.html | nueva |
| software-agroindustria.html | nueva |
| nosotros.html | nueva |
| contacto.html | nueva |
| privacidad.html · terminos.html · 404.html | nuevas |
| assets/css/styles.css | reemplazó styles.css y site/styles.css |
| assets/js/main.js | reemplazó main.js y site/main.js |
| assets/js/analytics.js | nuevo |
| api/contacto.php · api/config.example.php | nuevos |
| sitemap.xml · robots.txt | reemplazaron las versiones de la raíz y de site/ |
| .htaccess | nuevo |
