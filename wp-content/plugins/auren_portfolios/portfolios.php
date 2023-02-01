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
        //Para poder poner thumbnails a los portfolios
        add_theme_support( 'post-thumbnails' );
        //Ajustes plugin
        add_action('admin_menu' , array($this, 'portfolios_pagina_ajustes'));
        add_action('admin_init', array($this, 'ajustes'));
    }

    function portfolio_post_type(){
        register_post_type('portfolio',
            array(
              'public' => true,
              'label' => 'Portfolios',
              'capability_type'=>'post',
              'menu_icon' => 'dashicons-welcome-widgets-menus',
              'hierarchical' => false,
              'has_archive' => true,
              'show_in_rest' => true,
              'supports'=> array( 'title', 'editor', 'author', 'thumbnail','excerpt','custom-fields','comments','taxonomy')
          ));
        //Añadir categorias personalizadas solo para portfolio (no usar por defecto)
        register_taxonomy('portfolios_category','portfolio',array('hierarchical' => true, 'label' => 'Categorías', 'query_var'=> true, 'rewrite'=> true,'show_admin_column'=>true));
      }
    
    /* AJUSTES */

    function portfolios_pagina_ajustes() {
    add_submenu_page('edit.php?post_type=portfolio', 'Custom Settings', 'Ajustes', 'edit_posts', 'portfolios-pagina-ajustes', array($this,'nuestroHTML'));
    }

    function ajustes(){
        settings_errors(); // esto es añadido automaticamente en el menu ajustes pero no en los demás
        add_settings_section('portfolios_first_section', null, null,'portfolios-pagina-ajustes');

        add_settings_field('portfolio_orden','Orden de los portfolios por fecha',array($this, 'ajustes_orden_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_orden',array('sanitize_callback' => array($this, 'sanitizeOrden'), 'default' => '0'));

        add_settings_field('portfolio_filtro','Añadir filtro JS',array($this, 'ajustes_filtro_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_filtro',array('sanitize_callback' => array($this, 'sanitizeFiltro'), 'default' => '0'));
        
        add_settings_field('portfolios_categorias','Categorías a añadir en el filtro (separar por una coma)',array($this, 'ajustes_categorias_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_categorias',array('sanitize_callback' => array($this, 'sanitizeTextArea'), 'default' => ''));
    }

    function nuestroHTML(){
        ?>

        <div class="wrap">
            <h1>Ajustes Portfolios</h1>
            <form action="options.php" method="POST">
                <?php 
                settings_fields('portfoliosplugin');
                do_settings_sections('portfolios-pagina-ajustes');
                submit_button();
                ?>
            </form>
        </div>
     <?php   }

    function ajustes_filtro_HTML(){ ?>
        <input type="hidden" name="portfolios_filtro" value="0">
        <input type="checkbox" name="portfolios_filtro" value="1" <?php  checked(1, get_option('portfolios_filtro'), true);  ?> />
    <?php }

    function ajustes_orden_HTML(){ ?>
        <select name="portfolios_orden">
            <option value="0" <?php selected(get_option('portfolios_orden'),'0') ?> > <?php echo __('DESC.', 'wcpdomain') ?></option>
            <option value="1" <?php selected(get_option('portfolios_orden'),'1') ?> ><?php echo __('ASC.', 'wcpdomain') ?></option>
        </select>
    <?php }

    function ajustes_categorias_HTML(){
        $lista_categorias = get_terms( array('taxonomy' => 'portfolios_category','hide_empty' => false )); //recoge categorias asociadas a portfolios y no filtra vacias
        ?>
        <textarea name="portfolios_categorias" rows="5" cols="100" ><?php echo  get_option('portfolios_categorias')?></textarea>
        <select multiple disabled style="position:fixed"><?php
        foreach($lista_categorias as $categoria){
            echo "<option value=".$categoria->term_id.">".$categoria->name."</>";
        } ?>
        </select>
        <?php }

    function sanitizeOrden($input){
        if($input !='0' AND $input !='1'){
            //1 setting asociado 2 codigo error(no importa) 3 mensaje de error
            add_settings_error('portfolios_orden','portfolios_orden_error','Solo puedes elegir esas 2');
            return get_option('portfolios_orden');
        }
        return $input;
    }
    function sanitizeFiltro($input){
        if($input !='0' AND $input !='1'){
            //1 setting asociado 2 codigo error(no importa) 3 mensaje de error
            add_settings_error('portfolios_filtro','portfolios_orden_error','Solo dos opciones en el filtro.');
            return get_option('portfolios_filtro');
        }
        return $input;
    }
    function sanitizeTextArea($input){
        $lista_categorias = get_terms( array('taxonomy' => 'portfolios_category','hide_empty' => false ));
        echo "<pre>";
        var_dump($lista_categorias);
        echo "</pre>";
        echo "<pre>";
        var_dump($input);
        echo "</pre>";
        $lista = explode(',',$input);
        $lista = array_filter($lista);
        echo "<pre>";
        var_dump($lista);
        echo "</pre>";
        exit;
        return $input;
    }


  /////////////  BASE DE DATOS ///////////////////////

  function get_portfolios($params){
    echo "<pre>";
    echo "</pre>";

    if(isset($params['numero_por_pagina']) ? $numero_por_pagina = $params['numero_por_pagina'] : $numero_por_pagina=3);

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