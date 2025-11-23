# Sistema de Alertas SEO - Requerimientos

## 📋 Tipos de Alertas que Podemos Implementar

### 1. **Alertas de Posiciones (Position Alerts)**
- **Cuándo**: Cuando una URL/keyword pierde posiciones significativas
- **Ejemplo**: "La keyword 'hoteles en lima' bajó de posición 5 a 15"
- **Umbral**: Configurable (ej: pérdida de más de 5 posiciones)

### 2. **Alertas de Tráfico (Traffic Alerts)**
- **Cuándo**: Cuando hay caídas significativas en clics o impresiones
- **Ejemplo**: "Los clics bajaron 30% en los últimos 7 días"
- **Umbral**: Configurable (ej: caída de más del 20%)

### 3. **Alertas de Errores SEO (SEO Error Alerts)**
- **Cuándo**: Cuando una auditoría detecta nuevos errores críticos
- **Ejemplo**: "Nuevo error detectado: página sin título"
- **Tipos**: Errores críticos, advertencias, links rotos

### 4. **Alertas de Indexación (Indexing Alerts)**
- **Cuándo**: Cuando una URL desaparece de los resultados o cambia de estado
- **Ejemplo**: "La URL /productos ya no aparece en los resultados"

### 5. **Alertas de Performance (Performance Alerts)**
- **Cuándo**: Cuando el TTFB o score SEO empeora
- **Ejemplo**: "El TTFB aumentó a 2.5s (antes 0.8s)"

---

## 🗄️ Base de Datos - Tablas Necesarias

### 1. **Tabla: `seo_alerts`**
```sql
- id
- site_id (foreign key)
- type (enum: position, traffic, error, indexing, performance)
- severity (enum: info, warning, critical)
- title
- message
- url (opcional, para alertas de URL específica)
- keyword (opcional, para alertas de keyword)
- metadata (JSON - datos adicionales)
- is_read (boolean)
- resolved_at (timestamp nullable)
- created_at
- updated_at
```

### 2. **Tabla: `alert_rules`** (Opcional - para configurar reglas)
```sql
- id
- site_id (nullable - para reglas globales o por sitio)
- type
- condition (JSON - condiciones)
- threshold (valor umbral)
- is_active (boolean)
- created_at
- updated_at
```

### 3. **Tabla: `alert_subscriptions`** (Opcional - para suscripciones de usuarios)
```sql
- id
- user_id
- site_id (nullable)
- alert_types (JSON - tipos de alertas a recibir)
- notification_channels (JSON - email, in-app, etc)
- is_active
- created_at
- updated_at
```

---

## 📦 Componentes a Crear

### 1. **Modelos**
- `SeoAlert` - Modelo para las alertas
- `AlertRule` - Modelo para reglas de alertas (opcional)
- `AlertSubscription` - Modelo para suscripciones (opcional)

### 2. **Servicios**
- `AlertService` - Lógica principal para crear y gestionar alertas
- `PositionAlertService` - Detecta cambios de posiciones
- `TrafficAlertService` - Detecta cambios de tráfico
- `ErrorAlertService` - Detecta nuevos errores SEO

### 3. **Jobs (Trabajos en Cola)**
- `CheckPositionAlerts` - Compara posiciones actuales vs anteriores
- `CheckTrafficAlerts` - Compara tráfico actual vs anterior
- `CheckErrorAlerts` - Compara errores de auditorías
- `SendAlertNotifications` - Envía notificaciones (email, in-app)

### 4. **Notificaciones Laravel**
- `PositionAlertNotification` - Notificación de cambio de posición
- `TrafficAlertNotification` - Notificación de cambio de tráfico
- `ErrorAlertNotification` - Notificación de error SEO
- `AlertSummaryNotification` - Resumen diario/semanal de alertas

### 5. **Comandos Artisan**
- `seo:check-alerts` - Ejecuta todas las verificaciones de alertas
- `seo:send-alert-summary` - Envía resumen de alertas

### 6. **Controladores y Vistas**
- `AlertController` - CRUD de alertas
- Vista: Lista de alertas
- Vista: Detalles de alerta
- Vista: Configuración de reglas (opcional)
- Componente: Badge de alertas no leídas en el header

---

## 🔄 Flujo de Funcionamiento

### **Flujo 1: Detección de Cambio de Posición**
```
1. Job diario: CheckPositionAlerts
2. Obtiene métricas de hoy vs ayer (o período anterior)
3. Compara posiciones por URL/keyword
4. Si hay pérdida > umbral → crea SeoAlert
5. Dispara SendAlertNotifications
6. Usuario recibe notificación (email/in-app)
```

### **Flujo 2: Detección de Nuevo Error**
```
1. Se completa una auditoría SEO
2. Se compara con la auditoría anterior de la misma URL
3. Si hay nuevos errores → crea SeoAlert
4. Dispara SendAlertNotifications
5. Usuario recibe notificación
```

