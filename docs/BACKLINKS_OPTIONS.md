# Opciones para Análisis de Backlinks

## Resumen de APIs Disponibles

### 🆓 Opciones Gratuitas

#### 1. Google Search Console API
- **Costo**: Gratis
- **Limitaciones**: 
  - Solo muestra backlinks que Google conoce
  - Datos limitados (no incluye dofollow/nofollow, anchor text completo)
  - Requiere autenticación OAuth
- **Datos disponibles**:
  - Dominios que enlazan a tu sitio
  - Páginas más enlazadas
  - Tendencias de backlinks
- **Implementación**: Media complejidad (OAuth)

#### 2. OpenPageRank API
- **Costo**: Gratis (con límites)
- **Limitaciones**: 
  - Solo proporciona PageRank, no lista de backlinks
  - Límite de ~100 consultas/día
- **Datos disponibles**: PageRank de dominios
- **Implementación**: Fácil

#### 3. Scraping Básico (Google "link:")
- **Costo**: Gratis
- **Limitaciones**: 
  - Google bloquea muchas consultas automatizadas
  - Resultados incompletos
  - Puede violar términos de servicio
- **Datos disponibles**: Algunos backlinks visibles
- **Implementación**: Media complejidad
- **⚠️ No recomendado para producción**

### 💰 Opciones de Pago

#### 1. Ahrefs API
- **Costo**: Desde $99/mes
- **Datos completos**: Backlinks, dofollow/nofollow, anchor text, autoridad, historial
- **Calidad**: Excelente

#### 2. SEMrush API
- **Costo**: Desde $119/mes
- **Datos completos**: Backlinks, autoridad, tipos de enlace
- **Calidad**: Excelente

#### 3. Moz API
- **Costo**: Desde $99/mes
- **Datos completos**: Backlinks, Domain Authority, tipos de enlace
- **Calidad**: Buena

#### 4. Majestic API
- **Costo**: Desde $49/mes
- **Datos completos**: Backlinks, Trust Flow, Citation Flow
- **Calidad**: Buena

## Recomendación para Implementación

### Opción Recomendada: **Híbrida (GSC + Manual)**

1. **Google Search Console** (gratis)
   - Obtener backlinks conocidos por Google
   - Mostrar dominios que enlazan
   - Tendencias básicas

2. **Formulario Manual**
   - Permitir agregar backlinks encontrados manualmente
   - Usar herramientas gratuitas externas (Ahrefs free checker, etc.)
   - Importar desde CSV

3. **Preparar para API de Pago**
   - Estructura lista para Ahrefs/SEMrush
   - Implementar con datos mock primero
   - Activar API cuando haya presupuesto

## Estructura de Base de Datos Propuesta

```sql
CREATE TABLE backlinks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    site_id BIGINT NOT NULL,
    source_domain VARCHAR(255) NOT NULL,
    source_url TEXT NOT NULL,
    target_url TEXT NOT NULL,
    anchor_text TEXT,
    link_type ENUM('dofollow', 'nofollow', 'sponsored', 'ugc') DEFAULT 'dofollow',
    first_seen DATE,
    last_seen DATE,
    domain_authority INT,
    page_authority INT,
    source_type ENUM('gsc', 'manual', 'api_ahrefs', 'api_semrush', 'api_moz') DEFAULT 'manual',
    is_toxic BOOLEAN DEFAULT FALSE,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_site_id (site_id),
    INDEX idx_source_domain (source_domain),
    INDEX idx_is_toxic (is_toxic)
);
```

## Plan de Implementación Sugerido

### Fase 1: Básico (Gratis)
- Integración con Google Search Console
- Tabla de backlinks
- Dashboard básico
- Formulario manual para agregar backlinks

### Fase 2: Mejoras
- Detección de backlinks tóxicos (heurística básica)
- Importación desde CSV
- Exportación a Excel
- Alertas de nuevos backlinks

### Fase 3: Avanzado (Requiere API de Pago)
- Integración con Ahrefs/SEMrush
- Datos completos (dofollow/nofollow, anchor text, autoridad)
- Análisis de competidores
- Link building opportunities

