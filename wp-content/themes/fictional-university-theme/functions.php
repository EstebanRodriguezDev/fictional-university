<?php

require get_theme_file_path('/inc/search-route.php');


// Añade un campo personalizado 'authorName' a la respuesta de la API REST para los 'post'.
// 'get_the_author()' recupera el nombre del autor y 'add_action' asegura que
// esta configuración se cargue al inicializar la API REST.
function university_custom_rest()
{
  register_rest_field('post', 'authorName', array(
    'get_callback' => function () {
      return get_the_author();
    }
  ));
}
add_action('rest_api_init', 'university_custom_rest');
/**
 * pageBanner: Función reutilizable para generar el banner dinámico (con imagen de fondo, título y subtítulo)
 * en distintas páginas del sitio (como archivos, entradas individuales, páginas estáticas).
 * 
 * @param array|null $args Arreglo opcional. Permite pasar 'title', 'subtitle' y 'photo' de forma directa desde el código.
 */
function pageBanner($args = NULL)
{
  if (!isset($args['title'])) {
    $args['title'] = get_the_title();
  }
  if (!isset($args['subtitle'])) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }
  if (!isset($args['photo'])) {
    if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }
?>
  <div class="page-banner">
    <!-- get_theme_file_uri(): Busca la imagen de fondo dentro de la carpeta del tema. -->
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
    <div class="page-banner__content container container--narrow">
      <!-- the_title(): Imprime el nombre del profesor. -->
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div>
    </div>
  </div>
<?php }
// university_files: Función personalizada para cargar hojas de estilo (CSS) y archivos JavaScript.
function university_files()
{
  // wp_enqueue_script: Registra y carga un archivo JS. array('jquery') indica que depende de jQuery.
  wp_enqueue_script('main_university_scripts', get_theme_file_uri('/build/index.js'), array('jquery', 'googleMap'), '1.0', true);

  wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E', null, '1.0', true);

  // wp_enqueue_style: Registra y carga una hoja de estilo CSS.
  wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

  wp_localize_script('main_university_scripts', 'universityData', array(
    'root_url' => get_site_url(),
    'nonce' => wp_create_nonce('wp_rest')
  ));
}
// add_action: "Engancha" nuestra función al evento 'wp_enqueue_scripts' de WordPress.
add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
  // add_theme_support: Activa características nativas. 'title-tag' permite que WP gestione el título de la pestaña del navegador.
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails'); // post-thumbnails: Habilita las imagenes destacadas de los posts types excepto los posts types personalizados. Para ello debemos habilitarlos directamente en archivo donde se crea el custom post type

  // add_image_size: Registra un nuevo tamaño de imagen para que WordPress genere copias automáticas al subir archivos.
  add_image_size('professorLandscape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'university_features');

// university_adjust_queries: Modifica las consultas a la base de datos antes de que se ejecuten en la página.
function university_adjust_queries($query)
{
  if (!is_admin() and is_post_type_archive('campus') and $query->is_main_query()) {
    $query->set('posts_per_page', -1); // Mostrar todos los elementos (-1).
  }
  // is_admin: Verifica que no estemos en el panel de control.
  // is_post_type_archive: Verifica si estamos viendo el listado de un tipo de contenido (Programas).
  // is_main_query: Asegura que solo modifiquemos la consulta principal de la página.
  if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
    $query->set('orderby', 'title'); // Ordenar por título.
    $query->set('order', 'ASC'); // Orden ascendente (A-Z).
    $query->set('posts_per_page', -1); // Mostrar todos los elementos (-1).
  }

  if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date'); // Usar el campo personalizado de fecha para ordenar.
    $query->set('orderby', 'meta_value_num'); // Ordenar numéricamente.
    $query->set('order', 'ASC'); // Del más cercano al más lejano.
    $query->set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
      )
    ));
  }
}
add_action('pre_get_posts', 'university_adjust_queries');

add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend()
{
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit();
  }
}

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar()
{
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

// Customize Login Screen

add_action('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl()
{
  return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS()
{
  wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}
add_action('login_headertitle', 'ourLoginTitle');

function ourLoginTitle()
{
  return get_bloginfo('name');
}
// universityMapKey: Intercepta la configuración de Advanced Custom Fields (ACF) para el campo de Google Maps.
// Se inyecta la clave API necesaria para que el mapa pueda renderizarse correctamente en el backend y el frontend.
// function universityMapKey($api)
// {
//   $api['key'] = 'AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E';
//   return $api;
// }

// add_filter('acf/fields/google_map/api', ...): Engancha nuestra función al filtro específico de ACF para proveer la API key de Maps.
// add_filter('acf/fields/google_map/api', 'universityMapKey');

// CLAVE API GOOGLE MAPS
// AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E