### **Flujo 3: Detección de Caída de Tráfico**
```
1. Job diario: CheckTrafficAlerts
2. Compara clics/impresiones últimos 7 días vs 7 días anteriores
3. Si hay caída > umbral → crea SeoAlert
4. Dispara SendAlertNotifications
5. Usuario recibe notificación
```

---

## 🎨 Interfaz de Usuario

### **1. Badge de Alertas en Header**
- Icono de campana con contador de alertas no leídas
- Dropdown con últimas alertas
- Link a página completa de alertas

### **2. Página de Alertas**
- Filtros: por sitio, tipo, severidad, fecha
- Tabla con todas las alertas
- Acciones: marcar como leída, resolver, ver detalles
- Gráficos de alertas por tipo/tiempo

### **3. Configuración de Alertas** (Opcional)
- Formulario para crear reglas personalizadas
- Configurar umbrales
- Activar/desactivar tipos de alertas

---

## ⚙️ Configuración Necesaria

### **1. Variables de Entorno (.env)**
```env
# Alertas
ALERTS_ENABLED=true
ALERT_POSITION_THRESHOLD=5  # Posiciones perdidas
ALERT_TRAFFIC_THRESHOLD=20  # Porcentaje de caída
ALERT_EMAIL_ENABLED=true
```

### **2. Scheduler (app/Console/Kernel.php)**
```php
// Verificar alertas diariamente
$schedule->command('seo:check-alerts')->dailyAt('08:00');

// Enviar resumen semanal
$schedule->command('seo:send-alert-summary')->weekly();
```

---

## 📧 Notificaciones

### **Canales de Notificación**
1. **In-App**: Notificaciones en el panel (usando Laravel Notifications)
2. **Email**: Envío de emails con detalles de alerta
3. **Database**: Guardar en tabla `notifications` (Laravel)

### **Templates de Email**
- Email de alerta individual
- Email de resumen diario/semanal
- Email de alerta crítica (urgente)

---

## 🔍 Ejemplo de Implementación Mínima

### **Paso 1: Migración**
```php
Schema::create('seo_alerts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('site_id')->constrained();
    $table->enum('type', ['position', 'traffic', 'error', 'indexing', 'performance']);
    $table->enum('severity', ['info', 'warning', 'critical']);
    $table->string('title');
    $table->text('message');
    $table->string('url')->nullable();
    $table->string('keyword')->nullable();
    $table->json('metadata')->nullable();
    $table->boolean('is_read')->default(false);
    $table->timestamp('resolved_at')->nullable();
    $table->timestamps();
});
```

### **Paso 2: Modelo**
```php
class SeoAlert extends Model {
    protected $fillable = ['site_id', 'type', 'severity', 'title', 'message', ...];
    
    public function site() {
        return $this->belongsTo(Site::class);
    }
}
```

### **Paso 3: Servicio**
```php
class AlertService {
    public function createPositionAlert($site, $url, $keyword, $oldPosition, $newPosition) {
        SeoAlert::create([
            'site_id' => $site->id,
            'type' => 'position',
            'severity' => $this->calculateSeverity($oldPosition, $newPosition),
            'title' => "Pérdida de posición: {$keyword}",
            'message' => "La keyword '{$keyword}' bajó de posición {$oldPosition} a {$newPosition}",
            'url' => $url,
            'keyword' => $keyword,
            'metadata' => ['old_position' => $oldPosition, 'new_position' => $newPosition]
        ]);
    }
}
```

---

## 📊 Complejidad de Implementación

### **Versión Mínima (MVP)**
- ✅ Tabla `seo_alerts`
- ✅ Modelo `SeoAlert`
- ✅ Servicio básico `AlertService`
- ✅ Job para detectar cambios de posición
- ✅ Vista simple de alertas
- ✅ Badge en header
- ⏱️ **Tiempo estimado: 4-6 horas**

### **Versión Completa**
- ✅ Todo lo del MVP
- ✅ Sistema de reglas configurables
- ✅ Múltiples tipos de alertas
- ✅ Notificaciones por email
- ✅ Dashboard de alertas
- ✅ Resúmenes automáticos
- ⏱️ **Tiempo estimado: 12-16 horas**

---

## 🎯 Prioridades Sugeridas

1. **Alta Prioridad**: Alertas de posiciones (más útil)
2. **Media Prioridad**: Alertas de errores SEO
3. **Baja Prioridad**: Alertas de tráfico, indexación, performance

---

## 💡 Consideraciones Adicionales

- **Rate Limiting**: Evitar spam de alertas (máximo X alertas por día)
- **Agrupación**: Agrupar alertas similares
- **Historial**: Mantener historial de alertas resueltas
- **Exportación**: Exportar alertas a PDF/Excel
- **API**: Endpoint para consultar alertas (si se necesita integración)

