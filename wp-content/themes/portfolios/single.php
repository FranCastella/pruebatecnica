<?php
get_header();
?>
<div class="container">
<?php
while(have_posts()){
    echo "<p>Año: ".get_the_date('Y')."</p>";
    $lista_categorias = get_the_terms( get_the_ID(),'portfolios_category');
    if($lista_categorias){
        $texto_categorias="Categorias: ";
        foreach($lista_categorias as $key){
            $texto_categorias.=$key->name." ";
         }
         echo $texto_categorias."<hr>";
    }
    the_post();
    echo the_content();
}
?></div>
<div class="container">
<?php
get_footer();
?>
</div>