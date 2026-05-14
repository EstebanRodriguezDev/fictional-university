<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php de tu tema para mantener la cabecera consistente.
get_header();
// Genera el banner superior usando la función reutilizable pageBanner().
// Define estáticamente el título y subtítulo para la página principal de todos los programas.
pageBanner(array(
  'title' => 'All Programs',
  'subtitle' => 'There is something for everyone. Have a look around.',
));
?>

<div class="container container--narrow page-section">
  <ul class="link-list min-list">
    <?php
    // have_posts(): Función que devuelve true si hay contenidos (programas) en la base de datos para mostrar.
    // while(): Inicia el "Loop" de WordPress para recorrer cada uno de los resultados encontrados.
    while (have_posts()) {
      // the_post(): Prepara los datos del programa actual (título, link, etc.) para que las funciones de abajo puedan usarlos.
      the_post();
    ?>
      <!-- the_permalink(): Imprime la dirección URL única del programa actual para que el usuario pueda hacer clic. -->
      <!-- the_title(): Imprime el nombre o título del programa académico actual. -->
      <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
    <?php }
    // paginate_links(): Genera automáticamente la numeración de páginas (1, 2, Siguiente...) si hay muchos programas.
    echo paginate_links();
    ?>
  </ul>
</div>

<?php
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php de tu tema.
get_footer();
?>