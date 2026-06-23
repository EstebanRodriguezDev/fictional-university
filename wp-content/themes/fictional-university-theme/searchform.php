   <!-- Formulario de búsqueda nativo de WordPress. 
        Apunta a la URL principal del sitio (action) y usa el método GET. -->
   <form class="search-form" method="get" action="<?php echo esc_url(site_url('/')) ?>">
      <label for="s" class="headline headline--medium">Perform a New Search</label>
      <div class="search-form-row">
         <!-- El input debe tener el atributo name="s" para que WordPress lo reconozca como término de búsqueda -->
         <input placeholder="What are you looking for?" class="s" type="search" name="s">
         <input class="search-submit" type="submit" value="Search">
      </div>
   </form>