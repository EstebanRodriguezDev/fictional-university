<?php
// mu-plugins (Must-Use Plugins): Los archivos aquí se cargan automáticamente.
// Es la mejor práctica para registrar CPTs, ya que los datos no dependen del tema activo.

// university_post_types: Función donde definimos la estructura de datos personalizada.
function university_post_types()
{

  // Registro del tipo de contenido 'Campus' ()
  register_post_type('campus', array(
    'capability_type' => 'campus',
    'map_meta_cap' => true,
    'supports' => array('title', 'editor', 'excerpt'), // Define qué secciones aparecen en el editor.
    'rewrite' => array('slug' => 'campuses'), // Cambia la URL base de /campus/ a /campuses/ (más amigable).
    'has_archive' => true, // Habilita la página de listado automático (archive-campus.php).
    'public' => true, // Lo hace visible para visitantes y administradores.
    'show_in_rest' => true, // Necesario para habilitar el editor de bloques (Gutenberg) y la API.
    'labels' => array( // Textos descriptivos para la interfaz del administrador.
      'name' => 'Campuses',
      'add_new_item' => 'Add New Campus',
      'edit_item' => 'Edit Campus',
      'all_items' => 'All Campuses',
      'singular_name' => 'Campus'
    ),
    'menu_icon' => 'dashicons-location-alt' // Icono que se muestra en el menú lateral.
  ));

  // Registro del tipo de contenido 'Event' (Eventos)
  register_post_type('event', array(
    'capability_type' => 'event',
    'map_meta_cap' => true,
    'supports' => array('title', 'editor', 'excerpt'), // Define qué secciones aparecen en el editor.
    'rewrite' => array('slug' => 'events'), // Cambia la URL base de /event/ a /events/ (más amigable).
    'has_archive' => true, // Habilita la página de listado automático (archive-event.php).
    'public' => true, // Lo hace visible para visitantes y administradores.
    'show_in_rest' => true, // Necesario para habilitar el editor de bloques (Gutenberg) y la API.
    'labels' => array( // Textos descriptivos para la interfaz del administrador.
      'name' => 'Events',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
      'singular_name' => 'Event'
    ),
    'menu_icon' => 'dashicons-calendar' // Icono que se muestra en el menú lateral.
  ));

  // Registro del tipo de contenido 'Program' (Programas Académicos)
  register_post_type('program', array(
    'supports' => array('title', 'editor', 'excerpt'),
    'rewrite' => array('slug' => 'programs'),
    'has_archive' => true,
    'public' => true,
    'show_in_rest' => true,
    'labels' => array(
      'name' => 'Programs',
      'add_new_item' => 'Add New Program',
      'edit_item' => 'Edit Program',
      'all_items' => 'All Programs',
      'singular_name' => 'Program'
    ),
    'menu_icon' => 'dashicons-awards'
  ));

  // Registro del tipo de contenido 'Professor' (Profesores)
  register_post_type('professor', array(
    // Sugerencia: Añadir 'thumbnail' a supports si deseas usar la "Imagen Destacada" nativa de WP.
    'supports' => array('title', 'editor', 'thumbnail'),
    'public' => true,
    'show_in_rest' => true,
    'labels' => array(
      'name' => 'Professors',
      'add_new_item' => 'Add New Professor',
      'edit_item' => 'Edit Professor',
      'all_items' => 'All Professors',
      'singular_name' => 'Professor'
    ),
    'menu_icon' => 'dashicons-welcome-learn-more'
  ));
  // Registro del tipo de contenido 'Notas' (Mi Notas)
  register_post_type('note', array(
    'supports' => array('title', 'editor'),
    'public' => false,
    'show_ui' => true,
    'show_in_rest' => true,
    'labels' => array(
      'name' => 'Notes',
      'add_new_item' => 'Add New Note',
      'edit_item' => 'Edit Note',
      'all_items' => 'All Notes',
      'singular_name' => 'Note'
    ),
    'menu_icon' => 'dashicons-welcome-write-blog'
  ));
}
// add_action('init', ...): Registra los tipos de post cuando WordPress inicializa el sitio.
add_action('init', 'university_post_types');
