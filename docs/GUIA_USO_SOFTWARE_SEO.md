# 📚 Guía Completa de Uso del Software SEO

## 🎯 Introducción

Este software te permite gestionar múltiples sitios web, realizar auditorías SEO, seguir keywords, analizar competencia, generar reportes y recibir alertas automáticas. Todo desde un solo panel centralizado.

---

## 🚀 PASO 1: Configuración Inicial

### 1.1 Agregar un Sitio Web

1. Ve a **SEO → Sitios Web → Nuevo Sitio**
2. Completa el formulario:
   - **Nombre**: Nombre descriptivo (ej: "Mi Empresa")
   - **Dominio Base**: Dominio sin http:// (ej: `ejemplo.com`)
   - **GSC Property**: URL de la propiedad en Google Search Console (ej: `https://ejemplo.com`)
   - **Credenciales JSON**: Copia y pega el contenido del archivo JSON de credenciales de Google Search Console
3. Haz clic en **Guardar**

### 1.2 Obtener Credenciales de Google Search Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un proyecto o selecciona uno existente
3. Habilita la API de "Google Search Console API"
4. Crea una cuenta de servicio:
   - Ve a "IAM & Admin" → "Service Accounts"
   - Crea una nueva cuenta de servicio
   - Descarga el archivo JSON de credenciales
5. Comparte la propiedad en Google Search Console con el email de la cuenta de servicio
6. Copia el contenido del JSON y pégalo en el campo "Credenciales JSON" del sitio

---

## 📊 PASO 2: Sincronizar Métricas desde Google Search Console

### 2.1 Sincronización Manual

1. Ve al detalle del sitio (haz clic en el nombre del sitio)
2. Haz clic en **Sincronizar Métricas**
3. Selecciona el número de días a sincronizar (ej: 30 días)
4. Haz clic en **Sincronizar**
5. La sincronización se ejecuta en segundo plano (puedes cerrar la ventana)

### 2.2 Sincronización Automática

- El sistema sincroniza automáticamente las métricas diariamente a las 2:00 AM
- No necesitas hacer nada, solo esperar a que se complete

### 2.3 Ver Métricas Sincronizadas

1. Ve al **Dashboard SEO** del sitio
2. Verás gráficos de:
   - Clics e Impresiones (últimos 30 días)
   - CTR promedio
   - Posición promedio
   - Top 10 URLs con más clics
   - Top 10 Keywords con más clics

---

## 🔍 PASO 3: Investigación de Keywords

### 3.1 Buscar Keywords desde Google Search Console

1. Ve a **SEO → Keywords → Investigación de Keywords**
2. Selecciona un sitio en el filtro
3. En la sección "Desde Google Search Console", haz clic en **Buscar desde GSC**
4. El sistema encontrará keywords que ya rankean para tu sitio pero no están en tu tracking
5. Las keywords se mostrarán en la tabla con:
   - Intención detectada automáticamente
   - Cluster/Tema asignado automáticamente
   - Posición actual
   - Clics e impresiones

### 3.2 Buscar Sugerencias de Google Autocomplete

1. En la misma página, en la sección "Desde Google Autocomplete"
2. Ingresa una "Keyword Semilla" (ej: "hoteles en lima")
3. Selecciona el sitio
4. Haz clic en **Buscar Sugerencias**
5. El sistema te mostrará keywords relacionadas con:
   - Intención estimada
   - Cluster asignado
   - Dificultad estimada

### 3.3 Agrupar Keywords en Clusters

1. Después de buscar keywords, haz clic en **Asignar Clusters**
2. El sistema agrupará automáticamente las keywords por tema
3. Para ver los clusters, haz clic en **Ver Clusters**
4. Verás todas las keywords agrupadas por tema con estadísticas

### 3.4 Agregar Keywords al Tracking

1. En la tabla de investigación, busca la keyword que quieres trackear
2. Haz clic en el botón **+** (verde) en la columna "Acciones"
3. La keyword se agregará automáticamente a tu lista de tracking principal
4. Se marcará como "Trackeada"

---

## 📈 PASO 4: Tracking de Keywords

### 4.1 Agregar Keywords Manualmente

1. Ve a **SEO → Keywords → Nueva Keyword**
2. Selecciona el sitio
3. Ingresa la keyword a seguir
4. Opcional: Ingresa URL objetivo
5. Haz clic en **Guardar**

### 4.2 Ver Dashboard de Keyword

