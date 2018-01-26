<?php
	include("conexao.class.php");
	require_once("Compiler-SASS-To-PHP-master/scssphp/scss.inc.php");
	use Leafo\ScssPhp\Compiler;
	$compilar = new Compiler();
	$arquivo = file_get_contents("css/estilo.scss");
	$arquivo = str_replace('$padrao', '#000000', $arquivo);
	$estilo = $compilar->compile($arquivo);
	error_reporting(E_WARNING);
	error_reporting(E_NOTICE);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Gravação Arquivo de Log</title>
	<style type="text/css">
		
		<?php
			echo $estilo;
		?>

	</style>
</head>
<body>

	<?php
		if(isset($_FILES['fileUpload'])){

			/*
			* Upload do arquivo temporário
			*/
	   		date_default_timezone_set("Brazil/East"); //Define timezone padrão
	      	$ext = strtolower(substr($_FILES['fileUpload']['name'],-4)); //Extensão do arquivo
	      	$new_log_game = "games_" . date("Y.m.d-H.i.s") . $ext; //Define um novo nome para o arquivo temporário
	      	$dir = 'arq/'; //Diretório para uploads
	      	move_uploaded_file($_FILES['fileUpload']['tmp_name'], $dir.$new_log_game);	      	
	   }
	?>

	<div class="container">
		<div class="box">
			<h1>UPLOAD LOG</h1>

			<form action="#" method="POST" enctype="multipart/form-data">
		      	<input type="file" name="fileUpload">
		      	<input type="submit" value="Enviar">
		   	</form>

		   	<?php
		   		/*
		   		* Processa se recebeu o arquivo
		   		*/
		   		if(isset($_FILES['fileUpload'])){

		   			/*
		   			* Classe de manipulação de dados
		   			*/
		   			$sql = new CONEXAO();

					/*
			      	* Leitura do arquivo
			      	*/
			      	$arquivo = fopen($dir.$new_log_game, "r");
					$arrDados = array();					
					
					/*
					* Transfere os dados que interessam do arquivo para um array (mantendo os caracteres especiais)
					*/
					while (!feof ($arquivo)) {
						if ($linha = fgets($arquivo)){
							if(strripos("[".$linha."]", "InitGame") || strripos("[".$linha."]", "ClientUserinfoChanged") || strripos("[".$linha."]", "killed")){
								$arrDados[] = htmlspecialchars($linha);
							}
						}
					}


					/*
					* Pega as posições iniciais e finais de cada blogo de game do arquivo
					*/
					for($i=0; $i<count($arrDados); $i++){
						if(strripos("[".$arrDados[$i]."]", "InitGame")){
							$arrPosIni[] = $i;
						}
					}
					for($i=1; $i<=count($arrPosIni); $i++){
						if($i == count($arrPosIni)){
							$dif = count($arrDados);
						}
						else{
							$dif = $arrPosIni[$i] - $arrPosIni[$i-1]; 
						}
						$arrPosFim[] = $dif;
					}

					/*
					* Recupera os dados das partidas
					*/
					for($j=0; $j < count($arrPosIni); $j++){
						
						/*
						* Fatia os dados por jogo com base nas posições iniciais e finais de cada um
						*/
						$partida = array_slice($arrDados, $arrPosIni[$j], $arrPosFim[$j]);		

						/*
						* Monta vetor com os Players
						*/
						foreach ($partida as $linha) {
							$playerName[] = "";
							if(strripos("[".$linha."]", "ClientUserinfoChanged")){				
								
								/*
								* Usa caracteres de posições chave
								*/
								$ini = strpos("[".$linha."]", "n\\")+1;
								$fim = strpos("[".$linha."]", "\\t\\") - $ini-1;

								/*
								* Evita a gravação de valores duplicados
								*/
								if (!in_array(substr($linha, $ini,$fim), $playerName)) { 
									$playerName[] = substr($linha, $ini,$fim);
								}
							}	
						}

						/*
						* Elimina valores nulos
						*/
						$playerName = array_values(array_filter($playerName));

						/*
						* Converte o vetor em matriz (palyerName, totKills)
						*/
						$matrizPlayers[]="";
						for($i=0; $i<count($playerName); $i++){
							$matrizPlayers[$i] = array($playerName[$i], 0);
						}
						$matrizPlayers = array_values(array_filter($matrizPlayers));

					
						/*
						* Com a matriz de players, verifica a quantidade de mortes na partida para cada player
						*/
						$totalKills = 0;
						foreach ($partida as $linha) {

							if(strpos($linha, "Kill: ")){

								for($i=0; $i<count($matrizPlayers); $i++){

									if(strpos($linha, $matrizPlayers[$i][0]." killed ".$matrizPlayers[$i][0])){
										//usuário se matou, não pontua nada
										continue;	
									}
									elseif(strpos($linha, " killed ".$matrizPlayers[$i][0]." by") && !strpos($linha, $matrizPlayers[$i][0]." killed ".$matrizPlayers[$i][0])){		

										if(strpos($linha, "killed") < strpos($linha, $matrizPlayers[$i][0])){
											if(strpos($linha, "world")>0){
												//usuário foi morto por <world>, perde kill
												$matrizPlayers[$i][1]--;
											}							
										}
									}
									elseif (strpos($linha, $matrizPlayers[$i][0]." killed")) {
										//usuário matou, ganha kill
										$matrizPlayers[$i][1]++;
									}
								}

								$totalKills++;
							}
						}


						/*
						* Grava as partidas
						*/
						$sql->Insert("game","Game_Name, Tot_Kills","'"."game_".($j+1)."','".$totalKills."'");
						$gameId = $sql->retorno;


						/*
						* Grava os Players
						*/
						for($i=0; $i<count($matrizPlayers); $i++){
							$sql->Insert("player","Nick_Name, Game_Id, Player_Kills","'".$matrizPlayers[$i][0]."','".$gameId."','".$matrizPlayers[$i][1]."'");								
							$playerId = $sql->retorno;
						}

						/*
						* Monta uma string com os kills que cada usuário sofreu e a causa da morte
						*/
						$stringKills="";
						foreach ($partida as $linha) {
							if(strpos($linha, " Kill: ")){
								for($i=0; $i<count($matrizPlayers); $i++){
									if(strpos($linha, " killed ".$matrizPlayers[$i][0])){
										//foi morto
										if($stringKills == ""){
											$stringKills = $matrizPlayers[$i][0].",".substr($linha, strpos($linha, " MOD_"));
										}
										else{
											$stringKills = $stringKills."|".$matrizPlayers[$i][0].",".substr($linha, strpos($linha, " MOD_"));
										}
									}
								}
							}
						}

						/*
						* Grava os kills
						*/
						$vetorKills = explode("|", $stringKills);
						for($i=0; $i<count($vetorKills); $i++){
							$kills = explode(",", $vetorKills[$i]);
							$kills = array_values(array_filter($kills));
							if(isset($kills[0])){
								$sql->Insert("kills","Game_Id, Nick_Name, Causa_Mortis","'".$gameId."','".$kills[0]."','".$kills[1]."'");
							}
						}
					}
					
					/*
					* Fecha a leitura do arquivo
					*/
					fclose ($arquivo);

					/*
					* Deleta o arquivo temporário
					*/
					unlink($dir.$new_log_game);

					/*
					* Envia mensagem e redireciona
					*/
					if($sql->retorno == "OK"){						
						$sql = null;						
						echo '<script>alert("Arquivo processado com sucesso!");</script>';
						echo "<script>window.location = 'index.php';</script>"; 
					}
					else{
						$sql = null;
						echo '<script>alert("Arquivo processado com sucesso!");</script>';
					}

					/*
					* Limpa a conexão
					*/
					$sql = "";
				}//end if
		   	?>

		</div>
	</div>

</body>
</html>