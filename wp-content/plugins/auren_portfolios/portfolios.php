<?php
/*
Plugin Name: Portfolios
Description: Crea una nueva tipología (Portfolio) con sus propias categorías y ajustes, además crea un bloque personalizado para mostrar los elementos.
Version: 1.0
Author: Fran Pardo
Author URI: http://www.fran-pardo.com
Text Domain: portfolios_domain
Domain Path: /languages
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
        //inicia languages
        add_action('init', array($this, 'languages'));
    }

    function languages() {
        load_plugin_textdomain('portfolios_domain', false, dirname(plugin_basename(__FILE__)) . '/languages');
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
        register_taxonomy('portfolios_category','portfolio',array('hierarchical' => true, 'label' => __('Categorías', 'portfolios_domain'), 'query_var'=> true, 'rewrite'=> true,'show_admin_column'=>true));
      }
    
    /* AJUSTES */

    //Añade los ajustes dentro del propio menu portfolios
    function portfolios_pagina_ajustes() {
    add_submenu_page('edit.php?post_type=portfolio', __('Ajustes personalizables', 'portfolios_domain'), __('Ajustes', 'portfolios_domain'), 'edit_posts', 'portfolios-pagina-ajustes', array($this,'nuestroHTML'));
    }

    function ajustes(){
        settings_errors(); // esto es añadido automaticamente en el menu ajustes pero no en los demás, sirve para mostrar mensajes de sanitización
        add_settings_section('portfolios_first_section', null, null,'portfolios-pagina-ajustes');

        add_settings_field('portfolio_orden',__('Orden de los portfolios por fecha', 'portfolios_domain'),array($this, 'ajustes_orden_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_orden',array('sanitize_callback' => array($this, 'sanitizeOrden'), 'default' => '0'));

        add_settings_field('portfolio_filtro',__('Añadir filtro JS', 'portfolios_domain'),array($this, 'ajustes_filtro_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_filtro',array('sanitize_callback' => array($this, 'sanitizeFiltro'), 'default' => '0'));
        
        add_settings_field('portfolios_categorias',__('Categorías a añadir en el filtro (separar por una coma)', 'portfolios_domain'),array($this, 'ajustes_categorias_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_categorias',array('sanitize_callback' => array($this, 'sanitizeTextArea'), 'default' => ''));

        add_settings_field('portfolios_customHTML',__('Customizar clase de cada elemento (HTML)<br/> Ej: col-md-3 portfolio', 'portfolios_domain'),array($this, 'ajustes_custom_HTML'),'portfolios-pagina-ajustes','portfolios_first_section');
        register_setting('portfoliosplugin','portfolios_customHTML',array('sanitize_callback' => array($this, 'sanitizeCustomHTML'), 'default' => 'col-md-6'));
    }

    //Estructura principal de el menú Ajustes
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

        //html de cada campo Ajustes
    function ajustes_filtro_HTML(){ ?>
        <input type="hidden" name="portfolios_filtro" value="0">
        <input type="checkbox" name="portfolios_filtro" value="1" <?php  checked(1, get_option('portfolios_filtro'), true);  ?> />
    <?php }

    function ajustes_orden_HTML(){ ?>
        <select name="portfolios_orden">
            <option value="0" <?php selected(get_option('portfolios_orden'),'0') ?> > <?php echo __('DESC.', 'pfdomain') ?></option>
            <option value="1" <?php selected(get_option('portfolios_orden'),'1') ?> ><?php echo __('ASC.', 'pfdomain') ?></option>
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

    function ajustes_custom_HTML(){ ?>
        <span>&lt;div class=&#34;</span><textarea name="portfolios_customHTML" rows="1" cols="25" ><?php echo  get_option('portfolios_customHTML')?></textarea><span>&#34;</span>
        <?php }

        /* Sanitización de Ajustes */

    function sanitizeOrden($input){
        if($input !='0' AND $input !='1'){
            //1 setting asociado 2 codigo error(no importa) 3 mensaje de error
            add_settings_error('portfolios_orden','portfolios_orden_error',__('Error al introducir el campo de orden', 'portfolios_domain'));
            return get_option('portfolios_orden');
        }
        return $input;
    }
    function sanitizeFiltro($input){
        if($input !='0' AND $input !='1'){
            //1 setting asociado 2 codigo error(no importa) 3 mensaje de error
            add_settings_error('portfolios_filtro','portfolios_orden_error',__('Error al introducir el campo de filtro', 'portfolios_domain'));
            return get_option('portfolios_filtro');
        }
        return $input;
    }
    function sanitizeTextArea($input){
        $lista_categorias_string=array();
        $lista_categorias = get_terms(array('taxonomy' => 'portfolios_category','hide_empty' => false )); //recogemos categorias asociadas a portfolios
        foreach($lista_categorias as $cat){//las convertimos a un array simple de strings
            array_push($lista_categorias_string, $cat->name);
        }
        $lista = explode(',',$input); //montamos array quitando comas
        $lista = array_filter($lista); // quitamos arrays vacios

        foreach($lista as $list){ //comprobamos si las categorias que ponemos existen
            if(!in_array($list,$lista_categorias_string)){
                add_settings_error('portfolios_categorias','portfolios_orden_error',__('El siguiente campo no se encuentra entre las categorias: ', 'portfolios_domain').$list);
                return get_option('portfolios_categorias');
            }
        }
        return $input;
    }
    function sanitizeCustomHTML($input){
        return $input;
    }

    // salida HTML lista portfolios
  function get_portfolios(){
    //recogemos opciones del plugin
    $orden =     (get_option('portfolios_orden')==1) ? 'ASC': 'DESC';
    $filtro_js = (get_option('portfolios_filtro')==1) ? true: false;
    $categorias = get_option('portfolios_categorias');
    $customHTML = get_option('portfolios_customHTML');

    echo "<div id='portfolios_container'>";
    if($filtro_js && !empty($categorias)){
        $categorias_array = explode(',',$categorias);
        wp_enqueue_script('js_filtro', plugin_dir_url( __FILE__ ).'/assets/portfolios.js');
        wp_enqueue_script( 'bootstrap', get_template_directory_uri(__FILE__).'/assets/bootstrap.min.js' );
        echo "<div>";
            echo "<button type='button' id='portfolios_ALL'>".__('Todos','pfdomain')."</button>";
        foreach($categorias_array as $key => $value){
            echo "<button type='button' id='portfolios_".$value."' class='portfolios_filtro_boton boton_filtrado'>".$value."</button>";
        }
        echo "</div>";

    }

    $args = array(
        'post_type'=>'portfolio',
        'posts_per_page' => 100,
        'order' => $orden
    );

    $query = new WP_Query($args);

    while( $query->have_posts() ){
        if ( $query->have_posts() ); $query->the_post();
        $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'thumbnail');
        $lista_categorias = get_the_terms( get_the_ID(),'portfolios_category');
        ?>
        <div class="portfolio <?php echo $customHTML ?>">
        <?php if ($thumbnail){ ?>
        <img src="<?php echo $thumbnail[0] ?>"></img>
        <?php } if($lista_categorias) {
            $categorias="<div class='portfolios_cat'>";
            foreach($lista_categorias as $key){
               $categorias.="<span class='portfolio_".$key->name."'>".$key->name."</span> ";
            }
            
            echo $categorias.="</div>";
         }else{
            echo "<div class='portfolios_cat'></div>";
         } ?>
        <h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
        </div>

        <?php
    } 


    echo "</div>";

    }


  
}

$PluginPortfolios = new Portfolios();




?>