<!-- Se usa una plantilla parcial reutilizable para no duplicar el markup de la tarjeta
     del profesor en cada lugar donde se necesite mostrar (listado de búsqueda, página de programa, etc.). -->
<div class="post-item">
  <li class="professor-card__list-item">
    <!-- Se usa the_permalink() en el enlace de la tarjeta para que el usuario pueda navegar
         al perfil completo del profesor haciendo clic en cualquier parte de la tarjeta. -->
    <a class="professor-card" href="<?php the_permalink(); ?>">
      <!-- Se usa el tamaño 'professorLandscape' para cargar la versión optimizada de la imagen
           que WordPress generó al subir la foto, evitando que el navegador descargue una imagen más grande de lo necesario. -->
      <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?> ">
      <!-- Se muestra el nombre del profesor en la tarjeta para que el usuario identifique
           al docente antes de hacer clic en su perfil. -->
      <span class="professor-card__name"><?php the_title(); ?></span>
    </a>
  </li>
</div>