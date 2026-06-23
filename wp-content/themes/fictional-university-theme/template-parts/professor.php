<!-- Plantilla reutilizable para mostrar la tarjeta de presentación de un profesor -->
<div class="post-item">
  <li class="professor-card__list-item">
    <!-- the_permalink(): Genera el enlace a la página individual del profesor -->
    <a class="professor-card" href="<?php the_permalink(); ?>">
      <!-- the_post_thumbnail_url(): Obtiene la URL de la imagen destacada usando el tamaño personalizado 'professorLandscape' -->
      <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?> ">
      <!-- the_title(): Muestra el nombre del profesor -->
      <span class="professor-card__name"><?php the_title(); ?></span>
    </a>
  </li>
</div>