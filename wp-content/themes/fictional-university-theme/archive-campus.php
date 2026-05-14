<?php
// get_header(): Función de WordPress que busca e incluye el archivo header.php de tu tema para mantener la cabecera consistente.
get_header();
// Genera el banner superior usando la función reutilizable pageBanner().
// Define estáticamente el título y subtítulo para la página principal de todos los programas.
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
    data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>"></div>
  <?php }

  echo paginate_links();
  ?>
 </div>
</div>

<?php
// get_footer(): Función de WordPress que busca e incluye el archivo footer.php de tu tema.
get_footer();
?>