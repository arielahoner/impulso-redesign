# Guía de instalación · Resend, Google Analytics 4 y Search Console

Todo lo que tienes que hacer tú, en orden. Nada requiere tocar código salvo pegar dos valores.

---

## 1. Formulario de contacto con Resend

El formulario ya no usa Web3Forms. Ahora envía a `api/contacto.php`, que a su vez llama a Resend. La API key vive en el servidor, nunca en el navegador.

### 1.1 Crear la cuenta y verificar el dominio

1. Entra a **resend.com** y crea la cuenta con un correo del dominio.
2. Ve a **Domains → Add Domain** y escribe `impulsoestrategicocx.com`.
3. Resend te mostrará entre 3 y 4 registros DNS. Son de tipo **TXT**, **MX** y **CNAME**.
4. En **Hostinger → hPanel → Dominios → Zona DNS**, agrega cada registro tal cual: mismo tipo, mismo nombre, mismo valor.
   - Si Hostinger te pide el "nombre" y Resend te da algo como `resend._domainkey.impulsoestrategicocx.com`, en Hostinger normalmente escribes solo `resend._domainkey`.
   - No borres tu registro MX actual si usas ese dominio para recibir correo. Resend usa un subdominio propio (`send.` o similar) para el MX.
5. Vuelve a Resend y pulsa **Verify**. Puede tardar de 5 minutos a unas horas.

> Si ya tienes un SPF (`v=spf1 ...`) en el dominio, **no crees un segundo**: añade el include de Resend dentro del que ya existe. Dos registros SPF invalidan la autenticación y tus correos caen en spam.

### 1.2 Crear la API key

1. En Resend, **API Keys → Create API Key**.
2. Nombre: `sitio-web`. Permiso: **Sending access**. Dominio: el tuyo.
3. Copia la key (empieza por `re_`). **Solo se muestra una vez.**

### 1.3 Configurar el archivo en el servidor

1. En el proyecto hay una carpeta `api/` con dos archivos: `contacto.php` y `config.example.php`.
2. Duplica `config.example.php` y renombra la copia a **`config.php`** (misma carpeta).
3. Abre `config.php` y rellena:
   - `resend_api_key` → la key que copiaste.
   - `from` → un remitente del dominio verificado, por ejemplo `Sitio web Impulso Estratégico <web@impulsoestrategicocx.com>`. **No uses Gmail**: el envío fallará.
   - `to` → los correos que reciben los formularios (ya vienen los de ustedes dos).
   - `autoreply` → déjalo en `true` si quieres que el visitante reciba un acuse automático.
4. Sube todo por FTP o por el Administrador de archivos de Hostinger, respetando la carpeta `api/`.

### 1.4 Requisitos del hosting

- PHP **8.0 o superior** (en hPanel: Avanzado → Configuración PHP). Hostinger lo trae por defecto.
- Extensión **cURL** activa (viene activada; si no, el código usa un método alternativo automáticamente).

### 1.5 Probar

Entra a `impulsoestrategicocx.com/contacto.html`, envía un mensaje de prueba y revisa:

- Que llegue el correo a las dos casillas.
- Que el acuse llegue al remitente.
- Que al responder el correo, el "Para" sea el email del visitante (está configurado el `reply_to`).

Si algo falla, el error queda en el log de errores de PHP de Hostinger (hPanel → Avanzado → Registro de errores PHP). El visitante nunca ve detalles técnicos.

### Lo que YA está resuelto en el código

- Honeypot antispam (campo oculto `website`).
- Límite de 5 envíos por hora y por IP.
- Validación de todos los campos en servidor, no solo en el navegador.
- Protección del `config.php`: el `.htaccess` impide que se sirva por web aunque alguien adivine la ruta.
- La política de seguridad (CSP) del `.htaccess` ya fue actualizada: se quitó Web3Forms y se permitió Google Analytics.

---

## 2. Google Analytics 4

**Aclaración:** GA4 *es* Google Analytics. No son dos productos distintos. Universal Analytics dejó de funcionar; hoy solo existe GA4, y su identificador tiene el formato `G-XXXXXXXXXX`.

### 2.1 Crear la propiedad

1. Entra a **analytics.google.com** con la cuenta de Google de la empresa.
2. **Administrar → Crear → Propiedad.**
   - Nombre: `Impulso Estratégico CX & AI`
   - Zona horaria: Colombia o Chile (la que uses para reportar; no se puede cambiar sin perder comparabilidad).
   - Moneda: la que uses para reportar.
3. Al terminar, crea un **Flujo de datos → Web** con la URL `https://impulsoestrategicocx.com` y nombre `Sitio web`.
4. Copia el **Measurement ID**: `G-XXXXXXXXXX`.

### 2.2 Activarlo en el sitio

Abre **`assets/js/analytics.js`** y pega el ID en la primera línea de código:

```js
var GA4_ID = "G-XXXXXXXXXX";
```

Eso es todo: las 7 páginas ya cargan ese archivo. Mientras el valor esté vacío, Google Analytics no se carga y no se instala ninguna cookie de terceros.

### 2.3 Eventos que ya están programados

No necesitas Tag Manager para medir lo importante. El sitio envía solo:

| Evento | Cuándo se dispara |
|---|---|
| `generate_lead` | Se envía el formulario de contacto con éxito |
| `whatsapp_click` | Clic en el botón de WhatsApp |
| `email_click` | Clic en cualquier correo del sitio |
| `phone_click` | Clic en cualquier teléfono |
| `cta_click` | Clic en los botones principales, con etiqueta de ubicación (`hero`, `radiografia`, `cierre`…) |

### 2.4 Marcarlos como conversiones

En GA4: **Administrar → Eventos clave** (antes "Conversiones") → activa el interruptor en `generate_lead` y `whatsapp_click`. Los eventos aparecen en la lista **solo después de haberse disparado al menos una vez**, así que envía una prueba primero.

### 2.5 ¿Necesitas Google Tag Manager?

No. GTM sirve para agregar y editar etiquetas sin tocar código, algo útil cuando hay muchas campañas o varias personas gestionando el sitio. Con el volumen actual, `gtag` directo es más rápido y más fácil de mantener. Si más adelante lanzan campañas de Google Ads o Meta, ahí sí conviene migrar a GTM: avísame y lo cambio.

---

## 3. Google Search Console (imprescindible para el SEO)

Sin esto no hay forma de saber qué busca la gente ni qué páginas se indexan.

1. Entra a **search.google.com/search-console** → **Agregar propiedad → Dominio**.
2. Te dará un registro **TXT**. Agrégalo en **Hostinger → Zona DNS**.
3. Verifica. Luego ve a **Sitemaps** y envía: `sitemap.xml`.
4. Enlaza la propiedad con GA4: en Search Console, **Configuración → Asociaciones → Google Analytics**.

Repite el paso 1–3 en **Bing Webmaster Tools** si quieres cubrir Bing y ChatGPT Search: permite importar la verificación directamente desde Search Console en un clic.

---

## 4. Después de publicar

- [ ] Envía el sitemap en Search Console.
- [ ] Solicita la indexación de la nueva página `casos.html`.
- [ ] Actualiza el enlace del sitio en LinkedIn e Instagram.
- [ ] Prueba el formulario desde un teléfono, no solo desde el computador.
- [ ] Revisa que la vista clara y la oscura se vean bien en las siete páginas.
