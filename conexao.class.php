<?php
	class CONEXAO{

		/*
		* Dados da conexão
		*/
		var $usr = "root";
        var $pss = "";
        var $host = "localhost";
        var $bd = "quakelogparser";
        var $query = "";
	 	var $link = "";
	 	var $retorno = "OK"; 	

	 	/*
	 	* Construtor efetua a conexão no banco
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
		*/
		function GetDadosGrid($nome=null){
			$sql = "SELECT player.Nick_Name
					      ,SUM(player.Player_Kills) AS Ranking
					  FROM player ";

			if($nome!=""){
				$sql .= "WHERE player.Nick_Name = '".$nome."' ";
			}

			$sql .= "GROUP BY player.Nick_Name
					ORDER BY Ranking DESC, player.Nick_Name";

					//echo $sql; die;

			$query = $this->link->query($sql);
			$this->retorno = $query->fetchAll(PDO::FETCH_ASSOC);
		}

	}
?>