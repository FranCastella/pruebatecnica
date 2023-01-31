<?php
get_header();
?>

<div class="container">

<?php

while(have_posts()){
    the_post();

    echo the_content();
}


get_footer();
?>