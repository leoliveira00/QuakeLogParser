<?php
	include("conexao.class.php");
	require_once("scssphp/scss.inc.php");

	//error_reporting(E_WARNING);
	//error_reporting(E_NOTICE);
	
	/*
	* Compilador SASS
	*/
	use Leafo\ScssPhp\Compiler;
	$compilar = new Compiler();
	$arquivo = file_get_contents("css/estilo.scss");
	
	//exemplo de uso
	$arquivo = str_replace('$padrao', '#000000', $arquivo);
	$arquivo = str_replace('$bgColor', '#000000', $arquivo);

	//compila o css
	$estilo = $compilar->compile($arquivo);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Quake Log Parser - Leonardo Oliveira</title>	

	<style type="text/css">		
		<?php
			/*
			* Executa a inserção do estilo compilado
			*/
			echo $estilo;
		?>
	</style>
	
	<!--JQuery-->
	<script type="text/javascript" src="js/jquery-3.3.1.js"></script>
	<script src="js/jquery-confirm.js"></script>
	<link href="css/jquery-confirm.css" rel="stylesheet" type="text/css">

	<script type="text/javascript">
		$(document).ready(function(){
		   $('.divCarregando').fadeOut('fast');//esconde a mensagem "carregando"
		});
	</script>

</head>
<body>

	<div class="divCarregando">
		<img id="imgCarregando" src="http://goo.gl/prjII7" width="150" height="70" />
	</div>

	<div id="containerPai">
	
		<div id="containerPrincipal">
			
			<?php include "menu.php"; ?>		
			
			<div id="divCentro">

				<?php 
					include "ranking.php"; 
				?>

			</div>

		</div>
	</div>

</body>
</html>