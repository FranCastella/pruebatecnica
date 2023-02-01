<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit; //solo carga cuando wordpress va a a desinstalar

global $wpdb;

//borra custom posts
$lista_portfolios = $wpdb->get_results("SELECT ID from {$wpdb->posts} where post_type='portfolio'");
foreach($lista_portfolios as $delete_porfolio){
  $wpdb->query("DELETE from {$wpdb->posts} where ID='" . $delete_porfolio->ID . "'");
}

//borra opciones
$wpdb->query("DELETE from {$wpdb->prefix}options where option_name LIKE 'portfolios_%'");
//borra categorias (estan en 3 bases relacionadas) - term_taxonomy tiene id a las otras 2
$lista_categorias = $wpdb->get_results("SELECT term_taxonomy_id from {$wpdb->term_taxonomy} where taxonomy='portfolios_category'");
foreach($lista_categorias as $categoria){
    $wpdb->query("DELETE FROM {$wpdb->terms}  WHERE term_id = '{$categoria->term_taxonomy_id}'");
    $wpdb->query("DELETE FROM {$wpdb->term_relationships}  WHERE object_id = '{$categoria->term_taxonomy_id}'");
    $wpdb->query("DELETE FROM {$wpdb->term_taxonomy}  WHERE term_taxonomy_id = '{$categoria->term_taxonomy_id}'");
}


?>