1. Ve a **SEO → Keywords → Lista de Keywords**
2. Haz clic en el nombre de una keyword
3. Verás:
   - Gráfico de evolución de posición (últimos 30 días)
   - Posición actual vs anterior
   - Cambio vs ayer y vs hace 7 días

### 4.3 Actualizar Posiciones

1. Ve a la lista de keywords
2. Haz clic en **Actualizar Posiciones**
3. El sistema buscará las posiciones desde las métricas de GSC
4. Se actualizarán automáticamente todas las keywords activas

---

## 🔎 PASO 5: Auditorías SEO On-Page

### 5.1 Ejecutar una Auditoría

1. Ve al detalle del sitio
2. Haz clic en **Ejecutar Auditoría**
3. Ingresa la URL completa a auditar (ej: `https://ejemplo.com/pagina`)
4. Haz clic en **Ejecutar Auditoría**
5. La auditoría se ejecuta en segundo plano (puedes cerrar la ventana)

### 5.2 Ver Resultados de Auditoría

1. Ve a **Historial de Auditorías** desde el sitio
2. Haz clic en **Ver Detalles** de la auditoría deseada
3. Verás:
   - **Score SEO** (0-100)
   - **Análisis On-Page**:
     - Title y Meta Description
     - Encabezados (H1, H2, H3)
     - Imágenes sin ALT
     - Links (internos, externos, rotos)
     - Canonical y Robots Meta
     - TTFB (tiempo de respuesta)
   - **Análisis de Contenido**:
     - Conteo de palabras
     - Densidad de keywords (top 10)
     - Score de legibilidad
     - Sugerencias de mejora
   - **Errores y Advertencias**

### 5.3 Ver Detalles de Links

1. En los detalles de auditoría, ve a las pestañas:
   - **Links Internos**: Lista completa con DataTables
   - **Links Externos**: Lista completa con DataTables
   - **Links Rotos**: Lista con status codes
2. Puedes exportar cada lista a Excel con el botón correspondiente

### 5.4 Nota Importante

- Las auditorías con muchos links se ejecutan en segundo plano
- La verificación de links rotos puede tardar varios minutos
- Puedes cerrar la ventana y seguir trabajando

---

## 👥 PASO 6: Análisis de Competencia

### 6.1 Agregar un Competidor

1. Ve a **SEO → Competencia → Nuevo Competidor**
2. Selecciona el sitio para el cual es competidor
3. Ingresa:
   - **Nombre**: Nombre del competidor
   - **Dominio Base**: Dominio del competidor (ej: `competidor.com`)
4. Haz clic en **Guardar**
   - **Nota**: No necesitas credenciales GSC del competidor

### 6.2 Dashboard de Competencia

1. Ve al detalle del sitio
2. Haz clic en **Análisis de Competencia**
3. Selecciona un competidor del dropdown
4. Verás:
   - **Estadísticas**: Total keywords, keywords donde estás mejor, keywords donde el competidor está mejor
   - **Tabla de Comparación**: Todas las keywords con posiciones tuyas vs del competidor
   - **Gaps Identificados**: Keywords donde el competidor está mejor posicionado

### 6.3 Ingresar Posiciones del Competidor

1. En el dashboard de competencia, ve a la sección "Ingresar/Actualizar Posiciones"
2. Para cada keyword, ingresa manualmente la posición del competidor
3. El sistema calculará automáticamente el gap (diferencia de posiciones)
4. Haz clic en **Guardar Posiciones**

### 6.4 Obtener Posiciones del Competidor

Puedes obtener las posiciones usando:
- **Herramientas SEO**: Ahrefs, SEMrush, etc.
- **Búsquedas manuales**: Busca la keyword en Google y verifica la posición del competidor
- **Google Search Console del competidor**: Si tienes acceso

---

## ✅ PASO 7: Planificador de Tareas SEO

### 7.1 Vista Kanban

1. Ve a **SEO → Tareas SEO**
2. Verás un tablero Kanban con 3 columnas:
   - **Pendiente**: Tareas por hacer
   - **En Progreso**: Tareas en trabajo
   - **Completadas**: Tareas finalizadas

### 7.2 Crear una Tarea Manualmente

1. Haz clic en **Nueva Tarea**
2. Completa el formulario:
   - **Sitio**: Sitio relacionado
   - **Título**: Nombre de la tarea
   - **Descripción**: Detalles de la tarea
   - **Prioridad**: Baja, Media, Alta, Crítica
   - **Asignado a**: Usuario responsable
   - **Fecha límite**: Fecha de vencimiento
