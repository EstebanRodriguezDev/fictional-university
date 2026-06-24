# Jerarquía de Templates — fictional-university-theme

## ¿Qué es la jerarquía de templates en WordPress?

WordPress decide **qué archivo PHP cargar** para cada URL del sitio siguiendo una cadena de prioridades predefinida llamada **Template Hierarchy**. Cuando el usuario visita una URL, WordPress analiza qué tipo de contenido corresponde a esa ruta y busca el archivo PHP más específico disponible en el tema. Si no lo encuentra, sube en la jerarquía hasta llegar a `index.php`, que es el fallback universal.

```
URL visitada → WordPress identifica el tipo de contenido → busca el template más específico → sube en la cadena hasta index.php
```

> [!IMPORTANT]
> Ningún template es opcional en términos absolutos, pero `index.php` es el **único obligatorio**. Todos los demás son especializaciones que sobreescriben ese comportamiento por defecto.

---

## Mapa general del tema

```
fictional-university-theme/
│
├── index.php              ← Fallback universal (blog / listado de posts)
├── front-page.php         ← Página de inicio
├── page.php               ← Páginas estáticas genéricas
│   ├── page-my-notes.php  ← Página específica: /my-notes
│   ├── page-past-events.php ← Página específica: /past-events
│   └── page-search.php    ← Página específica: /search
│
├── single.php             ← Entradas de blog individuales
│   ├── single-event.php   ← Evento individual
│   ├── single-campus.php  ← Campus individual
│   ├── single-professor.php ← Profesor individual
│   └── single-program.php ← Programa individual
│
├── archive.php            ← Listado genérico (categorías, autores, fechas)
│   ├── archive-event.php  ← Listado de todos los eventos
│   ├── archive-campus.php ← Listado de todos los campus
│   └── archive-program.php ← Listado de todos los programas
│
├── search.php             ← Resultados de búsqueda nativa de WordPress
│
├── header.php             ← Cabecera global (todas las páginas)
├── footer.php             ← Pie de página global (todas las páginas)
├── searchform.php         ← Formulario de búsqueda reutilizable
├── functions.php          ← Cerebro del tema (hooks, funciones, configuración)
│
├── template-parts/        ← Fragmentos reutilizables (no son templates de WP)
│   ├── event.php
│   ├── campus.php
│   ├── professor.php
│   ├── program.php
│   ├── post.php
│   └── page.php
│
└── inc/                   ← Lógica PHP pura (endpoints de API REST)
    ├── like-route.php
    └── search-route.php
```

---

## Templates raíz

### `index.php`
**Rol en WP Core:** Fallback universal y template del blog.

WordPress lo carga en dos situaciones distintas:
1. Cuando ningún otro template más específico existe para la URL visitada.
2. Cuando la página de inicio del blog (`/blog`) no tiene un template dedicado.

En este tema actúa exclusivamente como **listado del blog** porque todos los demás casos tienen su propio template. Se usa el Loop estándar de WordPress porque la consulta principal ya viene preparada con las entradas del blog. El banner usa `pageBanner()` con textos fijos ya que el listado del blog no es un post en la base de datos y no tiene título dinámico propio.

```
Jerarquía que lleva aquí: home.php → index.php
```

---

### `front-page.php`
**Rol en WP Core:** Template de la página de inicio del sitio.

WordPress lo carga **siempre** que el usuario visita la raíz del sitio (`/`), independientemente de si en los ajustes está configurada una "página estática" o las "últimas entradas". Tiene **máxima prioridad** frente a cualquier otro template, incluyendo `home.php` y `page.php`.

Se usa este template porque la portada necesita una estructura única (banner hero, slider, secciones de eventos y blog recientes) que no comparte con ninguna otra página del sitio. Contiene dos `WP_Query` personalizados para mostrar exactamente 2 eventos futuros y 2 posts recientes sin depender de la configuración global de posts por página.

```
Jerarquía: front-page.php (máxima prioridad, siempre gana)
```

