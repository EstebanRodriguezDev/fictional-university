<?php
// get_header(): Función de WordPress que incluye el archivo 'header.php' del tema.
get_header();
?>

<?php
while (have_posts()) { // while (have_posts()): Inicia el "Loop" de WordPress. have_posts() devuelve true si hay posts (páginas en este caso) para mostrar.
 the_post(); // the_post(): Prepara los datos del post/página actual para ser usados por las funciones de plantilla.
 // Muestra el banner superior dinámico. Sin parámetros, usará el título de la página actual.
 pageBanner();
?>

 <div class="container container--narrow page-section">
  <?php
  // wp_get_post_parent_id(get_the_ID()): Función de WordPress que obtiene el ID de la página padre de la página actual.
  // get_the_ID(): Función de WordPress que devuelve el ID del post/página actual.
  $theParent = wp_get_post_parent_id(get_the_ID()); // $theParent: Variable que almacena el ID de la página padre. Si no hay padre, es 0.

  if ($theParent) { // if ($theParent): Condición que verifica si la página actual tiene una página padre (si $theParent es diferente de 0).
  ?>
   <div class="metabox metabox--position-up metabox--with-home-link">
    <p>
     <!-- get_permalink($theParent): Función de WordPress que obtiene la URL permanente de un post/página dado su ID. -->
     <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParent); ?></a> <span class="metabox__main"><?php the_title(); ?></span> <!-- get_the_title($theParent): Función de WordPress que obtiene el título de un post/página dado su ID. -->
    </p>
   </div>
  <?php } ?>

  <?php
  // get_pages(): Función de WordPress que recupera una lista de páginas.
  // 'child_of' => get_the_ID(): Argumento que filtra las páginas para que sean hijas de la página actual.
  $testArray = get_pages(array(
   'child_of' => get_the_ID()
  ));

  if ($theParent or $testArray) { // if ($theParent or $testArray): Condición que verifica si la página actual tiene un padre O si tiene páginas hijas.
  ?>
   <div class="page-links">
    <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent); ?>"><?php echo get_the_title($theParent); ?></a></h2>
    <ul class="min-list">
     <?php
     if ($theParent) {
      $findChildrenOf = $theParent; // $findChildrenOf: Variable que determina de qué ID se buscarán las páginas hijas (si la página actual es hija, se muestran sus hermanos; si es padre, se muestran sus hijos).
     } else {
      $findChildrenOf = get_the_ID(); // get_the_ID(): Obtiene el ID del post/página actual.
     }
     // wp_list_pages(): Función de WordPress que genera automáticamente una lista <li> de páginas.
     // 'title_li' => NULL: Oculta el título de la lista.
     // 'child_of' => $findChildrenOf: Muestra solo las páginas hijas del ID especificado.
     // 'sort_column' => 'menu_order': Ordena las páginas por el orden de menú definido en el panel de administración.
     wp_list_pages(array(
      'title_li' => NULL,
      'child_of' => $findChildrenOf,
      'sort_column' => 'menu_order'
     )); ?>
    </ul>
   </div>
  <?php } ?>
  <div class="generic-content">
   <form method="get" action="<?php echo esc_url(site_url('/')) ?>">
    <input type="search" name="s">
    <input type="submit" value="Search">
   </form>
  </div>
 </div>
<?php }

get_footer(); // get_footer(): Incluye el archivo 'footer.php' del tema.
?>