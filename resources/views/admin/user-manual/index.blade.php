@extends('adminlte::page')

@section('title', 'Manual de Usuario')

@section('content_header')
    <h1><i class="fas fa-book"></i> Manual de Usuario</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header p-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                        <i class="fas fa-info-circle"></i> General
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sites-tab" data-toggle="tab" href="#sites" role="tab">
                        <i class="fas fa-globe"></i> Gestión de Sitios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="audits-tab" data-toggle="tab" href="#audits" role="tab">
                        <i class="fas fa-search"></i> Auditorías SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="keywords-tab" data-toggle="tab" href="#keywords" role="tab">
                        <i class="fas fa-key"></i> Keywords
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab">
                        <i class="fas fa-tasks"></i> Tareas SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="competitors-tab" data-toggle="tab" href="#competitors" role="tab">
                        <i class="fas fa-users"></i> Análisis de Competencia
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab">
                        <i class="fas fa-file-pdf"></i> Reportes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="daily-tasks-tab" data-toggle="tab" href="#daily-tasks" role="tab">
                        <i class="fas fa-calendar-day"></i> Tareas Diarias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="alerts-tab" data-toggle="tab" href="#alerts" role="tab">
                        <i class="fas fa-bell"></i> Alertas SEO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="keyword-research-tab" data-toggle="tab" href="#keyword-research" role="tab">
                        <i class="fas fa-search"></i> Investigación Keywords
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <!-- Tab General -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <h3><i class="fas fa-info-circle"></i> Introducción</h3>
                    <p>Bienvenido al Manual de Usuario del Sistema SEO. Este sistema te permite gestionar múltiples sitios web, realizar auditorías SEO, seguir keywords, analizar competencia y generar reportes.</p>

                    <h4 class="mt-4">Características Principales</h4>
                    <ul>
                        <li><strong>Multisite:</strong> Gestiona múltiples sitios web desde un solo panel</li>
                        <li><strong>Integración con Google Search Console:</strong> Sincroniza métricas automáticamente</li>
                        <li><strong>Auditorías SEO On-Page:</strong> Analiza páginas web en busca de problemas SEO</li>
                        <li><strong>Tracking de Keywords:</strong> Monitorea posiciones de palabras clave</li>
                        <li><strong>Investigación de Keywords:</strong> Encuentra nuevas keywords para posicionar</li>
                        <li><strong>Análisis de Competencia:</strong> Compara tu rendimiento con competidores</li>
                        <li><strong>Planificador de Tareas:</strong> Organiza y gestiona tareas SEO</li>
                        <li><strong>Sistema de Alertas:</strong> Detecta problemas automáticamente</li>
                        <li><strong>Validación Técnica:</strong> Valida sitemap.xml y robots.txt</li>
                        <li><strong>Reportes PDF:</strong> Exporta reportes completos en formato PDF</li>
                    </ul>

                    <h4 class="mt-4">Requisitos del Sistema</h4>
                    <ul>
                        <li>Navegador web moderno (Chrome, Firefox, Edge)</li>
                        <li>Conexión a internet</li>
                        <li>Credenciales de Google Search Console (para sincronización de métricas)</li>
                    </ul>

                    <h4 class="mt-4">Soporte</h4>
                    <p>Para más información o soporte técnico, contacta al administrador del sistema.</p>
                </div>

                <!-- Tab Gestión de Sitios -->
                <div class="tab-pane fade" id="sites" role="tabpanel">
                    <h3><i class="fas fa-globe"></i> Gestión de Sitios Web</h3>

                    <h4 class="mt-4">Agregar un Nuevo Sitio</h4>
                    <ol>
                        <li>Ve a <strong>SEO → Sitios Web → Nuevo Sitio</strong></li>
                        <li>Completa el formulario:
                            <ul>
                                <li><strong>Nombre:</strong> Nombre descriptivo del sitio</li>
                                <li><strong>Dominio Base:</strong> Dominio sin http:// (ej: ejemplo.com)</li>
                                <li><strong>GSC Property:</strong> URL de la propiedad en Google Search Console</li>
                                <li><strong>Credenciales JSON:</strong> Archivo JSON de credenciales de GSC</li>
                            </ul>
                        </li>
                        <li>Haz clic en <strong>Guardar</strong></li>
                    </ol>

                    <h4 class="mt-4">Configurar Google Search Console</h4>
                    <ol>
                        <li>Ve a <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                        <li>Crea una cuenta de servicio o usa OAuth2</li>
                        <li>Descarga el archivo JSON de credenciales</li>
                        <li>Copia y pega el contenido en el campo "Credenciales JSON"</li>
                    </ol>

                    <h4 class="mt-4">Dashboard SEO</h4>
                    <p>Desde el dashboard de cada sitio puedes:</p>
                    <ul>
                        <li>Ver métricas de rendimiento (clicks, impresiones, CTR, posición)</li>
                        <li>Ver gráficos de evolución</li>
                        <li>Ver top URLs y keywords</li>
                        <li>Exportar reportes PDF</li>
                    </ul>

                    <h4 class="mt-4">Sincronizar Métricas</h4>
                    <ol>
                        <li>Ve al detalle del sitio</li>
                        <li>Haz clic en <strong>Sincronizar Métricas</strong></li>
                        <li>Selecciona el número de días a sincronizar</li>
                        <li>La sincronización se ejecutará en segundo plano</li>
                    </ol>
                </div>

                <!-- Tab Auditorías SEO -->
                <div class="tab-pane fade" id="audits" role="tabpanel">
                    <h3><i class="fas fa-search"></i> Auditorías SEO On-Page</h3>

                    <h4 class="mt-4">Ejecutar una Auditoría</h4>
                    <ol>
                        <li>Ve al detalle del sitio</li>
                        <li>Haz clic en <strong>Ejecutar Auditoría</strong></li>
                        <li>Ingresa la URL completa a auditar</li>
                        <li>Haz clic en <strong>Ejecutar Auditoría</strong></li>
                        <li>La auditoría se ejecutará en segundo plano</li>
                    </ol>

                    <h4 class="mt-4">¿Qué Analiza la Auditoría?</h4>
                    <ul>
                        <li><strong>Title:</strong> Verifica presencia y longitud</li>
                        <li><strong>Meta Description:</strong> Verifica presencia y longitud</li>
                        <li><strong>Encabezados:</strong> Cuenta H1, H2, H3</li>
                        <li><strong>Imágenes:</strong> Detecta imágenes sin atributo ALT</li>
                        <li><strong>Links:</strong> Identifica links internos, externos y rotos</li>
                        <li><strong>Canonical:</strong> Verifica presencia de canonical</li>
                        <li><strong>Robots Meta:</strong> Verifica directivas robots</li>
                        <li><strong>TTFB:</strong> Mide tiempo de respuesta del servidor</li>
                        <li><strong>Análisis de Contenido:</strong> Cuenta palabras, analiza densidad de keywords, sugiere mejoras</li>
                    </ul>

                    <h4 class="mt-4">Ver Resultados de Auditoría</h4>
                    <ol>
                        <li>Ve a <strong>Historial de Auditorías</strong> desde el sitio</li>
                        <li>Haz clic en <strong>Ver Detalles</strong> de la auditoría deseada</li>
                        <li>Revisa el score SEO y los problemas encontrados</li>
                    </ol>

                    <h4 class="mt-4">Links en Auditorías</h4>
                    <p>En la vista de detalles de auditoría encontrarás tabs para:</p>
                    <ul>
                        <li><strong>Links Internos:</strong> Lista completa con DataTables</li>
                        <li><strong>Links Externos:</strong> Lista completa con DataTables</li>
                        <li><strong>Links Rotos:</strong> Lista con status codes</li>
                    </ul>
                    <p>Cada tab tiene un botón para <strong>Exportar a Excel</strong>.</p>

                    <h4 class="mt-4">Nota Importante</h4>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Las auditorías con más de 30 links se ejecutan en segundo plano.
                        La verificación de links rotos puede tardar varios minutos.
                    </div>
                </div>

                <!-- Tab Keywords -->
                <div class="tab-pane fade" id="keywords" role="tabpanel">
                    <h3><i class="fas fa-key"></i> Tracking de Keywords</h3>

                    <h4 class="mt-4">Agregar una Keyword</h4>
                    <ol>
                        <li>Ve a <strong>SEO → Keywords → Nueva Keyword</strong></li>
                        <li>Selecciona el sitio</li>
                        <li>Ingresa la keyword a seguir</li>
                        <li>Opcional: Ingresa URL objetivo</li>
                        <li>Haz clic en <strong>Guardar</strong></li>
                    </ol>

                    <h4 class="mt-4">Dashboard de Keyword</h4>
                    <p>Desde el dashboard puedes ver:</p>
                    <ul>
                        <li>Gráfico de evolución de posición (últimos 30 días)</li>
                        <li>Posición actual</li>
                        <li>Cambio vs ayer</li>
                        <li>Cambio vs hace 7 días</li>
                    </ul>

                    <h4 class="mt-4">Actualizar Posiciones</h4>
                    <ol>
                        <li>Ve a la lista de keywords</li>
                        <li>Haz clic en <strong>Actualizar Posiciones</strong></li>
                        <li>El sistema buscará las posiciones desde las métricas de GSC</li>
                    </ol>

                    <h4 class="mt-4">Notas</h4>
                    <ul>
                        <li>Las posiciones se obtienen de Google Search Console</li>
                        <li>Es necesario tener métricas sincronizadas</li>
                        <li>Las posiciones se actualizan manualmente</li>
                    </ul>
                </div>

                <!-- Tab Tareas SEO -->
                <div class="tab-pane fade" id="tasks" role="tabpanel">
                    <h3><i class="fas fa-tasks"></i> Planificador de Tareas SEO</h3>

                    <h4 class="mt-4">Vista Kanban</h4>
                    <p>La vista Kanban te permite organizar tareas en columnas:</p>
                    <ul>
                        <li><strong>Pendiente:</strong> Tareas por hacer</li>
                        <li><strong>En Progreso:</strong> Tareas en trabajo</li>
                        <li><strong>Completadas:</strong> Tareas finalizadas</li>
                    </ul>

                    <h4 class="mt-4">Crear una Tarea</h4>
                    <ol>
                        <li>Ve a <strong>SEO → Tareas SEO → Nueva Tarea</strong></li>
                        <li>Completa el formulario:
                            <ul>
                                <li><strong>Sitio:</strong> Sitio relacionado</li>
                                <li><strong>Título:</strong> Nombre de la tarea</li>
                                <li><strong>Descripción:</strong> Detalles de la tarea</li>
                                <li><strong>Prioridad:</strong> Baja, Media, Alta, Crítica</li>
                                <li><strong>Asignado a:</strong> Usuario responsable</li>
                                <li><strong>Fecha límite:</strong> Fecha de vencimiento</li>
                            </ul>
                        </li>
                        <li>Haz clic en <strong>Guardar</strong></li>
                    </ol>

                    <h4 class="mt-4">Mover Tareas en Kanban</h4>
                    <p>Puedes mover tareas entre columnas de dos formas:</p>
                    <ol>
                        <li><strong>Arrastrar y soltar:</strong> Usa el mouse para arrastrar la tarjeta</li>
                        <li><strong>Botones:</strong> Usa los botones de acción en cada tarjeta</li>
                    </ol>

                    <h4 class="mt-4">Tareas Automáticas</h4>
                    <p>El sistema crea tareas automáticamente cuando:</p>
                    <ul>
                        <li>Se detectan errores críticos en auditorías</li>
                        <li>Se encuentran imágenes sin ALT</li>
                        <li>El score SEO es bajo (< 70)</li>
                    </ul>
                </div>

                <!-- Tab Competencia -->
                <div class="tab-pane fade" id="competitors" role="tabpanel">
                    <h3><i class="fas fa-users"></i> Análisis de Competencia</h3>

                    <h4 class="mt-4">Agregar un Competidor</h4>
                    <ol>
                        <li>Ve a <strong>SEO → Competencia → Nuevo Competidor</strong></li>
                        <li>Selecciona el sitio para el cual es competidor</li>
                        <li>Ingresa nombre y dominio del competidor</li>
                        <li><strong>Nota:</strong> No necesitas credenciales GSC del competidor</li>
                        <li>Haz clic en <strong>Guardar</strong></li>
                    </ol>

                    <h4 class="mt-4">Dashboard de Competencia</h4>
                    <p>Desde el dashboard puedes:</p>
                    <ul>
                        <li>Comparar posiciones de keywords con competidores</li>
                        <li>Identificar gaps (keywords donde el competidor está mejor)</li>
                        <li>Ver estadísticas de comparación</li>
                    </ul>

                    <h4 class="mt-4">Ingresar Posiciones del Competidor</h4>
                    <ol>
                        <li>Ve al dashboard de competencia</li>
                        <li>En la sección "Ingresar/Actualizar Posiciones"</li>
                        <li>Ingresa manualmente la posición del competidor para cada keyword</li>
                        <li>El sistema calculará el gap automáticamente</li>
                        <li>Haz clic en <strong>Guardar Posiciones</strong></li>
                    </ol>

                    <h4 class="mt-4">Obtener Posiciones del Competidor</h4>
                    <p>Puedes obtener las posiciones usando:</p>
                    <ul>
                        <li><strong>Herramientas SEO:</strong> Ahrefs, SEMrush, etc.</li>
                        <li><strong>Búsquedas manuales:</strong> Busca la keyword en Google y verifica la posición</li>
                    </ul>

                    <h4 class="mt-4">Gaps Identificados</h4>
                    <p>El sistema muestra automáticamente las keywords donde:</p>
                    <ul>
                        <li>El competidor está mejor posicionado</li>
                        <li>Hay oportunidades de mejora</li>
                    </ul>
                </div>

                <!-- Tab Reportes -->
                <div class="tab-pane fade" id="reports" role="tabpanel">
                    <h3><i class="fas fa-file-pdf"></i> Reportes PDF</h3>

                    <h4 class="mt-4">Tipos de Reportes</h4>
                    <ul>
                        <li><strong>Reporte de Sitio:</strong> Reporte completo con métricas, top URLs, keywords y auditorías</li>
                        <li><strong>Reporte de Métricas:</strong> Métricas detalladas por fecha</li>
                        <li><strong>Reporte de Auditoría:</strong> Resultados completos de una auditoría</li>
                    </ul>

                    <h4 class="mt-4">Generar Reporte de Sitio</h4>
                    <ol>
                        <li>Ve al dashboard del sitio</li>
                        <li>Haz clic en <strong>Exportar PDF</strong></li>
                        <li>Selecciona el período (días) en la URL si es necesario</li>
                        <li>El PDF se generará y descargará automáticamente</li>
                    </ol>

                    <h4 class="mt-4">Generar Reporte de Auditoría</h4>
                    <ol>
                        <li>Ve a los detalles de la auditoría</li>
                        <li>Haz clic en <strong>Exportar PDF</strong></li>
                        <li>El reporte incluirá:
                            <ul>
                                <li>Score SEO</li>
                                <li>Análisis On-Page</li>
                                <li>Análisis de Links</li>
                                <li>Errores y advertencias</li>
                            </ul>
                        </li>
                    </ol>

                    <h4 class="mt-4">Exportar Links a Excel</h4>
                    <p>Desde los detalles de auditoría puedes exportar:</p>
                    <ul>
                        <li>Links internos a Excel</li>
                        <li>Links externos a Excel</li>
                        <li>Links rotos a Excel</li>
                    </ul>
                    <p>Cada exportación incluye: URL, Texto del Link, Href Original y Status Code (para rotos).</p>
                </div>
                <div class="tab-pane fade" id="daily-tasks" role="tabpanel">
                    <h3><i class="fas fa-calendar-day"></i> Tareas Diarias como Auxiliar SEO</h3>
                    <p>Esta sección explica cómo el software te ayuda con tus tareas diarias.</p>

                    <h4 class="mt-4">🌅 Rutina Matutina (9:00 AM - 10:00 AM)</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>1. Revisar Alertas y Notificaciones</h5>
                            <p><strong>Módulo:</strong> Alertas SEO</p>
                            <p>El sistema te alerta automáticamente cuando:</p>
                            <ul>
                                <li>Una keyword pierde más de 5 posiciones</li>
                                <li>Hay caídas de tráfico > 20%</li>
                                <li>Se detectan nuevos errores SEO</li>
                                <li>Problemas de contenido (páginas muy cortas)</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 30 min → 5 min</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>2. Revisar Métricas del Día Anterior</h5>
                            <p><strong>Módulo:</strong> Dashboard SEO por Sitio</p>
                            <p>Ves automáticamente:</p>
                            <ul>
                                <li>Gráficos de evolución de clicks, impresiones, CTR, posición</li>
                                <li>Comparación con período anterior</li>
                                <li>Top URLs y keywords por rendimiento</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 20 min → 3 min</p>
                        </div>
                    </div>

                    <h4 class="mt-4">🔍 Investigación y Análisis (10:00 AM - 12:00 PM)</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>3. Investigación de Keywords</h5>
                            <p><strong>Módulo:</strong> Investigación de Keywords</p>
                            <p>Funcionalidades:</p>
                            <ul>
                                <li><strong>Desde GSC:</strong> Encuentra keywords que ya rankean pero no estás trackeando</li>
                                <li><strong>Autocomplete:</strong> Obtiene sugerencias relacionadas de Google</li>
                                <li><strong>Análisis de intención:</strong> Identifica si es informativa, comercial, transaccional</li>
                                <li><strong>Agregar al tracking:</strong> Con un clic agregas keywords prometedoras</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 1 hora → 15 min</p>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>4. Análisis de Competencia</h5>
                            <p><strong>Módulo:</strong> Análisis de Competencia</p>
                            <p>Compara automáticamente:</p>
                            <ul>
                                <li>Posiciones de tus keywords vs competidores</li>
                                <li>Identifica gaps (keywords donde están mejor)</li>
                                <li>Dashboard visual de comparación</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 45 min → 10 min</p>
                        </div>
                    </div>

                    <h4 class="mt-4">⚙️ Optimización Técnica (12:00 PM - 2:00 PM)</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>5. Auditorías SEO On-Page</h5>
                            <p><strong>Módulo:</strong> Auditorías SEO</p>
                            <p>Analiza automáticamente:</p>
                            <ul>
                                <li>Title, meta description, H1/H2/H3</li>
                                <li>Imágenes sin ALT</li>
                                <li>Links internos, externos y rotos</li>
                                <li>Análisis de contenido (palabras, densidad keywords)</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 2 horas → 30 min</p>
                        </div>
                    </div>

                    <h4 class="mt-4">📊 Seguimiento (2:00 PM - 4:00 PM)</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>6. Tracking de Keywords</h5>
                            <p><strong>Módulo:</strong> Tracking de Keywords</p>
                            <p>Funcionalidades:</p>
                            <ul>
                                <li>Botón "Actualizar Posiciones" actualiza todas las keywords</li>
                                <li>Dashboard por keyword con gráfico de evolución</li>
                                <li>Comparación diaria/semanal automática</li>
                                <li>Alertas cuando hay cambios significativos</li>
                            </ul>
                            <p><strong>Ahorro de tiempo:</strong> 1 hora → 5 min</p>
                        </div>
                    </div>

                    <h4 class="mt-4">📈 Resumen de Ahorro de Tiempo</h4>
                    <div class="alert alert-info">
                        <h5><i class="fas fa-clock"></i> Tiempo Total</h5>
                        <ul>
                            <li><strong>Antes:</strong> ~8 horas diarias</li>
                            <li><strong>Con el software:</strong> ~2 horas diarias</li>
                            <li><strong>Ahorro:</strong> ~75% del tiempo</li>
                        </ul>
                    </div>

                    <h4 class="mt-4">🎯 Flujo de Trabajo Diario Recomendado</h4>
                    <div class="card">
                        <div class="card-body">
                            <pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; white-space: pre-wrap;">9:00 AM  → Alertas SEO (5 min)
