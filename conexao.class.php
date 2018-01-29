<?php
	class CONEXAO{

		/*
		* Dados da conexão
		*/
		var $usr = "root";//colocar o usuário do bd aqui
        var $pss = "";//colocar a senha do bd aqui
        var $host = "localhost";
        var $bd = "quakelogparser";
        var $query = "";
	 	var $link = "";
	 	var $retorno = ""; 	

	 	/*
	 	* Efetua a conexão no banco
	 	*/
	 	function CONEXAO(){
			$this->Connect();
		}

  		function Connect(){
  			try{
				$this->link = new PDO('mysql:host='.$this->host.';dbname='.$this->bd,$this->usr, $this->pss);

				$this->link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  			}
  			catch(PDOException $e){
  				$this->retorno = "Error line #21!: " . $e->getMessage() . "<br/>";
  			}
		}

		/*
		* Função para inserir os dados nas tabelas
		* @tabela - tabela no banco de dados
		* @campos - campos nos quais serão inseridos os valores
		* @valores - valores a serem inseridos nos campos
		*/
		function Insert($tabela,$campos,$valores){
			try{
				$pdo = $this->link;
				$sql = 'INSERT INTO '.$tabela.'('.$campos.') VALUES('.$valores.')';
				$stmt = $pdo->prepare($sql);
  				$stmt->execute();
        		$this->retorno = $pdo->lastInsertId(); 
			}
			catch(PDOException $e){
				$this->retorno = "Error line #34!: " . $e->getMessage() . "<br/>";
			}
		}

		/*
		* Função para buscar os dados do ranking
		* @nomeBusca - o nome do player para efetuar a busca
		*/
		function GetDadosGrid($nomeBusca=null){
			$sql = "SELECT players.Nick_Name
					      ,SUM(players.Player_Kills) AS Ranking
					  FROM players ";

			if($nomeBusca!=""){
				$sql .= "WHERE players.Nick_Name LIKE '%".$nomeBusca."%' ";
			}

			$sql .= "GROUP BY players.Nick_Name
					ORDER BY Ranking DESC, players.Nick_Name";

			$query = $this->link->query($sql);
			$this->retorno = $query->fetchAll(PDO::FETCH_ASSOC);
		}

	}
?>