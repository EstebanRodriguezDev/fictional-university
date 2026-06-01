<?php
// get_header(): Función de WordPress que incluye el archivo 'header.php' del tema.
get_header();
if (!is_user_logged_in()) {
 wp_redirect(esc_url(site_url('/')));
 exit;
}
?>

<?php
while (have_posts()) { // while (have_posts()): Inicia el "Loop" de WordPress. have_posts() devuelve true si hay posts (páginas en este caso) para mostrar.
 the_post(); // the_post(): Prepara los datos del post/página actual para ser usados por las funciones de plantilla.
 // Muestra el banner superior dinámico. Sin parámetros, usará el título de la página actual.
 pageBanner();
?>

 <div class="container container--narrow page-section">
  <ul class="min-list link-list" id="my-notes">
   <?php
   $userNotes = new WP_Query(array(
    'post_type' => 'note',
    'posts_per_page' => -1,
    'author' => get_current_user_id(),
   ));
   while ($userNotes->have_posts()) {
    $userNotes->the_post(); ?>
    <li>
     <input class="note-title-field" value="<?php echo esc_attr(wp_strip_all_tags(get_the_title())); ?>">
     <span class="edit-note">
      <i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
     <span class="delete-note">
      <i class="fa fa-transh-o" aria-hidden="true"></i>Delete</span>
     <textarea class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
    </li>
   <?php } ?>
  </ul>
 </div>
<?php }

get_footer(); // get_footer(): Incluye el archivo 'footer.php' del tema.
?>