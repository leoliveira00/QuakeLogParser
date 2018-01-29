<style type="text/css">html{background-color: #000000;}	</style>
<?php

	include("conexao.class.php");

	/*
	* Processa se recebeu o arquivo
	*/
	if(isset($_FILES['fileUpload'])){

		/*
		* Efetua o upload do arquivo temporário
		*/
		date_default_timezone_set("Brazil/East"); //Timezone padrão
	  	$ext = strtolower(substr($_FILES['fileUpload']['name'],-4)); //Extensão do arquivo
	  	$new_log_game = "games_" . date("Y.m.d-H.i.s") . $ext; //Define um novo nome para o arquivo temporário
	  	$dir = 'arq/'; //Diretório para uploads
	  	move_uploaded_file($_FILES['fileUpload']['tmp_name'], $dir.$new_log_game);//sobe o arquivo


	  	//conecta
		$sql = new CONEXAO();


		/*
	  	* Lê o arquivo temporário
	  	*/
	  	$arquivo = fopen($dir.$new_log_game, "r");
		$arrDados = array();					

		
		/*
		* Transfere os dados que interessam do arquivo para um array
		*/
		while (!feof ($arquivo)) {
			if ($linha = fgets($arquivo)){
				if(strripos("[".$linha."]", "InitGame") || strripos("[".$linha."]", "ClientUserinfoChanged") || strripos("[".$linha."]", "killed")){
					$arrDados[] = htmlspecialchars($linha);//mantém caracteres especiais
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
		* Desfaz a conexção
		*/
		$sql = null;			

	}//end if


	/*
	* Retorna para o index
	*/
	echo "<script type=\"text/javascript\">
			window.location.href='index.php';
		 </script>";
?>