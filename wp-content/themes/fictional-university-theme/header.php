<!DOCTYPE html>
<!-- Se usa DOCTYPE html para indicar al navegador que el documento es HTML5
     y evitar que entre en modo quirks, lo que rompería el layout del sitio. -->
<!-- Se usa language_attributes() para que el atributo lang sea dinámico según
     la configuración del idioma del sitio, lo que mejora la accesibilidad y el SEO. -->
<html <?php language_attributes(); ?>>

<head>
  <!-- Se define charset desde WordPress para que coincida con la configuración del sitio
       (normalmente UTF-8) y evitar problemas con caracteres especiales o acentos. -->
  <meta charset="<?php bloginfo('charset'); ?>">
  <!-- Se incluye el meta viewport para que el diseño responsivo funcione correctamente
       en dispositivos móviles, ya que sin esto el navegador mobile no escala el layout. -->
  <meta name="viewport" content="width=device-width, scale=1">
  <!-- Se llama wp_head() aquí para que WordPress y los plugins puedan inyectar
       estilos, scripts y metas en el <head> de forma ordenada sin modificar este archivo. -->
  <?php wp_head(); ?>
</head>

<!-- Se usa body_class() para que WordPress genere automáticamente clases CSS contextuales
     (como 'home', 'logged-in', 'page-id-10'), permitiendo aplicar estilos específicos
     por página sin necesidad de JavaScript o lógica condicional en CSS. -->

<body <?php body_class(); ?>>
  <header class="site-header">
    <div class="container">
      <h1 class="school-logo-text float-left">
        <!-- Se enlaza el logo a site_url() para que siempre lleve a la raíz del sitio,
             independientemente de en qué página o subpágina esté el usuario. -->
        <a href="<?php echo site_url(); ?>"><strong>Fictional</strong> University</a>
      </h1>
      <!-- Se usa esc_url() para sanitizar la URL antes de imprimirla en el HTML
           y prevenir posibles ataques XSS si la URL fuera manipulada. -->
      <a href="<?php echo esc_url(site_url('/search')); ?>" class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
      <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
      <div class="site-header__menu group">
        <nav class="main-navigation">
          <ul>
            <!-- Se marca el ítem de menú como activo condicionalmente para dar feedback visual
                 al usuario de en qué sección del sitio se encuentra actualmente. -->
            <!-- Se usa wp_get_post_parent_id() además de is_page() para que las subpáginas
                 de "About Us" también activen el ítem, no solo la página raíz. -->
            <li <?php if (is_page('about-us') or wp_get_post_parent_id(0) == 17) echo 'class="current-menu-item"' ?>><a href="<?php echo site_url('/about-us'); ?>">About Us</a></li>

            <!-- Se usa get_post_type() para detectar si se está viendo un programa individual
                 y marcar el ítem de menú correspondiente como activo. -->
            <li <?php if (get_post_type() == 'program') echo 'class="current-menu-item"' ?>><a href="<?php echo get_post_type_archive_link('program'); ?>">Programs</a></li>

            <!-- Se incluye is_page('past-events') para que la página de eventos pasados
                 también active el ítem de Eventos en la navegación. -->
            <li <?php if (get_post_type() == 'event' or is_page('past-events')) echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('event'); ?>">Events</a></li>
            <li <?php if (get_post_type() == 'campus') echo 'class="current-menu-item"'; ?>><a href="<?php echo get_post_type_archive_link('campus'); ?>">Campuses</a></li>
            <!-- Se usa get_post_type() == 'post' para detectar entradas estándar del blog
                 y diferenciarlas de los custom post types al marcar el ítem activo. -->
            <li <?php if (get_post_type() == 'post') echo 'class="current-menu-item"'; ?>><a href="<?php echo site_url('/blog'); ?>">Blog</a></li>
          </ul>
        </nav>
        <div class="site-header__util">
          <?php
          // Se muestra el botón de "My Notes" y "Log Out" solo cuando el usuario
          // está autenticado para no confundir a visitantes anónimos con opciones que no les aplican.
          if (is_user_logged_in()) { ?>
            <a href="<?php echo esc_url(site_url('/my-notes')); ?>" class="btn btn--small btn--orange float-left push-right">My Notes</a>
            <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn--dark-orange float-left btn--with-photo">
              <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60); ?></span>
              <span class="btn__text">Log Out</span>
            </a>
          <?php } else { ?>
            <a href="<?php echo wp_login_url(); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
            <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>
          <?php } ?>
          <a href="<?php echo esc_url(site_url('/search')); ?>" class=" search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </header>