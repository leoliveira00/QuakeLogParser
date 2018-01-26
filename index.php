<?php
	include("conexao.class.php");
	require_once("Compiler-SASS-To-PHP-master/scssphp/scss.inc.php");	
	
	/*
	* Instancia compilador SASS
	*/
	use Leafo\ScssPhp\Compiler;
	$compilar = new Compiler();


	$arquivo = file_get_contents("css/estilo.scss");
	$arquivo = str_replace('$padrao', '#000000', $arquivo);
	

	/*
	* Compila o estilo
	*/
	$estilo = $compilar->compile($arquivo);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Teste</title>
	<style type="text/css">
		
		<?php
			echo $estilo;
		?>

	</style>
</head>
<body>

	<div id="divCentro">
		<h1>RANKING</h1>

		<form name="formBusca" method="POST">
            <p>
            	<input type="text" id="nome" placeholder="Busca por Nome" name="nome" /><input type="submit" value="Buscar">
            </p>
        </form>
		
		<table id="dados" summary="RANKING" >
		  <tr>
		    <th class="left"><span class="left">NAME</span></th>
		    <th class="right"><div id="kills"><img id="poison" src="svg/poison.svg">&nbsp;KILLS</div></th>
		  </tr>

        	<?php

        		/*
	   			* Classe de manipulação de dados
	   			*/
        		$sql = new CONEXAO();

        		/*
        		* Requisita os dados do grid
        		*/
        		$nome="";
        		if(isset($_POST['nome'])){
					$nome = $_POST['nome'];
        		}
        		$sql->GetDadosGrid($nome);

        		/*
        		* Exibe os dados requisitados
        		*/
        		for($i=0; $i<count($sql->retorno); $i++){
        			if($i%2 == 0){
					     echo "<tr class='alter'>";
					     echo "	<td>".$sql->retorno[$i]['Nick_Name']."</td>";
					     echo "	<td class='right'>".$sql->retorno[$i]['Ranking']."</td>";
					     echo "</tr>";
					} else {
					     echo "<tr>";
					     echo "	<td>".$sql->retorno[$i]['Nick_Name']."</td>";
					     echo "	<td class='right'>".$sql->retorno[$i]['Ranking']."</td>";
					     echo "</tr>";
					}
        		}

        		/*
        		* Limpa a conexão
        		*/
        		$sql = "";
        	?>
		
		</table>
	</div>
</body>
</html>