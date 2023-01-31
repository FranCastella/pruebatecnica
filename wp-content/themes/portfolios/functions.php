<?php

if(!defined('ABSPATH')) exit; // Se sale si se accede directamente

add_theme_support( 'align-wide' ); //blocks

//carga los scripts del tema
function cargar_estilos_plantilla(){
    wp_enqueue_style('plantilla-css',get_stylesheet_uri());
    wp_enqueue_style('bootstrap', get_template_directory_uri(__FILE__).'/assets/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap', get_template_directory_uri(__FILE__).'/assets/bootstrap.min.js' );
}

add_action( 'wp_enqueue_scripts', 'cargar_estilos_plantilla');





















?>