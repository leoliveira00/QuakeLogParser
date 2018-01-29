<?php
	include("conexao.class.php");
	require_once("scssphp/scss.inc.php");
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

	<script type="text/javascript" src="js/jquery-3.3.1.js"></script>
	<script src="js/jquery-confirm.js"></script>
	<link href="css/jquery-confirm.css" rel="stylesheet" type="text/css">

	<script type="text/javascript">
		$(document).ready(function(){
		   $('.divCarregando').fadeOut('fast');
		});


		function carregar(){
			$('.divCarregando').fadeIn('fast');
		    document.getElementById("frm_upld").submit();
		}

		function clickLink(param_link){
			switch(param_link){
				case "link_ranking":
					$(location).attr('href', 'index.php');
					break;
				case "link_relatorio":
					//$(location).attr('href', 'abre_rel.php');
					window.open('abre_rel.php', '_blank');
					break;
			}
		    
		}	

	</script>

</head>
<body>
<div class="divCarregando"><img id="imgCarregando" src="http://goo.gl/prjII7" width="150" height="70" /></div>
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

	<div class="divCentro">

		<h1><div id="kills"><img id="poison" src="svg/poison.svg">&nbsp;QUAKE LOG PARSER</div></h1>

		<form id="frm_upld" action="#" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
	      	</br>
			<label class="uploadArq" for='select_arq'>Upload Arquivo &#187;</label></br></br>
			<input id="select_arq" type="file" name="fileUpload" onChange="carregar()">

	      	<p class="botao" onclick="clickLink('link_ranking');"><a href="index.php">Ranking</a></p>

	      	<p class="botao" onclick="clickLink('link_relatorio');"><a>Relatório de Kills</a></p>

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
						$sql->Insert("games","Game_Name, Tot_Kills","'"."game_".($j+1)."','".$totalKills."'");
						$gameId = $sql->retorno;


						/*
						* Grava os Players
						*/
						for($i=0; $i<count($matrizPlayers); $i++){
							$sql->Insert("players","Nick_Name, Game_Id, Player_Kills","'".$matrizPlayers[$i][0]."','".$gameId."','".$matrizPlayers[$i][1]."'");								
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
					* Envia mensagem
					*/
					if(isset($sql->retorno) && $sql->retorno > 0){
						echo "<script type=\"text/javascript\"> 
						    $.confirm({
							    title: 'Concluído',
							    content: 'Arquivo processado com sucesso! Deseja ver o Ranking agora?',
								theme: 'black',
								animation: 'zoom',
							    buttons: {
							        'Sim, por favor': function () {
							        	$(location).attr('href', 'index.php');
							        },
							        'Não, obrigado': function () {
							        	$(location).attr('href', 'upload.php');			            
							        }
							    }
							});
						</script>";
					}
					else{
						echo "<script type=\"text/javascript\"> 
						    $.alert({
								title: 'Atenção!',
						        content: 'Falha no carregamento do arquivo.',
								theme: 'black',
								animation: 'zoom',
						        confirm: function(){}
						    });
						</script>";
					}					
					$sql = null;			

					/*
					* Limpa a conexão
					*/
					$sql = "";
				}//end if
		   	?>

	</div>

</body>
</html>