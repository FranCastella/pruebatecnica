<?php
get_header();
?>
<div class="container">
<?php
while(have_posts()){
    the_post();
    echo the_content();
}
?></div>
<div class="container">
<?php
get_footer();
?>
</div>