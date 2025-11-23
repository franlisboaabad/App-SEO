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
                        <li><strong>Análisis de Competencia:</strong> Compara tu rendimiento con competidores</li>
                        <li><strong>Planificador de Tareas:</strong> Organiza y gestiona tareas SEO</li>
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

