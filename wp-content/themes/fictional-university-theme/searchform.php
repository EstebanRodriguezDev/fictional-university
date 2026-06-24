   <!-- Se usa un formulario GET (en vez de POST) porque la búsqueda produce una URL compartible
        que el usuario puede guardar o enviar. WordPress detecta automáticamente el parámetro '?s='. -->
   <form class="search-form" method="get" action="<?php echo esc_url(site_url('/')) ?>">
      <label for="s" class="headline headline--medium">Perform a New Search</label>
      <div class="search-form-row">
         <!-- El atributo name="s" es obligatorio porque WordPress usa ese parámetro de la URL
              para identificar la búsqueda y preparar la consulta correctamente. -->
         <input placeholder="What are you looking for?" class="s" type="search" name="s">
         <input class="search-submit" type="submit" value="Search">
      </div>
   </form>