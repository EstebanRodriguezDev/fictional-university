<?php
// Se incluye el header para garantizar que toda la cabecera HTML, los estilos y scripts
// globales del sitio estén presentes antes del contenido de esta página.
get_header();
// Se usa pageBanner() con textos fijos porque esta es la página de listado de todos los campus,
// no una página individual, por lo que no tiene un título dinámico propio en la base de datos.
pageBanner(array(
  'title' => 'Our Campuses',
  'subtitle' => 'We have several conveniently located campuses.',
));
?>

<div class="container container--narrow page-section">
  <div class="acf-map">
    <?php
    while (have_posts()) {
      the_post();
      $mapLocation = get_field('map_location');
    ?>
      <div class="marker"
        data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
        <h3>
          <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
          </a>
        </h3>
        <p><?php echo $mapLocation['address']; ?></p>
      </div>
    <?php } ?>
  </div>
</div>

<?php
// Se incluye el footer para cerrar correctamente el HTML y cargar los scripts que
// WordPress y los plugins necesitan inyectar al final del body.
get_footer();
?>