9:30 AM  → Dashboard Métricas (3 min)
10:00 AM → Investigar Keywords (15 min)
10:30 AM → Analizar Competencia (10 min)
11:00 AM → Ejecutar Auditorías (30 min)
2:00 PM  → Actualizar Keywords (5 min)
3:00 PM  → Revisar Tareas Kanban (10 min)
4:00 PM  → Generar Reportes si necesario (5 min)

Total: ~1.5 horas de trabajo activo</pre>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="alerts" role="tabpanel">
                    <h3><i class="fas fa-bell"></i> Sistema de Alertas SEO</h3>
                    <p>El sistema detecta automáticamente problemas y cambios importantes.</p>

                    <h4 class="mt-4">Tipos de Alertas</h4>
                    <ul>
                        <li><strong>Posición:</strong> Cuando una keyword pierde más de 5 posiciones</li>
                        <li><strong>Tráfico:</strong> Cuando hay caídas de tráfico > 20%</li>
                        <li><strong>Error:</strong> Cuando se detectan nuevos errores SEO</li>
                        <li><strong>Contenido:</strong> Problemas de contenido (páginas muy cortas, densidad baja)</li>
                        <li><strong>Rendimiento:</strong> Problemas de velocidad o TTFB</li>
                        <li><strong>Indexación:</strong> Problemas con sitemap o robots.txt</li>
                    </ul>

                    <h4 class="mt-4">Cómo Usar</h4>
                    <ol>
                        <li>Ve a "Alertas SEO" en el menú</li>
                        <li>Revisa las alertas no leídas</li>
                        <li>Filtra por tipo, severidad o sitio</li>
                        <li>Marca como leída o resuelta cuando termines</li>
                        <li>Usa "Detectar Cambios" para buscar nuevos problemas</li>
                    </ol>

                    <h4 class="mt-4">Detección Automática</h4>
                    <p>El sistema ejecuta automáticamente:</p>
                    <ul>
                        <li><strong>Diario 3:00 AM:</strong> Detecta cambios de posición y tráfico</li>
                        <li><strong>Semanal (Lunes 4:00 AM):</strong> Valida sitemap y robots.txt</li>
                    </ul>
                </div>
                <div class="tab-pane fade" id="keyword-research" role="tabpanel">
                    <h3><i class="fas fa-search"></i> Investigación de Keywords</h3>
                    <p>Encuentra nuevas keywords para posicionar y analiza oportunidades.</p>

                    <h4 class="mt-4">Fuentes de Investigación</h4>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>1. Desde Google Search Console</h5>
                            <p>Encuentra keywords que ya están rankeando pero no estás trackeando.</p>
                            <ol>
                                <li>Selecciona un sitio</li>
                                <li>Haz clic en "Buscar desde GSC"</li>
                                <li>El sistema muestra keywords con posición, clics e impresiones</li>
                                <li>Agrega las mejores al tracking con un clic</li>
                            </ol>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>2. Google Autocomplete</h5>
                            <p>Obtiene sugerencias de búsqueda relacionadas.</p>
                            <ol>
                                <li>Escribe una keyword semilla (ej: "hoteles en lima")</li>
                                <li>Haz clic en "Buscar Sugerencias"</li>
                                <li>El sistema obtiene sugerencias de Google</li>
                                <li>Analiza intención automáticamente</li>
                            </ol>
                        </div>
                    </div>

                    <h4 class="mt-4">Análisis de Keywords</h4>
                    <p>Cada keyword encontrada muestra:</p>
                    <ul>
                        <li><strong>Intención:</strong> Informativa, Comercial, Transaccional, Navegacional</li>
                        <li><strong>Posición actual:</strong> Si tu sitio ya rankea para esa keyword</li>
                        <li><strong>Clics e Impresiones:</strong> Datos desde GSC (si aplica)</li>
                        <li><strong>Volumen:</strong> Estimado (puedes editarlo manualmente)</li>
                        <li><strong>Dificultad:</strong> Fácil, Media o Difícil</li>
                    </ul>

                    <h4 class="mt-4">Agregar al Tracking</h4>
                    <ol>
                        <li>Revisa las keywords encontradas</li>
                        <li>Haz clic en el botón "+" (verde) para agregar al tracking</li>
                        <li>La keyword se agregará automáticamente a tu lista de keywords</li>
                        <li>El sistema comenzará a trackear su posición</li>
                    </ol>

                    <h4 class="mt-4">Filtros y Búsqueda</h4>
                    <p>Puedes filtrar por:</p>
                    <ul>
                        <li>Sitio</li>
                        <li>Fuente (GSC, Autocomplete, Manual)</li>
                        <li>Intención</li>
                        <li>Solo no trackeadas</li>
                    </ul>
                    <p>La tabla usa DataTables para búsqueda y ordenamiento rápido.</p>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .tab-content {
            padding: 20px;
        }
        .tab-content h3 {
            color: #3c8dbc;
            border-bottom: 2px solid #3c8dbc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .tab-content h4 {
            color: #555;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .tab-content ul, .tab-content ol {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        .tab-content li {
            margin-bottom: 8px;
        }
        .tab-content code {
            background-color: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.9em;
        }
    </style>
@stop