---

### `header.php`
**Rol en WP Core:** Fragmento de cabecera incluido globalmente.

No es un template autónomo: se carga mediante `get_header()` al inicio de casi todos los demás templates. WordPress reserva este nombre de archivo para que `get_header()` lo encuentre automáticamente sin necesitar una ruta absoluta.

Contiene la estructura HTML `<!DOCTYPE html>`, `<head>`, `wp_head()` (hook crítico para scripts y estilos), y la barra de navegación principal. Se centraliza aquí para no duplicar esa estructura en cada uno de los +15 templates del tema.

La navegación usa condicionales (`is_page()`, `get_post_type()`) para marcar el ítem de menú activo en cada contexto sin necesidad de JavaScript.

```
Cargado por: get_header() en todos los templates
```

---

### `footer.php`
**Rol en WP Core:** Fragmento de pie de página incluido globalmente.

Análogo a `header.php`, se carga con `get_footer()` al final de todos los templates. Contiene el cierre del HTML (`</body>`, `</html>`) y el hook `wp_footer()`, que es **obligatorio** porque WordPress y los plugins inyectan aquí todos los scripts JavaScript que deben cargarse al final del DOM.

Sin `wp_footer()` los plugins activos y los scripts registrados en `functions.php` no se cargarían, rompiendo funcionalidades como el mapa de Google Maps o las interacciones AJAX de las notas y los likes.

```
Cargado por: get_footer() en todos los templates
```

---

### `functions.php`
**Rol en WP Core:** Archivo de configuración y extensión del tema.

WordPress lo carga **automáticamente** al inicializar cualquier petición, antes de cargar cualquier template. Es el único archivo del tema que no se incluye manualmente: se ejecuta siempre.

Aquí se definen:

| Función/Hook | Por qué está aquí |
|---|---|
| `university_files()` | Para registrar estilos y scripts de forma centralizada usando el sistema de enqueue de WordPress, que evita duplicados y gestiona dependencias. |
| `pageBanner()` | Función reutilizable que evita duplicar el markup del banner en los +15 templates del tema. |
| `university_features()` | Para declarar las capacidades del tema (imágenes destacadas, tamaños personalizados) antes de que WordPress las necesite. |
| `university_adjust_queries()` | Para modificar la consulta principal desde `pre_get_posts` sin crear WP_Query en cada template, manteniendo las plantillas limpias. |
| `redirectSubsToFrontend()` | Para bloquear el acceso al dashboard de usuarios con rol 'subscriber' en el momento correcto del ciclo de vida. |
| `makeNotePrivate()` | Para garantizar la privacidad y sanitización de las notas ANTES de que lleguen a la base de datos, independientemente del origen de la petición. |
| `university_custom_rest()` | Para añadir campos extra (authorName, userNoteCount) a las respuestas de la API REST que el JS del frontend consume. |

```
Cargado por: WordPress automáticamente en cada petición
```

---

### `searchform.php`
**Rol en WP Core:** Template del formulario de búsqueda.

WordPress lo busca automáticamente cuando se llama a `get_search_form()` en cualquier parte del tema. Si no existiera, WordPress generaría un formulario por defecto con su propio HTML, que no respetaría las clases CSS del tema.

Se personaliza aquí para que el formulario use las clases CSS del tema (`search-form`, `search-form-row`) y apunte a la URL correcta con `site_url('/')`. El campo `name="s"` es obligatorio porque WordPress identifica las búsquedas por ese parámetro en la URL (`?s=término`).

```
Cargado por: get_search_form() en search.php y page-search.php
```

---

## Templates de páginas estáticas (`page-*.php`)

### `page.php`
**Rol en WP Core:** Template genérico para páginas estáticas de WordPress.

Se carga para cualquier página creada en el panel de administración (Páginas → Añadir nueva) que no tenga un template más específico. Es el punto de entrada para todo el sistema de páginas jerárquicas del sitio (About Us, sus subpáginas, etc.).