3. Haz clic en **Guardar**

### 7.3 Mover Tareas en Kanban

Puedes mover tareas entre columnas de dos formas:
1. **Arrastrar y soltar**: Usa el mouse para arrastrar la tarjeta
2. **Botones**: Usa los botones de acción en cada tarjeta

### 7.4 Tareas Automáticas

El sistema crea tareas automáticamente cuando:
- Se detectan errores críticos en auditorías (ej: página sin título, sin H1)
- Se encuentran imágenes sin ALT
- El score SEO es bajo (< 70)
- Se detectan problemas de contenido (ej: contenido muy corto, baja legibilidad)

---

## 🔔 PASO 8: Alertas SEO

### 8.1 Ver Alertas

1. Ve a **SEO → Alertas SEO**
2. Verás todas las alertas con filtros:
   - Por sitio
   - Por tipo (posición, tráfico, error, contenido, técnica, rendimiento)
   - Por severidad (info, advertencia, crítica)
   - Por estado (leídas/no leídas)

### 8.2 Tipos de Alertas

- **Posición**: Cambios significativos en el ranking de tus keywords
- **Tráfico**: Caídas o aumentos importantes en clics o impresiones
- **Error SEO**: Problemas detectados en auditorías
- **Contenido**: Problemas de contenido (páginas cortas, baja legibilidad)
- **Técnica**: Problemas con sitemap.xml o robots.txt
- **Rendimiento**: Cambios en el score SEO o TTFB

### 8.3 Gestionar Alertas

1. **Marcar como leída**: Haz clic en el ícono de ojo 👁️
2. **Marcar como resuelta**: Haz clic en el ícono de check ✓
3. **Marcar todas como leídas**: Botón en la parte superior

### 8.4 Detección Automática

El sistema ejecuta tareas programadas diariamente para detectar:
- Cambios de posición de keywords (a las 3:00 AM)
- Caídas o aumentos de tráfico (a las 3:00 AM)
- Validación de sitemap/robots (cada lunes a las 4:00 AM)

---

## 🛠️ PASO 9: Validación Técnica

### 9.1 Validar Sitemap y Robots.txt

1. Ve al detalle del sitio
2. Haz clic en **Validar Sitemap/Robots**
3. El sistema verificará:
   - **sitemap.xml**: Existencia y validez del XML
   - **robots.txt**: Existencia y contenido
4. Los resultados se mostrarán como alertas SEO

### 9.2 Ver Resultados

1. Ve a **SEO → Alertas SEO**
2. Filtra por tipo "Técnica"
3. Verás las alertas generadas por la validación

---

## 📄 PASO 10: Generar Reportes

### 10.1 Reporte de Sitio (PDF)

1. Ve al detalle del sitio
2. Haz clic en **Reporte PDF**
3. El PDF incluirá:
   - Información general del sitio
   - Métricas de rendimiento
   - Top URLs y Keywords
   - Resumen de auditorías
   - Gráficos de evolución

### 10.2 Reporte de Auditoría (PDF)

1. Ve a los detalles de una auditoría
2. Haz clic en **Exportar PDF**
3. El reporte incluirá:
   - Score SEO
   - Análisis On-Page completo
   - Análisis de Links
   - Análisis de Contenido
   - Errores y advertencias

### 10.3 Exportar Links a Excel

1. En los detalles de auditoría, ve a las pestañas de links
2. Haz clic en **Exportar a Excel**
3. Se descargará un archivo Excel con:
   - URL
   - Texto del Link
   - Href Original
   - Status Code (para links rotos)

---

## 🔄 Flujo de Trabajo Recomendado

### Rutina Diaria (Auxiliar SEO)

#### 🌅 Mañana (9:00 AM - 10:00 AM)
1. **Revisar Alertas**: Ve a Alertas SEO y revisa las no leídas
2. **Revisar Métricas**: Ve al Dashboard SEO de cada sitio activo
3. **Priorizar Tareas**: Revisa el Kanban y mueve tareas según prioridad

#### 🔍 Investigación (10:00 AM - 12:00 PM)
1. **Buscar Keywords**: Usa Investigación de Keywords para encontrar nuevas oportunidades
2. **Asignar Clusters**: Agrupa las keywords encontradas
3. **Agregar al Tracking**: Agrega las keywords más prometedoras al tracking

