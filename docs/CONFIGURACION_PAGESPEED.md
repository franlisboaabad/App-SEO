# 🚀 Configuración de PageSpeed Insights API

## 📋 Requisitos

Para usar el análisis de velocidad con PageSpeed Insights, necesitas:

1. **API Key de Google PageSpeed Insights** (GRATIS)
2. Configurar la API key en tu archivo `.env`

---

## 🔑 Obtener API Key

### Paso 1: Ir a Google Cloud Console
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un proyecto nuevo o selecciona uno existente

### Paso 2: Habilitar PageSpeed Insights API
1. Ve a **APIs & Services** → **Library**
2. Busca "**PageSpeed Insights API**"
3. Haz clic en **Enable**

### Paso 3: Crear API Key
1. Ve a **APIs & Services** → **Credentials**
2. Haz clic en **Create Credentials** → **API Key**
3. Copia la API key generada

### Paso 4: (Opcional) Restringir API Key
Para mayor seguridad, puedes restringir la API key:
- **Application restrictions**: Restringe por IP o HTTP referrer
- **API restrictions**: Limita solo a "PageSpeed Insights API"

---

## ⚙️ Configurar en Laravel

### 1. Agregar al archivo `.env`

Abre tu archivo `.env` y agrega:

```env
PAGESPEED_INSIGHTS_API_KEY=tu_api_key_aqui
```

**Ejemplo:**
```env
PAGESPEED_INSIGHTS_API_KEY=AIzaSyBxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 2. Verificar configuración

El sistema ya está configurado en `config/services.php`:

```php
'pagespeed' => [
    'api_key' => env('PAGESPEED_INSIGHTS_API_KEY'),
],
```

### 3. Limpiar caché de configuración

Después de agregar la API key, ejecuta:

```bash
php artisan config:clear
```

---

## 📊 Límites de la API

### Cuota Gratuita
- **25,000 requests por día** (más que suficiente para uso normal)
- Sin costo

### Si excedes el límite
- La API devolverá un error
- El sistema continuará funcionando, pero sin datos de PageSpeed Insights
- La auditoría SEO seguirá funcionando normalmente

---

## ✅ Verificar que Funciona

1. **Ejecuta una auditoría** de cualquier URL
2. **Ve a los detalles** de la auditoría
3. **Busca la sección** "Análisis de Velocidad (PageSpeed Insights)"
4. Si ves los scores y métricas, **¡está funcionando!**

---

## 🔧 Solución de Problemas

### Error: "PageSpeed Insights API key no configurada"
- Verifica que agregaste `PAGESPEED_INSIGHTS_API_KEY` en tu `.env`
- Ejecuta `php artisan config:clear`
- Reinicia el servidor si es necesario

### Error: "API key not valid"
- Verifica que la API key sea correcta
- Asegúrate de que la API esté habilitada en Google Cloud Console
- Verifica que no hayas restringido la API key demasiado

### No aparecen métricas en la auditoría
- Verifica que la API key esté configurada correctamente
- Revisa los logs en `storage/logs/laravel.log`
- La API puede tardar 10-30 segundos en responder

### La auditoría funciona pero sin PageSpeed Insights
- Esto es normal si la API key no está configurada
- La auditoría SEO seguirá funcionando normalmente
- Solo faltarán los datos de velocidad

---

## 💡 Notas Importantes

1. **La API es gratuita** pero tiene límites de cuota
2. **No es obligatorio** - La auditoría SEO funciona sin PageSpeed Insights
3. **Puede tardar** - Cada análisis de PageSpeed Insights tarda 10-30 segundos
4. **Se ejecuta en segundo plano** - No bloquea la auditoría principal

---

## 📚 Recursos

- [Documentación oficial de PageSpeed Insights API](https://developers.google.com/speed/docs/insights/v5/get-started)
- [Google Cloud Console](https://console.cloud.google.com/)
- [Límites y cuotas](https://developers.google.com/speed/docs/insights/v5/quotas)

---

**¡Listo! Ahora puedes analizar la velocidad de tus páginas con PageSpeed Insights. 🚀**