Incluye lógica de navegación lateral que se activa automáticamente si la página tiene padre o hijos, usando `wp_get_post_parent_id()` y `get_pages()`. Esto permite que páginas como "About Us" y sus subpáginas compartan automáticamente la misma barra lateral sin configuración extra.

```
Jerarquía: page-{slug}.php → page-{id}.php → page.php
```

---

### `page-my-notes.php`
**Rol en WP Core:** Template específico para la página con slug `my-notes`.

WordPress lo carga automáticamente cuando la URL es `/my-notes`, antes de intentar `page.php`. Se nombra con el patrón `page-{slug}.php` para que WordPress lo reconozca sin configuración.

Se necesita este template separado (en vez de usar `page.php`) porque `/my-notes` requiere:
1. **Verificar autenticación** antes de mostrar cualquier contenido y redirigir al home si el usuario no está logueado.
2. **Una WP_Query personalizada** para cargar las notas del usuario actual, ya que el Loop principal solo carga la página estática, no las notas asociadas.

```
Jerarquía: page-my-notes.php → page.php → index.php
```

---

### `page-past-events.php`
**Rol en WP Core:** Template específico para la página con slug `past-events`.

Se necesita un template propio porque esta página no puede usar el Loop principal: los eventos pasados no son subpáginas ni posts estándar, sino un Custom Post Type filtrado por fecha. Requiere una `WP_Query` personalizada con `meta_query` para filtrar eventos cuya `event_date` sea menor a la fecha de hoy.

Además, la paginación requiere pasar `max_num_pages` del query personalizado explícitamente, cosa que no haría el Loop principal, que devolvería la paginación de la página estática (siempre 1 página).

```
Jerarquía: page-past-events.php → page.php → index.php
```

---

### `page-search.php`
**Rol en WP Core:** Template específico para la página con slug `search`.

Esta página es la "search page" visual del sitio (diferente a `search.php`, que maneja la búsqueda nativa de WordPress). Muestra el formulario de búsqueda con el mismo diseño de subpáginas que `page.php` (menú lateral jerárquico), pero sin mostrar contenido de post porque su único propósito es alojar el formulario.

Al usar el patrón `page-{slug}.php`, WordPress lo carga automáticamente para `/search` sin necesidad de configuración adicional en el admin.

```
Jerarquía: page-search.php → page.php → index.php
```

---

## Templates de entradas individuales (`single-*.php`)

### `single.php`
**Rol en WP Core:** Template para entradas de blog individuales (post type `post`).

Se carga cuando el usuario visita el permalink de una entrada del blog. Muestra el contenido completo con `the_content()` (a diferencia de `index.php` que muestra el extracto), el autor enlazado, la fecha y las categorías en el metabox.

Es deliberadamente simple porque las entradas del blog no tienen relaciones con otros Custom Post Types.

```
Jerarquía: single-{post-type}.php → single.php → singular.php → index.php
```

---

### `single-event.php`
**Rol en WP Core:** Template para el post type personalizado `event`.

WordPress lo carga cuando el usuario visita el permalink de un evento individual. Se necesita un template propio (en vez de `single.php`) porque los eventos tienen datos específicos que los posts del blog no tienen: programas relacionados obtenidos desde el campo ACF `related_programs`.

Muestra un breadcrumb de regreso al archivo de eventos usando `get_post_type_archive_link('event')` para que el usuario pueda volver al listado con un clic.

```
Jerarquía: single-event.php → single.php → singular.php → index.php
```

---

### `single-campus.php`
**Rol en WP Core:** Template para el post type personalizado `campus`.

Se necesita un template propio porque los campus tienen dos bloques únicos:
1. **Mapa de Google Maps** construido con los datos del campo ACF `map_location` (lat, lng, address), renderizado mediante atributos `data-*` en el HTML que el JS lee para inicializar el mapa.
2. **Lista de programas disponibles** en ese campus, obtenida con una `WP_Query` personalizada que busca programas cuyo campo `related_campus` contenga el ID del campus actual.

