<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se redirige al usuario no autenticado antes de mostrar cualquier contenido
// para proteger la página de notas de visitantes anónimos.
if (!is_user_logged_in()) {
  wp_redirect(esc_url(site_url('/')));
  exit;
}
?>

<?php
// Se usa el Loop de WordPress aquí (aunque sea una página) para poder llamar
// a pageBanner() sin argumentos y que use automáticamente el título de la página 'My Notes'.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que use el título de la página actual,
  // manteniendo el diseño consistente con el resto del sitio.
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <div class="create-note">
      <h2 class="headline headline--medium">Create New Note</h2>
      <input class="new-note-title" type="text" placeholder="Title">
      <textarea class="new-note-body" name="" id="" placeholder="Your Note here..."></textarea>
      <span class="submit-note">Create Note</span>
      <span class="note-limit-message">Note Limit Reached: delete an existing note to make room for a new one.</span>
    </div>
    <ul class="min-list link-list" id="my-notes">
      <?php
      // Se usa una WP_Query personalizada para obtener SOLO las notas del usuario actual,
      // ya que el Loop principal solo carga la página, no sus notas asociadas.
      $userNotes = new WP_Query(array(
        'post_type' => 'note',
        'posts_per_page' => -1, // Se traen todas las notas porque el usuario necesita verlas y gestionarlas todas.
        'author' => get_current_user_id(), // Se filtra por usuario para que nadie vea las notas de otros.
      ));
      while ($userNotes->have_posts()) {
        $userNotes->the_post(); ?>
        <li data-id="<?php the_ID(); ?>">
          <input readonly class="note-title-field" value="<?php echo str_replace('Privado: ', '', esc_attr(wp_strip_all_tags(get_the_title()))); ?>">
          <span class="edit-note">
            <i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
          <span class="delete-note">
            <i class="fa fa-trash-o" aria-hidden="true"></i>Delete
          </span>
          <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
          <span class="update-note btn btn--blue btn--small">
            <i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
        </li>
      <?php } ?>
    </ul>
  </div>
<?php }

// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>