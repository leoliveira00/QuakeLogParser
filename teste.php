<?php
	$arrDados = array();
	$arquivo = fopen("arq/games - Copia.log", "r");

	
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
	* Pega as posições iniciais e finais de cada jogo
	*/
	for($i=0; $i<count($arrDados); $i++){
		if(strripos("[".$arrDados[$i]."]", "InitGame")){
			$arrPosIni[] = $i;
		}
	}

	echo "<pre>arrPosIni: ";
	print_r($arrPosIni);
	echo "</pre>";

	for($i=1; $i<=count($arrPosIni); $i++){

		if($i == count($arrPosIni)){
			$dif = count($arrDados);
		}
		else{
			$dif = $arrPosIni[$i] - $arrPosIni[$i-1]; 
		}		

		$arrPosFim[] = $dif;
	}


	echo "<pre>arrPosFim: ";
	print_r($arrPosFim);
	echo "</pre>";

	/*
	* Recupera dados das partidas com base nas posições iniciais e finais de cada jogo
	*/
	for($j=0; $j < count($arrPosIni); $j++){
		$partida = array_slice($arrDados, $arrPosIni[$j], $arrPosFim[$j]);		

		/*
		* Pega os Players
		*/
		foreach ($partida as $linha) {
			$playerName[] = "";
			if(strripos("[".$linha."]", "ClientUserinfoChanged")){				
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
		* Converte players em matriz (palyerName, totKills)
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
						//se matou, não pontua
						continue;	
					}
					elseif(strpos($linha, " killed ".$matrizPlayers[$i][0]." by") && !strpos($linha, $matrizPlayers[$i][0]." killed ".$matrizPlayers[$i][0])){		

						if(strpos($linha, "killed") < strpos($linha, $matrizPlayers[$i][0])){
							if(strpos($linha, "world")>0){
								//morto por <world>, perde kill
								$matrizPlayers[$i][1]--;
							}							
						}
					}
					elseif (strpos($linha, $matrizPlayers[$i][0]." killed")) {
						//matou, ganha kill
						$matrizPlayers[$i][1]++;
					}
				}

				$totalKills++;
			}

		}

		/*echo "<pre>";
		print_r($matrizPlayers);
		echo "</pre>";*/


		/*
		* Grava as partidas
		*/
		//$sql->Insert("game","Game_Name, Tot_Kills","'"."game_".($j+1)."','".$totalKills."'");
		//$gameId = $sql->retorno;


		/*
		* Grava os players
		*/
		for($i=0; $i<count($matrizPlayers); $i++){
			//$sql->Insert("player","Nick_Name, Game_Id, Player_Kills","'".$matrizPlayers[$i][0]."','".$gameId."','".$matrizPlayers[$i][1]."'");								
			//$playerId = $sql->retorno;

			/*
			* Grava os Kills
			*/
			$matrizKills[]="";
			foreach ($partida as $linha) {
				if(strpos($linha, " Kill: ")){
					
					for($i=0; $i<count($matrizPlayers); $i++){
						if(strpos($linha, " killed ".$matrizPlayers[$i][0])){
							//foi morto
							$matrizKills[] = array($matrizPlayers[$i][0], substr($linha, strpos($linha, " MOD_")));
						}
					}
				}
			}							
		}


		$matrizKills = array_values(array_filter($matrizKills));
		
		/*echo "<strong>Game: ".$gameId." - kills:</strong><pre>";
		print_r($matrizKills);
		echo "</pre>";*/

		/*
		* Grava as mortes
		*/
		for($i=0; $i<count($matrizKills); $i++){
			//$sql->Insert("kills","Game_Id, Nick_Name, Causa_Mortis","'".$gameId."','".$matrizKills[$i][0]."','".$matrizKills[$i][1]."'");
		}
	}

	
?>