```
Jerarquía: single-campus.php → single.php → singular.php → index.php
```

---

### `single-professor.php`
**Rol en WP Core:** Template para el post type personalizado `professor`.

Tiene la estructura más compleja del tema porque además del contenido del editor muestra:
- **Sistema de likes**: Dos `WP_Query` para contar el total de likes del profesor y verificar si el usuario actual ya le dio like, pasando esos datos al JS mediante atributos `data-*` en el elemento `.like-box`.
- **Materias que imparte**: Campo ACF `related_programs` que lista los programas asociados al profesor.
- **Imagen en tamaño portrait**: Usa el tamaño personalizado `professorPortrait` (480×650) registrado en `functions.php` para mostrar la foto del docente en el formato correcto.

```
Jerarquía: single-professor.php → single.php → singular.php → index.php
```

---

### `single-program.php`
**Rol en WP Core:** Template para el post type personalizado `program`.

Es el template más extenso del tema porque un programa académico necesita mostrar tres tipos de contenido relacionado, cada uno con su propia `WP_Query`:

| Sección | WP_Query | Por qué |
|---|---|---|
| Profesores | Busca `post_type: professor` con `related_programs LIKE "ID"` | El campo relacional está en el profesor, no en el programa |
| Eventos futuros | Busca `post_type: event` con fecha >= hoy Y `related_programs LIKE "ID"` | Combina filtro temporal y relacional en una sola consulta |
| Campus disponibles | `get_field('related_campus')` desde ACF | El campo relacional está directamente en el programa |

El patrón `'value' => '"' . get_the_ID() . '"'` (con comillas dobles) es crítico: ACF serializa los arrays de relaciones como `a:2:{i:0;s:2:"42";i:1;s:2:"17";}` y sin las comillas la búsqueda `LIKE` podría encontrar el ID `1` dentro de `12` o `21`.

```
Jerarquía: single-program.php → single.php → singular.php → index.php
```

---

## Templates de archivos (`archive-*.php`)

### `archive.php`
**Rol en WP Core:** Template genérico para vistas de archivo (categorías, autores, fechas).

WordPress lo carga para cualquier vista de archivo que no tenga un template más específico: listado por categoría, por etiqueta, por autor, por fecha. Usa `get_the_archive_title()` y `get_the_archive_description()` de forma dinámica porque el título varía ("Categoría: Noticias", "Autor: Juan", etc.) y no puede ser hardcodeado.

```
Jerarquía: category.php / tag.php / author.php / date.php → archive.php → index.php
```

---

### `archive-event.php`
**Rol en WP Core:** Template de archivo para el post type `event`.

WordPress lo carga cuando la URL corresponde al archivo del post type `event` (generalmente `/events/`). Tiene prioridad sobre `archive.php` gracias al prefijo `archive-{post-type}.php`.

Se necesita un template propio porque:
1. El banner usa un título fijo ("All Events") en vez del dinámico de `archive.php`.
2. Cada evento se renderiza con `get_template_part('template-parts/event')` para mostrar la tarjeta visual de fecha con mes/día, no el listado simple de títulos.
3. Incluye un enlace a la página de eventos pasados, que es exclusivo de este listado.

La consulta que alimenta este template ya viene modificada desde `functions.php` via `university_adjust_queries()`, que filtra eventos futuros y ordena por fecha sin necesidad de una `WP_Query` personalizada en la plantilla.

```
Jerarquía: archive-event.php → archive.php → index.php
```

---

### `archive-campus.php`
**Rol en WP Core:** Template de archivo para el post type `campus`.

A diferencia de otros archivos que muestran una lista, este template muestra un **mapa de Google Maps** con todos los campus como marcadores. Para ello itera sobre todos los campus en el Loop y construye `<div class="marker" data-lat="..." data-lng="...">` para cada uno, que el script JS de Google Maps lee para posicionar los pines.

