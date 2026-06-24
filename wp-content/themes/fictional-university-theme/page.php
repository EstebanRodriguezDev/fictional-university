<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
?>

<?php
// Se usa el Loop de WordPress aquí (aunque sea una página) para poder llamar
// a pageBanner() sin argumentos y que use automáticamente el título de la página actual.
while (have_posts()) {
  the_post();
  // Se llama pageBanner() sin argumentos para que use el título de la página actual,
  // manteniendo el diseño consistente con el resto del sitio.
  pageBanner();
?>

  <div class="container container--narrow page-section">
    <?php
    // Se obtiene el ID del padre para saber si esta página forma parte de una jerarquía,
    // lo que determina si hay que mostrar el menú lateral de subpáginas.
    $theParent = wp_get_post_parent_id(get_the_ID()); // Si es 0, la página no tiene padre y no se muestra navegación lateral.

    // Se muestra el breadcrumb de vuelta al padre solo si la página actual es hija de otra,
    // para que el usuario pueda volver fácilmente a la sección principal.
    if ($theParent) {
    ?>
      <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
          <!-- Se enlaza al padre con get_permalink() pasando el ID para no depender del slug,
               que podría cambiar si el editor renombra la página. -->
          <a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($theParent); ?></a> <span class="metabox__main"><?php the_title(); ?></span> <!-- Se muestra el título del padre para dar contexto de dónde está el usuario dentro de la jerarquía. -->
        </p>
      </div>
    <?php } ?>

    <?php
    // Se obtienen las subpáginas de la página actual para saber si hay que
    // mostrar un menú de navegación lateral con las páginas hermanas o hijas.
    $testArray = get_pages(array(
      'child_of' => get_the_ID()
    ));

    // Se muestra el menú lateral solo si la página tiene padre o tiene hijas,
    // para que páginas sueltas no muestren una navegación lateral vacía o sin sentido.
    if ($theParent or $testArray) {
    ?>
      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent); ?>"><?php echo get_the_title($theParent); ?></a></h2>
        <ul class="min-list">
          <?php
          // Se decide el ID de referencia según si la página actual es hija (muestra hermanas)
          // o padre (muestra sus propias hijas), para que la navegación siempre sea relevante.
          if ($theParent) {
            $findChildrenOf = $theParent;
          } else {
            $findChildrenOf = get_the_ID(); // Se usa el ID actual porque esta página es la raíz de la jerarquía.
          }
          // Se usa wp_list_pages() para no construir manualmente el listado de páginas,
          // ya que WordPress lo genera y gestiona automáticamente con el orden de menú correcto.
          // Se pasa 'title_li' => NULL para ocultar el encabezado que genera por defecto.
          // Se ordena por 'menu_order' para respetar el orden manual definido por el editor.
          wp_list_pages(array(
            'title_li' => NULL,
            'child_of' => $findChildrenOf,
            'sort_column' => 'menu_order'
          )); ?>
        </ul>
      </div>
    <?php } ?>
    <div class="generic-content">
      <?php the_content(); ?> <!-- Se usa the_content() para renderizar el contenido del editor de WordPress, permitiendo al editor enriquecer la página sin tocar código. -->
    </div>
  </div>
<?php }

// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>