#### 🛠️ Optimización (2:00 PM - 4:00 PM)
1. **Ejecutar Auditorías**: Audita páginas nuevas o actualizadas
2. **Revisar Errores**: Ve los detalles de auditorías y corrige errores críticos
3. **Validar Técnico**: Ejecuta validación de sitemap/robots si es necesario

#### 📊 Seguimiento (4:00 PM - 5:00 PM)
1. **Actualizar Posiciones**: Actualiza las posiciones de keywords
2. **Revisar Competencia**: Compara tus posiciones con competidores
3. **Generar Reportes**: Genera reportes para enviar a clientes o stakeholders

---

## 💡 Consejos y Mejores Prácticas

### Keywords
- **Prioriza keywords con buena intención**: Transaccional > Comercial > Informativa
- **Agrupa keywords relacionadas**: Usa clusters para organizar contenido
- **Actualiza posiciones semanalmente**: Para detectar cambios importantes

### Auditorías
- **Audita páginas nuevas inmediatamente**: Antes de publicar
- **Reaudita después de cambios**: Para verificar mejoras
- **Revisa links rotos mensualmente**: Especialmente en sitios grandes

### Competencia
- **Actualiza posiciones de competidores mensualmente**: Para detectar gaps
- **Enfócate en keywords donde el competidor está mejor**: Oportunidades de mejora

### Tareas
- **Revisa tareas automáticas diariamente**: El sistema crea tareas desde auditorías
- **Marca tareas completadas**: Para mantener el Kanban organizado
- **Asigna fechas límite realistas**: Para evitar sobrecarga

### Alertas
- **Revisa alertas críticas inmediatamente**: Pérdidas de posición > 5 posiciones
- **Marca alertas resueltas**: Para mantener el panel limpio
- **Usa filtros**: Para encontrar alertas específicas rápidamente

---

## 🎯 Casos de Uso Comunes

### Caso 1: Nuevo Sitio Web
1. Agregar sitio → Configurar GSC → Sincronizar métricas
2. Buscar keywords desde GSC → Agrupar en clusters
3. Agregar keywords importantes al tracking
4. Ejecutar auditorías de páginas principales
5. Corregir errores críticos encontrados

### Caso 2: Optimizar Página Existente
1. Ejecutar auditoría de la página
2. Revisar score SEO y errores
3. Crear tareas desde errores encontrados
4. Trabajar en las tareas (Kanban)
5. Reauditar después de cambios
6. Comparar scores antes/después

### Caso 3: Investigar Nuevas Keywords
1. Usar Google Autocomplete con keyword semilla
2. Revisar intenciones y dificultades
3. Agrupar en clusters
4. Agregar keywords prometedoras al tracking
5. Crear contenido optimizado para esas keywords

### Caso 4: Análisis de Competencia
1. Agregar competidores
2. Ingresar posiciones del competidor para keywords importantes
3. Identificar gaps (keywords donde el competidor está mejor)
4. Priorizar keywords con mayor gap
5. Crear estrategia de contenido para esas keywords

---

## ⚙️ Configuración Avanzada

### Cron Jobs (Tareas Programadas)

El sistema ejecuta automáticamente:
- **Diario a las 2:00 AM**: Sincronización de métricas de GSC
- **Diario a las 3:00 AM**: Detección de alertas (posición, tráfico)
- **Lunes a las 4:00 AM**: Validación de sitemap/robots

Asegúrate de tener configurado el cron job en tu servidor:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Permisos

El sistema usa roles y permisos. Asegúrate de tener los permisos necesarios:
- `admin.sites.*`: Gestión de sitios
- `admin.keywords.*`: Gestión de keywords
- `admin.audits.*`: Gestión de auditorías
- `admin.alerts.*`: Gestión de alertas
- etc.

---

## 🆘 Solución de Problemas

### Error: "No se pueden sincronizar métricas"
- Verifica que las credenciales GSC sean válidas
- Asegúrate de que la cuenta de servicio tenga acceso a la propiedad
- Revisa los logs en `storage/logs/laravel.log`

### Error: "Auditoría falla"
- Verifica que la URL sea accesible
- Revisa que el sitio no bloquee bots
- Revisa los logs para más detalles

### Error: "Keywords no se actualizan"
- Asegúrate de tener métricas sincronizadas de GSC
- Verifica que las keywords existan en las métricas
- Revisa que las keywords estén activas

---

## 📞 Soporte

Para más ayuda o reportar problemas, contacta al administrador del sistema.

---

**¡Listo! Ahora ya sabes cómo usar todas las funcionalidades del software SEO. 🚀**

