<!DOCTYPE html>
<!-- DOCTYPE html: Declaración que define el tipo de documento como HTML5. -->
<!-- html: Etiqueta raíz de un documento HTML. -->
<html <?php language_attributes(); ?>> <!-- language_attributes(): Función de WordPress que imprime los atributos de idioma (ej: lang="es-ES") según la configuración del sitio. -->

<head>
  <!-- meta charset: Define la codificación de caracteres del documento. -->
  <meta charset="<?php bloginfo('charset'); ?>"> <!-- bloginfo('charset'): Función de WordPress que muestra la codificación de caracteres definida en los ajustes (normalmente UTF-8). -->
  <!-- meta viewport: Configura la ventana de visualización para dispositivos móviles, asegurando que el sitio sea responsivo. -->
  <meta name="viewport" content="width=device-width, scale=1">
  <!-- wp_head(): Función crucial de WordPress. Permite que WordPress y los plugins inserten automáticamente scripts, estilos y etiquetas meta en el <head>. -->
  <?php wp_head(); ?>
</head>

<!-- body: Etiqueta que contiene todo el contenido visible del documento HTML. -->

<body <?php body_class(); ?>> <!-- body_class(): Función de WordPress que genera automáticamente clases de CSS para la etiqueta <body> (ej: 'home', 'logged-in', 'page-id-10') permitiendo aplicar estilos específicos según la página. -->
  <header class="site-header">
    <div class="container">
      <h1 class="school-logo-text float-left">
        <!-- a: Etiqueta de anclaje para crear un hipervínculo. -->
        <a href="<?php echo site_url(); ?>"><strong>Fictional</strong> University</a> <!-- site_url(): Función de WordPress que devuelve la dirección URL principal de tu sitio web. -->
      </h1>
      <!-- span: Etiqueta genérica para agrupar elementos en línea. -->
      <!-- i: Etiqueta para iconos (usando Font Awesome en este caso). -->
      <a href="<?php echo esc_url(site_url('/search')); ?>" class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
      <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
      <div class="site-header__menu group">
        <nav class="main-navigation">
          <ul>
            <!-- li: Elemento de lista. -->
            <!-- if: Estructura de control condicional de PHP. -->
            <!-- is_page('about-us'): Función condicional de WordPress que retorna true si la página actual tiene el slug 'about-us'. -->
            <!-- or: Operador lógico "o". -->
            <!-- wp_get_post_parent_id(0): Función de WordPress que obtiene el ID de la página padre de la página actual (0 se refiere al ID del post actual). -->
            <!-- ==: Operador de comparación "igual a". -->
            <!-- echo: Imprime una cadena de texto. -->
            <li <?php if (is_page('about-us') or wp_get_post_parent_id(0) == 17) echo 'class="current-menu-item"' ?>><a href="<?php echo site_url('/about-us'); ?>">About Us</a></li> <!-- site_url('/about-us'): Devuelve la URL del sitio con el slug '/about-us' adjunto. -->

            <!-- get_post_type(): Función de WordPress que devuelve el tipo de contenido actual (ej: 'post', 'page', 'program'). -->
            <!-- get_post_type_archive_link('program'): Función de WordPress que obtiene la URL de la página de archivo para el tipo de post 'program'. -->
            <li <?php if (get_post_type() == 'program') echo 'class="current-menu-item"' ?>><a href="<?php echo get_post_type_archive_link('program'); ?>">Programs</a></li>

            <li <?php if (get_post_type() == 'event' or is_page('past-events')) echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('event'); ?>">Events</a></li> <!-- is_page('past-events'): Condición que retorna true si la página actual tiene el slug 'past-events'. -->
            <li <?php if (get_post_type() == 'campus') echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('campus'); ?>">Campuses</a></li>
            <li <?php if (get_post_type() == 'post') echo 'class="current-menu-item"'; ?>><a href="<?php echo site_url('/blog'); ?>">Blog</a></li> <!-- get_post_type() == 'post': Condición que retorna true si el tipo de post actual es una entrada de blog estándar. -->
          </ul>
        </nav>
        <div class="site-header__util">
          <a href="#" class="btn btn--small btn--orange float-left push-right">Login</a>
          <a href="#" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
          <a href="<?php echo esc_url(site_url('/search')); ?>" class=" search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </header>