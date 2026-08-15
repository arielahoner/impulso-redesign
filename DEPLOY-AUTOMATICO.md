# Deploy automático · GitHub → Hostinger

> **Estado actual: activo por el camino A (webhook de Hostinger).**
> Configurado el 15 de agosto de 2026 y verificado con entrega exitosa.
> Cada push a `main` publica el sitio automáticamente. No hay que entrar a hPanel.
>
> El camino B queda documentado abajo solo como alternativa, por si alguna vez el
> webhook deja de funcionar. **No configures los dos a la vez**: ambos escriben en
> `public_html` y se pisan.

Objetivo: que cada push a `main` publique el sitio sin que toques hPanel.

Hay dos caminos. **Prueba primero el A**, que no requiere configurar nada.

---

## Camino A · Webhook de Hostinger (sin configuración)

hPanel → tu dominio → **Avanzado → GIT**. En la fila del repositorio, abre el
menú de tres puntos. Busca una opción tipo **Webhook URL**, *Copiar webhook* o
*Auto deployment*.

Si aparece:

1. Copia esa URL.
2. Ve a GitHub → `impulso-redesign` → **Settings → Webhooks → Add webhook**.
3. **Payload URL:** la URL copiada.
4. **Content type:** `application/json`
5. Deja marcado *Just the push event* y **Add webhook**.

Listo: cada push dispara el deploy. Si en el menú de Hostinger no hay webhook
—depende del plan—, usa el camino B.

---

## Camino B · GitHub Actions por SSH (alternativa, hoy sin usar)

Este camino **no está activo**. El archivo `.github/workflows/deploy.yml` no está
en el repositorio: se eliminó al confirmar que el webhook funcionaba. Si alguna
vez lo necesitas, pídemelo y lo vuelvo a generar.

Sincroniza con `rsync --delete`, así que si borras una página del repositorio
también desaparece del servidor — algo que el webhook de Hostinger no hace.
Solo faltarían cuatro secretos.

### 1. Obtener los datos SSH de Hostinger

hPanel → **Avanzado → Acceso SSH**. Ahí verás:

- **Host / IP del servidor** → por ejemplo `82.197.xxx.xxx`
- **Puerto** → en Hostinger normalmente `65002`
- **Usuario** → el tuyo es `u679759757`

Necesitas además tu **clave privada**, la que corresponde a la pública que ya
cargaste en Hostinger. En tu Mac suele estar en `~/.ssh/id_ed25519` (sin `.pub`).
Para verla completa:

```bash
cat ~/.ssh/id_ed25519
```

Copia **todo**, incluidas las líneas `-----BEGIN...` y `-----END...`.

> Si no tienes ese archivo, genera un par nuevo con
> `ssh-keygen -t ed25519 -C "deploy-github"` y sube el `.pub` a hPanel →
> Acceso SSH → Administrar claves SSH.

### 2. Guardar los secretos en GitHub

GitHub → `impulso-redesign` → **Settings → Secrets and variables → Actions** →
**New repository secret**. Crea estos cuatro, con el nombre exacto:

| Nombre | Valor |
|---|---|
| `SSH_HOST` | la IP del servidor |
| `SSH_PORT` | `65002` |
| `SSH_USER` | `u679759757` |
| `SSH_PRIVATE_KEY` | el contenido completo de la clave privada |
| `SSH_TARGET_DIR` | `/home/u679759757/domains/impulsoestrategicocx.com/public_html` |

Confirma la ruta exacta de `SSH_TARGET_DIR` en el Administrador de archivos:
es la ruta que muestra al estar dentro de `public_html`.

### 3. Desconectar el Git de Hostinger

Si dejas activo el repositorio en hPanel → GIT **y** este workflow, los dos
escriben en `public_html` y se pisan. Elige uno.

Para usar el camino B: hPanel → GIT → menú de tres puntos → **Delete**. Eso solo
borra la conexión, no los archivos.

### 4. Probar

GitHub → pestaña **Actions** → *Deploy a Hostinger* → **Run workflow**. Debería
terminar en verde y el último paso confirma que la home responde 200.

---

## Qué NUNCA se sobrescribe

El workflow excluye explícitamente `api/config.php`. Tu API key de Resend vive
solo en el servidor y ningún deploy la toca. También excluye los `.md`,
`publicar.sh`, `uploads/` y `screenshots/`, porque no son parte del sitio.

## Cuando cambie el CSS o el JS

Recuerda subir el número de versión en las 18 páginas (`?v=20260815`). El
`.htaccess` cachea esos archivos un mes; sin cambiar la versión, los visitantes
que ya estuvieron en el sitio seguirán viendo la hoja antigua.

---

## El tramo que no se puede automatizar

Mis cambios llegan a GitHub solo con una acción tuya: mi acceso al repositorio es
de lectura, no de escritura. Lo más cómodo es **GitHub Desktop**:

1. Instálalo desde `desktop.github.com` y abre el repositorio clonado.
2. Cuando te entregue archivos nuevos, reemplaza el contenido de esa carpeta.
3. GitHub Desktop lista los cambios: escribes un mensaje y pulsas
   **Commit** y luego **Push origin**.

Dos clics, sin terminal. Y con el deploy automático configurado, el sitio se
actualiza solo desde ahí.
