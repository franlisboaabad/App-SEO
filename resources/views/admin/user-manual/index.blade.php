@extends('adminlte::page')

@section('title', 'Manual de Usuario')

@section('content_header')
    <div class="row">
        <div class="col-md">
            <h1><i class="fas fa-book"></i> Manual de Usuario</h1>
            <p class="text-muted">Guía completa para usar el Sistema SEO</p>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Navegación</h3>
                </div>
                <div class="card-body p-0">
                    <nav class="nav nav-pills flex-column" id="manual-nav">
                        <a class="nav-link active" href="#general" data-toggle="tab">
                            <i class="fas fa-info-circle"></i> General
                        </a>
                        <a class="nav-link" href="#sites" data-toggle="tab">
                            <i class="fas fa-globe"></i> Gestión de Sitios
                        </a>
                        <a class="nav-link" href="#audits" data-toggle="tab">
                            <i class="fas fa-search"></i> Auditorías SEO
                        </a>
                        <a class="nav-link" href="#keywords" data-toggle="tab">
                            <i class="fas fa-key"></i> Keywords
                        </a>
                        <a class="nav-link" href="#keyword-research" data-toggle="tab">
                            <i class="fas fa-search-plus"></i> Investigación Keywords
                        </a>
                        <a class="nav-link" href="#serp-analysis" data-toggle="tab">
                            <i class="fas fa-chart-line"></i> Análisis SERP
                        </a>
                        <a class="nav-link" href="#backlinks" data-toggle="tab">
                            <i class="fas fa-link"></i> Backlinks
                        </a>
                        <a class="nav-link" href="#tasks" data-toggle="tab">
                            <i class="fas fa-tasks"></i> Tareas SEO
                        </a>
                        <a class="nav-link" href="#competitors" data-toggle="tab">
                            <i class="fas fa-users"></i> Competencia
                        </a>
                        <a class="nav-link" href="#alerts" data-toggle="tab">
                            <i class="fas fa-bell"></i> Alertas SEO
                        </a>
                        <a class="nav-link" href="#reports" data-toggle="tab">
                            <i class="fas fa-file-pdf"></i> Reportes
                        </a>
                        <a class="nav-link" href="#global-search" data-toggle="tab">
                            <i class="fas fa-search"></i> Búsqueda Global
                        </a>
                        <a class="nav-link" href="#daily-tasks" data-toggle="tab">
                            <i class="fas fa-calendar-day"></i> Tareas Diarias
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="manual-content">
                        <!-- Tab General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-info-circle fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Introducción</h2>
                            </div>

                            <div class="alert alert-primary">
                                <h5><i class="fas fa-lightbulb"></i> Bienvenido</h5>
                                <p class="mb-0">Bienvenido al Manual de Usuario del Sistema SEO. Este sistema te permite gestionar múltiples sitios web, realizar auditorías SEO, seguir keywords, analizar competencia y generar reportes.</p>
                            </div>

                            <h4 class="mt-4"><i class="fas fa-star text-warning"></i> Características Principales</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-info"><i class="fas fa-globe"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"><strong>Multisite</strong></span>
                                            <span class="info-box-number">Gestiona múltiples sitios desde un solo panel</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-success"><i class="fab fa-google"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"><strong>Google Search Console</strong></span>
                                            <span class="info-box-number">Sincroniza métricas automáticamente</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-search"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"><strong>Auditorías SEO</strong></span>
                                            <span class="info-box-number">Análisis completo On-Page</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-key"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text"><strong>Tracking Keywords</strong></span>
                                            <span class="info-box-number">Monitorea posiciones en tiempo real</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-list-check"></i> Funcionalidades Completas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> <strong>Multisite:</strong> Gestiona múltiples sitios web</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>GSC Integration:</strong> Sincronización automática</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Auditorías On-Page:</strong> Análisis completo</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Tracking Keywords:</strong> Monitoreo de posiciones</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Investigación Keywords:</strong> Encuentra nuevas oportunidades</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success"></i> <strong>Análisis SERP:</strong> Analiza resultados de búsqueda</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Backlinks:</strong> Gestiona enlaces entrantes</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Análisis Competencia:</strong> Compara rendimiento</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Sistema Alertas:</strong> Detecta problemas automáticamente</li>
                                                <li><i class="fas fa-check text-success"></i> <strong>Reportes PDF:</strong> Exporta reportes completos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4 class="mt-4"><i class="fas fa-cog text-info"></i> Requisitos del Sistema</h4>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="mb-0">
                                        <li><i class="fas fa-browser text-primary"></i> Navegador web moderno (Chrome, Firefox, Edge)</li>
                                        <li><i class="fas fa-wifi text-success"></i> Conexión a internet</li>
                                        <li><i class="fab fa-google text-danger"></i> Credenciales de Google Search Console (opcional, para sincronización)</li>
                                    </ul>
                                </div>
                            </div>

                            <h4 class="mt-4"><i class="fas fa-life-ring text-warning"></i> Soporte</h4>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Para más información o soporte técnico, contacta al administrador del sistema.
                            </div>
                        </div>

                        <!-- Tab Gestión de Sitios -->
                        <div class="tab-pane fade" id="sites" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-globe fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Gestión de Sitios Web</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Agregar un Nuevo Sitio</h5>
                                </div>
                                <div class="card-body">
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
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fab fa-google"></i> Configurar Google Search Console</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <a href="https://search.google.com/search-console" target="_blank">Google Search Console</a></li>
                                        <li>Crea una cuenta de servicio o usa OAuth2</li>
                                        <li>Descarga el archivo JSON de credenciales</li>
                                        <li>Copia y pega el contenido en el campo "Credenciales JSON"</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Dashboard SEO</h5>
                                </div>
                                <div class="card-body">
                                    <p>Desde el dashboard de cada sitio puedes:</p>
                                    <ul>
                                        <li>Ver métricas de rendimiento (clicks, impresiones, CTR, posición)</li>
                                        <li>Ver gráficos de evolución</li>
                                        <li>Ver top URLs y keywords</li>
                                        <li>Exportar reportes PDF y Excel</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-sync"></i> Sincronizar Métricas</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve al detalle del sitio</li>
                                        <li>Haz clic en <strong>Sincronizar Métricas</strong></li>
                                        <li>Selecciona el número de días a sincronizar</li>
                                        <li>La sincronización se ejecutará en segundo plano</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Auditorías SEO -->
                        <div class="tab-pane fade" id="audits" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-search fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Auditorías SEO On-Page</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-play-circle"></i> Ejecutar una Auditoría</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve al detalle del sitio</li>
                                        <li>Haz clic en <strong>Ejecutar Auditoría</strong></li>
                                        <li>Ingresa la URL completa a auditar</li>
                                        <li>Haz clic en <strong>Ejecutar Auditoría</strong></li>
                                        <li>La auditoría se ejecutará en segundo plano (puede tardar 1-5 minutos)</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-list-check"></i> ¿Qué Analiza la Auditoría?</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul>
                                                <li><strong>Title:</strong> Verifica presencia y longitud</li>
                                                <li><strong>Meta Description:</strong> Verifica presencia y longitud</li>
                                                <li><strong>Encabezados:</strong> Cuenta H1, H2, H3</li>
                                                <li><strong>Imágenes:</strong> Detecta imágenes sin atributo ALT</li>
                                                <li><strong>Links:</strong> Identifica links internos, externos y rotos</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul>
                                                <li><strong>Canonical:</strong> Verifica presencia de canonical</li>
                                                <li><strong>Robots Meta:</strong> Verifica directivas robots</li>
                                                <li><strong>TTFB:</strong> Mide tiempo de respuesta del servidor</li>
                                                <li><strong>PageSpeed Insights:</strong> Análisis de velocidad (FCP, LCP, CLS, FID, TTI)</li>
                                                <li><strong>Análisis de Contenido:</strong> Cuenta palabras, analiza densidad de keywords, sugiere mejoras</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-eye"></i> Ver Resultados de Auditoría</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <strong>Historial de Auditorías</strong> desde el sitio</li>
                                        <li>Haz clic en <strong>Ver Detalles</strong> de la auditoría deseada</li>
                                        <li>Revisa el score SEO y los problemas encontrados</li>
                                        <li>Revisa el análisis de velocidad (PageSpeed Insights)</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-link"></i> Links en Auditorías</h5>
                                </div>
                                <div class="card-body">
                                    <p>En la vista de detalles de auditoría encontrarás tabs para:</p>
                                    <ul>
                                        <li><strong>Links Internos:</strong> Lista completa con DataTables</li>
                                        <li><strong>Links Externos:</strong> Lista completa con DataTables</li>
                                        <li><strong>Links Rotos:</strong> Lista con status codes</li>
                                        <li><strong>Análisis de Contenido:</strong> Palabras, densidad, legibilidad</li>
                                        <li><strong>Análisis de Velocidad:</strong> Métricas de PageSpeed Insights</li>
                                    </ul>
                                    <p class="mb-0"><strong>Nota:</strong> Cada tab tiene un botón para <strong>Exportar a Excel</strong>.</p>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Nota Importante:</strong> Las auditorías con más de 30 links se ejecutan en segundo plano.
                                La verificación de links rotos y el análisis de PageSpeed Insights pueden tardar varios minutos.
                            </div>
                        </div>

                        <!-- Tab Keywords -->
                        <div class="tab-pane fade" id="keywords" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-key fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Tracking de Keywords</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> Agregar una Keyword</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <strong>SEO → Keywords → Nueva Keyword</strong></li>
                                        <li>Selecciona el sitio</li>
                                        <li>Ingresa la keyword a seguir</li>
                                        <li>Opcional: Ingresa URL objetivo</li>
                                        <li>Haz clic en <strong>Guardar</strong></li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Dashboard de Keyword</h5>
                                </div>
                                <div class="card-body">
                                    <p>Desde el dashboard puedes ver:</p>
                                    <ul>
                                        <li>Gráfico de evolución de posición (últimos 30 días)</li>
                                        <li>Posición actual</li>
                                        <li>Cambio vs ayer</li>
                                        <li>Cambio vs hace 7 días</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-sync"></i> Actualizar Posiciones</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a la lista de keywords</li>
                                        <li>Haz clic en <strong>Actualizar Posiciones</strong></li>
                                        <li>El sistema buscará las posiciones desde las métricas de GSC</li>
                                    </ol>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Las posiciones se obtienen de Google Search Console.
                                        Es necesario tener métricas sincronizadas. Las posiciones se actualizan manualmente.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Investigación Keywords -->
                        <div class="tab-pane fade" id="keyword-research" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-search-plus fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Investigación de Keywords</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fab fa-google"></i> Desde Google Search Console</h5>
                                </div>
                                <div class="card-body">
                                    <p>Encuentra keywords que ya están rankeando pero no estás trackeando.</p>
                                    <ol>
                                        <li>Selecciona un sitio</li>
                                        <li>Haz clic en "Buscar desde GSC"</li>
                                        <li>El sistema muestra keywords con posición, clics e impresiones</li>
                                        <li>Agrega las mejores al tracking con un clic</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-magic"></i> Google Autocomplete</h5>
                                </div>
                                <div class="card-body">
                                    <p>Obtiene sugerencias de búsqueda relacionadas.</p>
                                    <ol>
                                        <li>Escribe una keyword semilla (ej: "hoteles en lima")</li>
                                        <li>Haz clic en "Buscar Sugerencias"</li>
                                        <li>El sistema obtiene sugerencias de Google</li>
                                        <li>Analiza intención automáticamente</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Análisis de Keywords</h5>
                                </div>
                                <div class="card-body">
                                    <p>Cada keyword encontrada muestra:</p>
                                    <ul>
                                        <li><strong>Intención:</strong> Informativa, Comercial, Transaccional, Navegacional</li>
                                        <li><strong>Posición actual:</strong> Si tu sitio ya rankea para esa keyword</li>
                                        <li><strong>Clics e Impresiones:</strong> Datos desde GSC (si aplica)</li>
                                        <li><strong>Volumen:</strong> Estimado (puedes editarlo manualmente)</li>
                                        <li><strong>Dificultad:</strong> Fácil, Media o Difícil</li>
                                        <li><strong>Cluster/Tema:</strong> Agrupación automática por tema</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Agregar al Tracking</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Revisa las keywords encontradas</li>
                                        <li>Haz clic en el botón "+" (verde) para agregar al tracking</li>
                                        <li>La keyword se agregará automáticamente a tu lista de keywords</li>
                                        <li>El sistema comenzará a trackear su posición</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Análisis SERP -->
                        <div class="tab-pane fade" id="serp-analysis" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-chart-line fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Análisis de SERP</h2>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> El análisis de SERP te permite ver cómo aparecen tus resultados en Google y compararlos con los competidores.
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-play"></i> Analizar SERP</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <strong>SEO → Análisis de SERP</strong></li>
                                        <li>Haz clic en <strong>Nuevo Análisis</strong></li>
                                        <li>Selecciona el sitio y la keyword</li>
                                        <li>Haz clic en <strong>Analizar SERP</strong></li>
                                        <li>El análisis puede tardar 10-30 segundos</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Información Obtenida</h5>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li><strong>Tu Posición:</strong> Dónde aparece tu sitio en los resultados</li>
                                        <li><strong>Tu Snippet:</strong> Título, descripción y URL que muestra Google</li>
                                        <li><strong>Top 10 Competidores:</strong> Quién está rankeando mejor</li>
                                        <li><strong>Sugerencias:</strong> Recomendaciones para mejorar tu snippet</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-sync"></i> Re-analizar</h5>
                                </div>
                                <div class="card-body">
                                    <p>Puedes re-analizar un SERP en cualquier momento para ver cambios:</p>
                                    <ol>
                                        <li>Ve a los detalles del análisis</li>
                                        <li>Haz clic en <strong>Re-analizar</strong></li>
                                        <li>El sistema actualizará la información</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Backlinks -->
                        <div class="tab-pane fade" id="backlinks" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-link fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Gestión de Backlinks</h2>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Los backlinks son enlaces de otros sitios que apuntan a tu contenido. Son importantes para el SEO.
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> Agregar Backlink Manualmente</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <strong>SEO → Backlinks → Agregar Backlink</strong></li>
                                        <li>Completa el formulario:
                                            <ul>
                                                <li><strong>Sitio:</strong> Selecciona el sitio que recibe el enlace</li>
                                                <li><strong>URL Fuente:</strong> URL completa del sitio que enlaza</li>
                                                <li><strong>URL Destino:</strong> URL de tu sitio que recibe el enlace</li>
                                                <li><strong>Anchor Text:</strong> Texto del enlace (opcional)</li>
                                                <li><strong>Tipo:</strong> Dofollow, Nofollow, Sponsored, UGC</li>
                                            </ul>
                                        </li>
                                        <li>Haz clic en <strong>Guardar</strong></li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Detección de Backlinks Tóxicos</h5>
                                </div>
                                <div class="card-body">
                                    <p>El sistema detecta automáticamente backlinks tóxicos basándose en:</p>
                                    <ul>
                                        <li>Palabras clave sospechosas en el dominio o anchor text</li>
                                        <li>Dominios de spam (.tk, .ml, .ga, .cf)</li>
                                        <li>Heurística de calidad</li>
                                    </ul>
                                    <p class="mb-0">Los backlinks tóxicos se marcan en <span class="badge badge-danger">rojo</span> en la lista.</p>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Dashboard de Backlinks</h5>
                                </div>
                                <div class="card-body">
                                    <p>Desde el dashboard puedes ver:</p>
                                    <ul>
                                        <li>Total de backlinks</li>
                                        <li>Cantidad de dofollow vs nofollow</li>
                                        <li>Backlinks tóxicos</li>
                                        <li>Dominios únicos que enlazan</li>
                                        <li>Top 10 dominios que más enlazan</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fab fa-google"></i> Sincronización desde GSC</h5>
                                </div>
                                <div class="card-body">
                                    <p>La sincronización desde Google Search Console requiere configuración OAuth.</p>
                                    <p class="mb-0">Por ahora, puedes agregar backlinks manualmente o importar desde CSV.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Tareas SEO -->
                        <div class="tab-pane fade" id="tasks" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-tasks fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Planificador de Tareas SEO</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-th"></i> Vista Kanban</h5>
                                </div>
                                <div class="card-body">
                                    <p>La vista Kanban te permite organizar tareas en columnas:</p>
                                    <ul>
                                        <li><strong>Pendiente:</strong> Tareas por hacer</li>
                                        <li><strong>En Progreso:</strong> Tareas en trabajo</li>
                                        <li><strong>Completadas:</strong> Tareas finalizadas</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Crear una Tarea</h5>
                                </div>
                                <div class="card-body">
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
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-robot"></i> Tareas Automáticas</h5>
                                </div>
                                <div class="card-body">
                                    <p>El sistema crea tareas automáticamente cuando:</p>
                                    <ul>
                                        <li>Se detectan errores críticos en auditorías</li>
                                        <li>Se encuentran imágenes sin ALT</li>
                                        <li>El score SEO es bajo (< 70)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Competencia -->
                        <div class="tab-pane fade" id="competitors" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-users fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Análisis de Competencia</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> Agregar un Competidor</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a <strong>SEO → Competencia → Nuevo Competidor</strong></li>
                                        <li>Selecciona el sitio para el cual es competidor</li>
                                        <li>Ingresa nombre y dominio del competidor</li>
                                        <li>Haz clic en <strong>Guardar</strong></li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Dashboard de Competencia</h5>
                                </div>
                                <div class="card-body">
                                    <p>Desde el dashboard puedes:</p>
                                    <ul>
                                        <li>Comparar posiciones de keywords con competidores</li>
                                        <li>Identificar gaps (keywords donde el competidor está mejor)</li>
                                        <li>Ver estadísticas de comparación</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-edit"></i> Ingresar Posiciones del Competidor</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve al dashboard de competencia</li>
                                        <li>En la sección "Ingresar/Actualizar Posiciones"</li>
                                        <li>Ingresa manualmente la posición del competidor para cada keyword</li>
                                        <li>El sistema calculará el gap automáticamente</li>
                                        <li>Haz clic en <strong>Guardar Posiciones</strong></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Alertas SEO -->
                        <div class="tab-pane fade" id="alerts" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-bell fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Sistema de Alertas SEO</h2>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> El sistema detecta automáticamente problemas y cambios importantes. La detección es <strong>manual</strong> - debes hacer clic en "Detectar Cambios" para buscar nuevos problemas.
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-danger">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Tipos de Alertas</h5>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li><strong>Posición:</strong> Cuando una keyword pierde más de 5 posiciones</li>
                                        <li><strong>Tráfico:</strong> Cuando hay caídas de tráfico > 20%</li>
                                        <li><strong>Error:</strong> Cuando se detectan nuevos errores SEO</li>
                                        <li><strong>Contenido:</strong> Problemas de contenido (páginas muy cortas, densidad baja)</li>
                                        <li><strong>Rendimiento:</strong> Problemas de velocidad o TTFB</li>
                                        <li><strong>Técnico:</strong> Problemas con sitemap o robots.txt</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-hand-pointer"></i> Cómo Usar</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Ve a "Alertas SEO" en el menú</li>
                                        <li>Haz clic en <strong>Detectar Cambios</strong> para buscar nuevos problemas</li>
                                        <li>Revisa las alertas no leídas</li>
                                        <li>Filtra por tipo, severidad o sitio</li>
                                        <li>Marca como leída o resuelta cuando termines</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Reportes -->
                        <div class="tab-pane fade" id="reports" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-file-pdf fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Reportes PDF y Excel</h2>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-danger">
                                    <h5 class="mb-0"><i class="fas fa-file-pdf"></i> Tipos de Reportes PDF</h5>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li><strong>Reporte de Sitio:</strong> Reporte completo con métricas, top URLs, keywords y auditorías</li>
                                        <li><strong>Reporte de Métricas:</strong> Métricas detalladas por fecha</li>
                                        <li><strong>Reporte de Auditoría:</strong> Resultados completos de una auditoría (incluye PageSpeed Insights)</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-file-excel"></i> Exportaciones a Excel</h5>
                                </div>
                                <div class="card-body">
                                    <p>Puedes exportar a Excel:</p>
                                    <ul>
                                        <li><strong>Keywords:</strong> Lista completa de keywords con posiciones</li>
                                        <li><strong>Métricas SEO:</strong> Métricas por fecha</li>
                                        <li><strong>Resultados de Auditoría:</strong> Links internos, externos y rotos</li>
                                        <li><strong>Investigación Keywords:</strong> Keywords encontradas en investigación</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-download"></i> Generar Reportes</h5>
                                </div>
                                <div class="card-body">
                                    <h6>Reporte de Sitio:</h6>
                                    <ol>
                                        <li>Ve al dashboard del sitio</li>
                                        <li>Haz clic en <strong>Exportar PDF</strong></li>
                                        <li>El PDF se generará y descargará automáticamente</li>
                                    </ol>
                                    <hr>
                                    <h6>Reporte de Auditoría:</h6>
                                    <ol>
                                        <li>Ve a los detalles de la auditoría</li>
                                        <li>Haz clic en <strong>Exportar PDF</strong></li>
                                        <li>El reporte incluye análisis completo, PageSpeed Insights y recomendaciones</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Búsqueda Global -->
                        <div class="tab-pane fade" id="global-search" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-search fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Búsqueda Global</h2>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> La búsqueda global te permite encontrar rápidamente keywords, sitios, URLs y tareas desde cualquier parte del sistema.
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-search"></i> Cómo Usar</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Usa la barra de búsqueda en el <strong>navbar superior</strong></li>
                                        <li>Escribe al menos 2 caracteres</li>
                                        <li>Verás sugerencias mientras escribes (autocompletado)</li>
                                        <li>Haz clic en una sugerencia para ir directamente</li>
                                        <li>O presiona Enter para ver todos los resultados</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Qué Puedes Buscar</h5>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li><strong>Keywords:</strong> Por nombre de keyword</li>
                                        <li><strong>Sitios:</strong> Por nombre o dominio</li>
                                        <li><strong>Tareas:</strong> Por título o descripción</li>
                                        <li><strong>URLs:</strong> Cualquier URL en auditorías o keywords</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-magic"></i> Autocompletado</h5>
                                </div>
                                <div class="card-body">
                                    <p>El autocompletado muestra hasta 5 sugerencias de cada tipo mientras escribes.</p>
                                    <p class="mb-0">Cada sugerencia muestra el tipo (keyword, site, task) y un icono para identificarlo fácilmente.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Tareas Diarias -->
                        <div class="tab-pane fade" id="daily-tasks" role="tabpanel">
                            <div class="d-flex align-items-center mb-4">
                                <i class="fas fa-calendar-day fa-2x text-primary mr-3"></i>
                                <h2 class="mb-0">Tareas Diarias como Auxiliar SEO</h2>
                            </div>

                            <div class="alert alert-success">
                                <h5><i class="fas fa-clock"></i> Ahorro de Tiempo</h5>
                                <p class="mb-0"><strong>Antes:</strong> ~8 horas diarias | <strong>Con el software:</strong> ~2 horas diarias | <strong>Ahorro:</strong> ~75% del tiempo</p>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0"><i class="fas fa-sun"></i> Rutina Matutina (9:00 AM - 10:00 AM)</h5>
                                </div>
                                <div class="card-body">
                                    <h6>1. Revisar Alertas y Notificaciones</h6>
                                    <p><strong>Módulo:</strong> Alertas SEO</p>
                                    <p>Haz clic en "Detectar Cambios" para buscar:</p>
                                    <ul>
                                        <li>Keywords que perdieron más de 5 posiciones</li>
                                        <li>Caídas de tráfico > 20%</li>
                                        <li>Nuevos errores SEO</li>
                                        <li>Problemas de contenido</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 30 min → 5 min</p>
                                    <hr>
                                    <h6>2. Revisar Métricas del Día Anterior</h6>
                                    <p><strong>Módulo:</strong> Dashboard SEO por Sitio</p>
                                    <p>Ves automáticamente:</p>
                                    <ul>
                                        <li>Gráficos de evolución</li>
                                        <li>Comparación con período anterior</li>
                                        <li>Top URLs y keywords</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 20 min → 3 min</p>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-info">
                                    <h5 class="mb-0"><i class="fas fa-search"></i> Investigación y Análisis (10:00 AM - 12:00 PM)</h5>
                                </div>
                                <div class="card-body">
                                    <h6>3. Investigación de Keywords</h6>
                                    <p><strong>Módulo:</strong> Investigación de Keywords</p>
                                    <ul>
                                        <li>Busca desde GSC keywords no trackeadas</li>
                                        <li>Usa Autocomplete para sugerencias</li>
                                        <li>Analiza intención automáticamente</li>
                                        <li>Agrega al tracking con un clic</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 1 hora → 15 min</p>
                                    <hr>
                                    <h6>4. Análisis de Competencia</h6>
                                    <p><strong>Módulo:</strong> Análisis de Competencia</p>
                                    <ul>
                                        <li>Compara posiciones automáticamente</li>
                                        <li>Identifica gaps</li>
                                        <li>Dashboard visual</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 45 min → 10 min</p>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-cog"></i> Optimización Técnica (12:00 PM - 2:00 PM)</h5>
                                </div>
                                <div class="card-body">
                                    <h6>5. Auditorías SEO On-Page</h6>
                                    <p><strong>Módulo:</strong> Auditorías SEO</p>
                                    <ul>
                                        <li>Análisis completo automático</li>
                                        <li>PageSpeed Insights integrado</li>
                                        <li>Detección de problemas</li>
                                        <li>Exportación a Excel</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 2 horas → 30 min</p>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-success">
                                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Seguimiento (2:00 PM - 4:00 PM)</h5>
                                </div>
                                <div class="card-body">
                                    <h6>6. Tracking de Keywords</h6>
                                    <p><strong>Módulo:</strong> Tracking de Keywords</p>
                                    <ul>
                                        <li>Actualiza todas las posiciones con un clic</li>
                                        <li>Dashboard por keyword</li>
                                        <li>Alertas automáticas</li>
                                    </ul>
                                    <p class="text-success"><strong>Ahorro:</strong> 1 hora → 5 min</p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-danger">
                                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Flujo de Trabajo Diario Recomendado</h5>
                                </div>
                                <div class="card-body">
                                    <pre style="background: #f4f4f4; padding: 15px; border-radius: 5px; white-space: pre-wrap; font-family: 'Courier New', monospace;">9:00 AM  → Alertas SEO (5 min)
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .nav-pills .nav-link {
        border-radius: 0;
        color: #495057;
    }
    .nav-pills .nav-link.active {
        background-color: #3c8dbc;
        color: white;
    }
    .nav-pills .nav-link:hover {
        background-color: #e9ecef;
    }
    .tab-content {
        min-height: 500px;
    }
    .tab-content h2 {
        color: #3c8dbc;
        border-bottom: 3px solid #3c8dbc;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    .tab-content h4 {
        color: #555;
        margin-top: 30px;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .tab-content h5 {
        color: #333;
        margin-bottom: 15px;
    }
    .tab-content h6 {
        color: #666;
        margin-top: 20px;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .tab-content ul, .tab-content ol {
        margin-left: 25px;
        margin-bottom: 20px;
    }
    .tab-content li {
        margin-bottom: 10px;
        line-height: 1.6;
    }
    .card-header {
        font-weight: 600;
    }
    .info-box {
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 5px;
    }
    pre {
        font-size: 0.9em;
        line-height: 1.5;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Activar navegación lateral
    $('#manual-nav a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        // Actualizar tabs
        $('#manual-nav a').removeClass('active');
        $(this).addClass('active');

        // Mostrar contenido
        $('.tab-pane').removeClass('show active');
        $(target).addClass('show active');

        // Scroll al top
        $('html, body').animate({
            scrollTop: 0
        }, 300);
    });
});
</script>
@stop
