<?php
/*
Plugin Name: Portfolios
Description: Crea una nueva tipología (Portfolio) y crea bloques para mostrar tanto listado de elementos, detalles de elementos como modificación de datos.
Version: 1.0
Author: Fran Pardo
Author URI: http://www.fran-pardo.com
*/

class Portfolios{
    function __construct(){
        //registro del post type portfolio
        add_action('init',array($this, 'portfolio_post_type'));
    }

    function portfolio_post_type(){
      register_post_type('portfolio', array(
          'public' => true,
          'label' => 'Portfolios',
          'capability_type'=>'post',
          'menu_icon' => 'dashicons-welcome-widgets-menus',
          'hierarchical' => false,
          'has_archive' => true,
          'show_in_rest' => true,
          'supports'=> array( 'title', 'editor', 'author', 'thumbnail','excerpt','custom-fields')
      ));
  }


  function get_portfolios($params){
    echo "<pre>";
    echo "</pre>";

    if(isset($params['numero_por_pagina']) ? $numero_por_pagina = $params['numero_por_pagina'] : $numero_por_pagina=2);

    //construye el listado con paginación
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $args = array(
        'post_type'=>'portfolio',
        'posts_per_page' => $numero_por_pagina,
        'paged' => $paged,
    );

    $query = new WP_Query($args);

    while( $query->have_posts() ){
        if ( $query->have_posts() ); $query->the_post(); ?>
        <div>
            <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
            <p><?php the_excerpt();?></p>
            <span><?php echo get_the_category_list()?></span>
            <hr>
        </div>
        <?php
    } 

    echo paginate_links( array(
        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
        'total'        => $query->max_num_pages,
        'current'      => max( 1, get_query_var( 'paged' ) ),
        'format'       => '?paged=%#%',
        'show_all'     => false,
        'type'         => 'plain',

    ) );
    wp_reset_postdata(); 


  }

  function details_portfolio(){

  }
  


}

$PluginPortfolios = new Portfolios();





?>