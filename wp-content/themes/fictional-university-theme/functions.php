<?php


require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/search-route.php');


// Se expone authorName en la API REST porque el frontend necesita mostrar
// el nombre del autor en las tarjetas del blog, y ese dato no viene por defecto en la respuesta estándar.
function university_custom_rest()
{
  // Se registra 'authorName' para los posts del blog porque el JS del frontend lo consume
  // al renderizar las tarjetas en los resultados de búsqueda.
  register_rest_field('post', 'authorName', array(
    'get_callback' => function () {
      return get_the_author();
    }
  ));
  // Se registra 'userNoteCount' para limitar cuántas notas puede crear un usuario
  // sin necesidad de hacer una consulta extra desde el frontend.
  register_rest_field('note', 'userNoteCount', array(
    'get_callback' => function () {
      return count_user_posts(get_current_user_id(), 'note');
    }
  ));
}
add_action('rest_api_init', 'university_custom_rest');

/**
 * pageBanner: Se centraliza en una función reutilizable para evitar duplicar el markup del banner
 * en cada plantilla. Permite pasar 'title', 'subtitle' y 'photo' opcionalmente para que
 * cada página pueda personalizar su banner sin romper el diseño global.
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
    // Se verifica que haya una imagen en ACF y que no sea un archivo ni el home,
    // porque esas vistas no tienen imagen asignada por post y deben usar la imagen de reserva.
    if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      // Se usa una imagen de reserva del tema para que el banner nunca quede vacío
      // en páginas que no tienen imagen de fondo configurada en ACF.
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }
?>
  <div class="page-banner">
    <!-- Se referencia la imagen con get_theme_file_uri() para obtener una URL absoluta correcta
         independientemente del dominio o subdirectorio donde esté instalado WordPress. -->
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
    <div class="page-banner__content container container--narrow">
      <!-- Se usa $args['title'] en lugar de hardcodear el título para que la misma
           función sirva para cualquier tipo de contenido (página, evento, programa, etc.). -->
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div>
    </div>
  </div>
<?php }

// university_files: Se agrupa la carga de estilos y scripts en una función propia
// para engancharla al hook correcto de WordPress y evitar cargarlos directamente en el HTML.
function university_files()
{
  // Se declara la dependencia de jQuery y googleMap para que WordPress los cargue
  // en el orden correcto y el script principal no falle por falta de dependencias.
  wp_enqueue_script('main_university_scripts', get_theme_file_uri('/build/index.js'), array('jquery', 'googleMap'), '1.0', true);

  wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E', null, '1.0', true);

  // Se cargan las fuentes y estilos a través de wp_enqueue_style para que WordPress
  // gestione la deduplicación y el orden de carga, evitando duplicados de plugins o temas hijos.
  wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

  // Se usa wp_localize_script para pasar datos de PHP al JS de forma segura,
  // evitando hardcodear la URL del sitio o el nonce directamente en el código JS.
  wp_localize_script('main_university_scripts', 'universityData', array(
    'root_url' => get_site_url(),
    'nonce' => wp_create_nonce('wp_rest')
  ));
}
// Se engancha al hook 'wp_enqueue_scripts' para que WordPress cargue los archivos
// en el momento correcto del ciclo de vida de la página, no antes ni después.
add_action('wp_enqueue_scripts', 'university_files');

function university_features()
{
  // Se habilita 'title-tag' para delegar la gestión del título de la pestaña del navegador
  // a WordPress, permitiendo que plugins de SEO lo modifiquen correctamente.
  add_theme_support('title-tag');
  // Se habilita 'post-thumbnails' para activar las imágenes destacadas en los tipos de post estándar.
  // Los custom post types requieren habilitarse por separado en su archivo de registro.
  add_theme_support('post-thumbnails');

  // Se registran tamaños de imagen personalizados para que WordPress genere copias optimizadas
  // automáticamente al subir imágenes, evitando que el navegador descargue imágenes más grandes de lo necesario.
  add_image_size('professorLandscape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'university_features');

// university_adjust_queries: Se intercepta la consulta principal ANTES de que se ejecute
// para modificar sus parámetros sin necesidad de crear una WP_Query personalizada en cada plantilla.
function university_adjust_queries($query)
{
  if (!is_admin() and is_post_type_archive('campus') and $query->is_main_query()) {
    $query->set('posts_per_page', -1); // Se muestran todos los campus para que el mapa de Google Maps tenga todos los marcadores disponibles.
  }
  // Se verifica !is_admin() para que estos cambios no afecten las listas del panel de administración.
  // Se verifica is_main_query() para no alterar consultas secundarias o widgets en la misma página.
  if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
    $query->set('orderby', 'title'); // Se ordena alfabéticamente para que el usuario encuentre su carrera más fácilmente.
    $query->set('order', 'ASC');
    $query->set('posts_per_page', -1); // Se muestran todos los programas para que el usuario vea el catálogo completo de una vez.
  }

  if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date'); // Se indica a WordPress qué campo personalizado usar para el ordenamiento cronológico.
    $query->set('orderby', 'meta_value_num'); // Se ordena numéricamente porque la fecha en ACF está en formato 'AñoMesDía' (ej. 20241230).
    $query->set('order', 'ASC'); // Se muestra el evento más próximo primero para que sea lo más útil posible para el usuario.
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

// Se usa el hook 'admin_init' para redirigir a los suscriptores antes de que vean
// cualquier contenido del panel, protegiendo el backend de usuarios sin permisos de administración.
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend()
{
  $ourCurrentUser = wp_get_current_user();

  // Se verifica que el rol sea exactamente 'subscriber' (y solo ese) para no redirigir
  // a usuarios con múltiples roles que sí necesitan acceder al panel.
  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit();
  }
}

// Se oculta la barra de administración para los suscriptores porque es una interfaz
// de administración que no les aporta valor y puede confundir al usuario final.
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar()
{
  $ourCurrentUser = wp_get_current_user();

  if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

// --- Personalización de la pantalla de Login ---

// Se sobrescribe la URL del logotipo de login para que el usuario sea redirigido
// al sitio principal en vez de a wordpress.org al hacer clic en el logo.
add_action('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl()
{
  return esc_url(site_url('/'));
}

// Se cargan los estilos del tema en el login para mantener la identidad visual consistente
// con el resto del sitio y dar una experiencia de marca unificada.
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS()
{
  wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

// Se cambia el atributo title del logotipo para mostrar el nombre del sitio
// en vez de "WordPress", reforzando la identidad de la universidad en la pantalla de login.
add_action('login_headertitle', 'ourLoginTitle');

function ourLoginTitle()
{
  return get_bloginfo('name');
}

// Se intercepta la inserción de posts con wp_insert_post_data para aplicar
// validaciones y sanitización ANTES de que los datos lleguen a la base de datos,
// garantizando seguridad y consistencia sin depender del frontend.
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

function makeNotePrivate(array $data, array $postarr)
{
  if ($data['post_type'] == 'note') {
    // Se limita a 4 notas para evitar que un usuario abuse del almacenamiento
    // de la base de datos. La condición !$postarr['ID'] evita bloquear actualizaciones de notas existentes.
    if (count_user_posts(get_current_user_id(), 'note') > 4 and !$postarr['ID']) {
      wp_send_json_error('You have reached your note limit.', 403);
    }
    // Se sanitizan el contenido y el título para prevenir inyección de HTML o JavaScript malicioso
    // que podría ejecutarse al mostrar la nota a otros usuarios.
    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }
  
  // Se fuerza el estado 'private' para que las notas solo sean visibles para su autor
  // y no aparezcan en búsquedas o listados públicos del sitio.
  if ($data['post_type'] == 'note' and $data['post_status'] != 'trash') {
    $data['post_status'] = "private";
  }
  return $data;
}
// universityMapKey: Se comenta porque la API key ahora se inyecta directamente en el script
// de Google Maps en university_files(), haciéndola redundante.
// function universityMapKey($api)
// {
//   $api['key'] = 'AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E';
//   return $api;
// }

// Se comenta porque la API key de ACF se reemplazó por la carga directa del script en university_files().
// add_filter('acf/fields/google_map/api', 'universityMapKey');

// CLAVE API GOOGLE MAPS
// AIzaSyBa4cXpvb0iBPuurmTNsIVTpSMN-cXgq8E