La consulta ya viene ajustada desde `university_adjust_queries()` con `posts_per_page = -1` para que **todos** los campus aparezcan en el mapa, no solo los primeros 10.

```
Jerarquía: archive-campus.php → archive.php → index.php
```

---

### `archive-program.php`
**Rol en WP Core:** Template de archivo para el post type `program`.

Muestra todos los programas académicos en una lista simple con enlaces. La consulta ya viene modificada por `university_adjust_queries()` para ordenar alfabéticamente (A-Z) y mostrar todos los programas sin paginación (`posts_per_page = -1`), ya que una lista completa de carreras es más útil que una paginada.

```
Jerarquía: archive-program.php → archive.php → index.php
```

---

## Template de búsqueda nativa

### `search.php`
**Rol en WP Core:** Template para los resultados de búsqueda nativa de WordPress.

WordPress lo carga cuando la URL contiene el parámetro `?s=término` (la búsqueda estándar de WordPress). Es diferente a la búsqueda en tiempo real via AJAX que usa `inc/search-route.php`.

Usa `get_post_type()` dinámicamente para cargar la template-part correcta según el tipo de resultado: `get_template_part('template-parts/' . get_post_type())`. Si el resultado es un 'event', carga `template-parts/event.php`; si es un 'professor', carga `template-parts/professor.php`, etc. Esto evita duplicar la lógica de presentación.

```
Jerarquía: search.php → index.php
```

---

## Template-parts (fragmentos reutilizables)

Los archivos en `template-parts/` **no son templates de WordPress**: WordPress no los carga automáticamente. Son fragmentos PHP que se incluyen manualmente con `get_template_part('template-parts/nombre')` desde otros templates.

> [!NOTE]
> La ventaja de `get_template_part()` frente a `include` es que WordPress busca el archivo dentro del tema (y del tema hijo si existiera), y permite que temas hijos sobreescriban partes específicas sin tocar el tema padre.

### `template-parts/event.php`
**Usado en:** `archive-event.php`, `page-past-events.php`, `front-page.php`, `single-program.php`, `search.php`

Renderiza la tarjeta visual de un evento con el bloque de fecha (mes + día en grande) y el extracto. Se centraliza aquí porque esta misma tarjeta aparece en 5 lugares distintos del sitio. Usa `DateTime` para convertir la fecha de ACF (formato `YYYYMMDD`) en componentes formateables (mes abreviado, día numérico).

---

### `template-parts/professor.php`
**Usado en:** `search.php`

Renderiza la tarjeta de un profesor en los resultados de búsqueda (imagen + nombre + enlace). Es el formato simplificado del profesor, diferente al template completo `single-professor.php`.

---

### `template-parts/campus.php`
**Usado en:** `search.php`

Renderiza un campus en los resultados de búsqueda con título, extracto y botón "View Campus".

---

### `template-parts/program.php`
**Usado en:** `search.php`

Renderiza un programa académico en los resultados de búsqueda con título, extracto y botón "View program".

---

### `template-parts/post.php`
**Usado en:** `search.php`

Renderiza una entrada de blog en los resultados de búsqueda con título, metabox (autor, fecha, categoría) y extracto.

---

### `template-parts/page.php`
**Usado en:** `search.php`

Renderiza una página estática en los resultados de búsqueda. Comparte estructura con `template-parts/post.php` pero sin el metabox de autor/fecha, ya que las páginas no tienen esa información contextual.

---

## Archivos de lógica pura (`inc/`)

Los archivos en `inc/` tampoco son templates: son módulos PHP que se incluyen en `functions.php` con `require`. Se separan del archivo principal para mantenerlo legible.

### `inc/like-route.php`
**Propósito:** Registrar los endpoints de la API REST para el sistema de likes de profesores.

Define dos rutas bajo el namespace `university/v1`:
- `POST /wp-json/university/v1/manageLike` → crea un like (nuevo post de tipo `like`)
- `DELETE /wp-json/university/v1/manageLike` → elimina un like permanentemente

