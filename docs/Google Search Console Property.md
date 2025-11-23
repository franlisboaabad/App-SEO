✅ 1. Cómo obtener tu “Google Search Console Property”

La propiedad es simplemente tu sitio web registrado en Search Console.

👉 Paso 1: Entra a Search Console

https://search.google.com/search-console

👉 Paso 2: En el panel izquierdo, verás tus sitios registrados

Ejemplos de propiedades:

https://midominio.com/

https://www.midominio.com/

https://blog.midominio.com/

👉 Paso 3: El valor que necesitas es exactamente el que aparece allí

Ese string lo usarás así en Laravel:

APP_SITE_URL="https://midominio.com/"


⚠️ Usa el dominio EXACTO, con https y slash final, igualito a como aparece en Search Console.

Si aún no tienes tu sitio agregado:

Clic en Agregar propiedad

Elige Prefijo URL

Verifica (archivo HTML, DNS, Google Tag, etc.)

✅ 2. Cómo obtener el archivo JSON de credenciales (Service Account)

Este archivo JSON es lo que permitirá que tu software Laravel acceda a Search Console sin necesidad de iniciar sesión.

🟢 Paso 1: Ir a Google Cloud Console

https://console.cloud.google.com/

🟢 Paso 2: Crear un nuevo proyecto

Esquina superior izquierda → Selecciona proyecto

Clic en Nuevo proyecto

Ponle el nombre:
SEO Tool

Crear

🟢 Paso 3: Habilitar la API de Search Console

Menú izquierdo → API & Services

Enable APIs and Services

Busca: Search Console API

Activar

🟢 Paso 4: Crear Service Account

Menú izquierdo: IAM & Admin

Service Accounts

Clic en Create Service Account

Nombre:
seo-dashboard-service

Crear y continuar (no hace falta rol)

Cuando termine, verás tu service account con formato:

xxxxxxx@xxxxxx.iam.gserviceaccount.com

🟢 Paso 5: Crear la credencial JSON

En la misma pantalla:

En la fila de tu service account → clic en los tres puntos

Manage keys

Add key → Create new key

Tipo: JSON

Se descargará un archivo como:

seo-dashboard-fg54321a2123.json


Ese archivo es tu credencial JSON.

🟢 Paso 6: Darle acceso al Search Console

Este paso es CRÍTICO.
Si no lo haces, la API devolverá “no autorizado”.

Entra a Google Search Console

ve a la propiedad

Menú → Configuración

Usuarios y permisos

“Agregar usuario”

Pega el email del Service Account:

xxxxxx@xxxxxx.iam.gserviceaccount.com


Permisos → Total (Full)

🟢 Paso 7: Colocar el JSON en tu proyecto Laravel

Guárdalo aquí:

storage/app/google/service-account.json


Y en .env:

GOOGLE_APPLICATION_CREDENTIALS="storage/app/google/service-account.json"
