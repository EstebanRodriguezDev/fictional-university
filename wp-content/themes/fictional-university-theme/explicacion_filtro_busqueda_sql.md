# 🔍 Intercepción de Búsqueda SQL en WP_Query (Solo Título)

> [!NOTE]
> Este documento detalla la técnica de ingeniería inversa que aplicamos en `search-route.php` para forzar a WordPress a buscar la palabra clave **únicamente en el título** del contenido, ignorando el `post_content` nativo.

## 1. El Problema Base: ¿Por qué fallaba Gutenberg?

Cuando usamos `WP_Query` con el parámetro de búsqueda `'s' => 'math'`, WordPress construye la cláusula SQL `WHERE` de la siguiente manera por defecto:

```sql
AND (wp_posts.post_title LIKE '%math%' OR wp_posts.post_content LIKE '%math%')
```

Esto provocaba que, si el programa de "Biology" mencionaba la palabra "math" dentro de su contenido de Gutenberg (`post_content`), el buscador de la API lo devolviera como resultado válido, arrastrando a los profesores equivocados a la interfaz.

La solución del profesor del curso fue abandonar el `post_content` nativo y crear un campo personalizado en ACF. Esto funciona porque la consulta anterior **no busca en campos personalizados** (`wp_postmeta`) de forma nativa. Sin embargo, esto empobrece la experiencia del administrador al obligarlo a usar dos editores de texto a la vez.

## 2. La Solución Industrial: Filtrar la Consulta SQL

Para mantener Gutenberg (el editor de bloques moderno) y arreglar el buscador, aplicamos un **Filter Hook** (Filtro) nativo de WordPress llamado `posts_search`.

Este filtro actúa como una aduana: captura la porción del código SQL de búsqueda *justo antes* de que sea enviado a la base de datos MySQL, permitiéndonos reescribirla.

### Paso 1: La Función Interceptora

Definimos una función en `search-route.php` que recibe el SQL original (`$search`) y los datos de la consulta (`$wp_query`):

```php
function university_search_by_title_only($search, $wp_query) {
  global $wpdb; // Objeto global de WordPress para interactuar con la DB
  
  if (empty($search)) return $search;

  // Extraemos el término que el usuario buscó (ej: "math")
  $search_term = $wp_query->query_vars['s'];
  
  // Reescribimos el SQL para que busque EXCLUSIVAMENTE en la columna post_title
  $search = " AND ({$wpdb->posts}.post_title LIKE '%" . $wpdb->esc_like($search_term) . "%') ";

  return $search;
}
```

> [!IMPORTANT]  
> Usamos `$wpdb->esc_like()` para escapar la palabra clave por seguridad y evitar ataques de Inyección SQL.

### Paso 2: Ejecución Quirúrgica (Precisión)

Si simplemente dejamos este filtro activo globalmente, **romperemos la barra de búsqueda de todo el sitio web** (el Frontend), obligando a que toda búsqueda global mire únicamente en los títulos. 

Para evitar este daño colateral, aplicamos la inyección del filtro de forma dinámica, milisegundos antes de que ocurra nuestra búsqueda en la API REST, y lo apagamos milisegundos después:

```php
function universitySearchResults(WP_REST_Request $data) {
  
  // 🟢 1. ACTIVAMOS el filtro: Toda búsqueda a partir de aquí solo mirará títulos
  add_filter('posts_search', 'university_search_by_title_only', 10, 2);

  // 🔄 2. EJECUTAMOS la búsqueda: La base de datos es interceptada por nuestro filtro
  $mainQuery = new WP_Query(array(
   'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
   's' => sanitize_text_field($data['term'])
  ));

  // 🔴 3. DESACTIVAMOS el filtro inmediatamente: 
  // El resto del ecosistema de WordPress vuelve a la normalidad
  remove_filter('posts_search', 'university_search_by_title_only', 10);
  
  // ... resto del código para construir el JSON
}
```

## 3. Conclusión Arquitectónica

Con esta técnica:
1. **Limpiaste la base de datos:** No necesitas crear campos ACF redundantes (como "Contenido Principal").
2. **Mejoraste el Panel de Control:** Puedes usar el editor nativo Gutenberg tranquilamente.
3. **Escalabilidad:** Mantuviste la API limpia y blindada, sin afectar el resto del motor de WordPress.