Se usa el post type `like` como mecanismo de persistencia porque aprovecha la infraestructura de WordPress (usuarios, autores, meta fields) sin necesidad de tablas de base de datos propias. La verificación de autoría en el DELETE garantiza que nadie pueda eliminar los likes de otro usuario.

---

### `inc/search-route.php`
**Propósito:** Registrar el endpoint de búsqueda AJAX para la búsqueda en tiempo real del sitio.

Define la ruta `GET /wp-json/university/v1/search?term=X` que el JS del frontend consume al escribir en el buscador. Esta búsqueda es más potente que la nativa de WordPress porque:

1. **Busca solo en el título** (no en el contenido) mediante el filtro `posts_search`, lo que produce resultados más relevantes.
2. **Hace búsqueda relacional**: si encuentra un programa, lanza una segunda `WP_Query` para traer también los profesores y eventos vinculados a ese programa, aunque no contengan el término buscado en su título.
3. **Devuelve datos estructurados por tipo** (`generalInfo`, `professors`, `programs`, `events`, `campuses`) para que el JS pueda renderizar cada categoría con su propio componente visual.
4. **Limpia duplicados** con `array_unique(SORT_REGULAR)` + `array_values()` porque un elemento puede aparecer tanto en la búsqueda directa como en la relacional.

---

## Flujo completo de una petición típica

```
Usuario visita /programs/computer-science
        │
        ▼
WordPress identifica: post type 'program', vista 'single'
        │
        ▼
Busca: single-program.php ✓ (encontrado)
        │
        ▼
single-program.php llama:
  ├── get_header()           → carga header.php
  ├── pageBanner()           → función de functions.php
  ├── the_content()          → contenido del editor
  ├── WP_Query (profesores)  → busca profesores relacionados
  ├── WP_Query (eventos)     → busca eventos futuros relacionados
  ├── get_field()            → obtiene campus relacionados (ACF)
  └── get_footer()           → carga footer.php
                                    │
                                    └── wp_footer() → scripts JS
```

---

## Tabla resumen

| Template | URL típica | Cargado por WP cuando... | Tiene WP_Query propia |
|---|---|---|---|
| `front-page.php` | `/` | Siempre que se visita la raíz | ✅ (eventos + posts recientes) |
| `index.php` | `/blog` | No hay template más específico | ❌ (usa consulta principal) |
| `page.php` | `/about-us` | Página estática sin template específico | ❌ |
| `page-my-notes.php` | `/my-notes` | Slug exacto `my-notes` | ✅ (notas del usuario) |
| `page-past-events.php` | `/past-events` | Slug exacto `past-events` | ✅ (eventos pasados) |
| `page-search.php` | `/search` | Slug exacto `search` | ❌ |
| `single.php` | `/blog/titulo-post` | Post type `post`, vista individual | ❌ |
| `single-event.php` | `/events/nombre-evento` | Post type `event`, vista individual | ❌ (usa campo ACF) |
| `single-campus.php` | `/campuses/nombre-campus` | Post type `campus`, vista individual | ✅ (programas relacionados) |
| `single-professor.php` | `/professors/nombre` | Post type `professor`, vista individual | ✅ (likes totales + like del usuario) |
| `single-program.php` | `/programs/nombre` | Post type `program`, vista individual | ✅ (profesores + eventos + campus) |
| `archive.php` | `/category/noticias` | Archivo de categoría/autor/fecha | ❌ |
| `archive-event.php` | `/events` | Archivo del post type `event` | ❌ (consulta ajustada en functions.php) |
| `archive-campus.php` | `/campuses` | Archivo del post type `campus` | ❌ (consulta ajustada en functions.php) |
| `archive-program.php` | `/programs` | Archivo del post type `program` | ❌ (consulta ajustada en functions.php) |
| `search.php` | `/?s=término` | Parámetro `?s=` en la URL | ❌ (usa consulta de búsqueda de WP) |
