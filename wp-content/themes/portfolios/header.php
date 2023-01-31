<html>
    <head>
		<meta charset="utf-8">
  		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<?php wp_head()?>
    </head>
	
<?php 
global $wp;
$actual_url = home_url($wp->request);
?>
	
<body>
	<div class="container navegacion">
	<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
		<a href="/" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-dark text-decoration-none">
			<svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
		</a>
		<ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
			<li><a href="<?php echo site_url() ?>" class="nav-link px-2 link-secondary<?php if(site_url()==$actual_url) echo " active" ?>">Inicio</a></li>
			<li><a href="<?php echo site_url()."/portfolio/"?>" class="nav-link px-2 link-dark<?php if(site_url()."/portfolio"==$actual_url) echo " active" ?>">Portfolio</a></li>
			<li><a href="<?php echo site_url()."/sobre-mi/"?>" class="nav-link px-2 link-dark<?php if(site_url()."/sobre-mi"==$actual_url) echo " active" ?>">Sobre mi</a></li>
			<li><a href="<?php echo site_url()."/contacto/"?>" class="nav-link px-2 link-dark<?php if(site_url()."/contacto"==$actual_url) echo " active" ?>">Contacto</a></li>
		</ul>
	</div>
