# Rediseño Impulso Estratégico CX

Fecha inicio: 2026-06-07
Sitio actual: https://impulsoestrategicocx.com
Owner: Ariel Saez
Ejecución: Oliver

## Objetivo

Rediseñar el sitio de Impulso Estratégico CX para que deje de verse como brochure genérico de consultoría y funcione como una pieza comercial clara: posicionamiento, confianza, ofertas concretas y conversión a conversación/diagnóstico.

## Diagnóstico inicial

- El sitio actual funciona, pero comunica de forma amplia y poco diferenciada.
- El hero no abre con un dolor o resultado específico.
- Los servicios están descritos como categorías amplias, no como ofertas comprables.
- Falta evidencia: casos, escenarios, métricas, industrias, señales de experiencia.
- CTA principal demasiado genérico.
- Problemas técnicos: sin `robots.txt`, sin `sitemap.xml`, Tailwind CDN duplicado, assets pesados.

## Decisión de staging

Preferencia operativa: publicar primero en `https://impulsoestrategicocx.com/new/`.

Motivo:
- Menos riesgo que crear/tocar DNS para `new.impulsoestrategicocx.com`.
- El hosting actual ya sirve carpetas bajo `public_html`.
- Permite validar con Ariel antes de reemplazar producción.

Pendiente para deploy:
- Conseguir vía de escritura al hosting: FTP/SFTP, hPanel/File Manager o repo/fuente con deploy.
- La API Hostinger disponible permite inventario y creación de subdominios, pero no se encontró endpoint de subida de archivos.

## Alcance MVP

1. Nueva arquitectura de contenidos.
2. Nueva home estática responsive.
3. Copy comercial completo.
4. Optimización de assets.
5. `robots.txt` y `sitemap.xml`.
6. Staging en `/new/`.
7. Revisión con Ariel.
8. Lanzamiento reemplazando producción cuando esté aprobado.

## Propuesta de estructura

1. Hero con dolor/resultado concreto.
2. Problemas que resuelve Impulso.
3. Ofertas/servicios paquetizados.
4. Experiencia real de Ariel y método de trabajo.
5. Casos o escenarios tipo con resultados esperados.
6. Recursos/lead magnet.
7. CTA a diagnóstico inicial.
8. Contacto directo.

## Criterios de éxito

- En 10 segundos debe quedar claro qué hace Impulso, para quién y por qué confiar.
- Menos frases genéricas; más lenguaje de operación real.
- CTA claro hacia diagnóstico/conversación.
- Mobile impecable.
- Assets livianos.
- Sitio fácil de mantener y desplegar.

