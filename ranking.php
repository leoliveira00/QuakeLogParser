<h1>RANKING</h1>

<form name="formBusca" method="POST" class="right">
    <p>
    	<input type="text" placeholder="Busca por Nome" name="nomeBusca" /><input type="submit" value="Buscar">
    </p>
</form>

<table id="dados" summary="RANKING" >
  <tr>
    <th class="left"><span class="left">NAME</span></th>
    <th class="right"><div id="kills"><img id="poison" src="svg/poison.svg">&nbsp;KILLS</div></th>
  </tr>

	<?php

		$sql = new CONEXAO();

		/*
		* Requisita os dados do grid
		*/
		$nomeBusca="";
		if(isset($_POST['nomeBusca'])){
			$nomeBusca = $_POST['nomeBusca'];
		}
		$sql->GetDadosGrid($nomeBusca);

		/*
		* Exibe os dados requisitados
		*/		
		if(count($sql->retorno)>0){
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
		}
		else{
			echo "<script type=\"text/javascript\">";
			echo "$.alert({";
			echo "        title: '',";
			echo "        content: 'Nenhum registro encontrado.',";
			echo "		theme: 'black',";
			echo "		animation: 'zoom',";
			echo "        confirm: function(){},";
			echo "    });";
			echo "</script>";
		}
		

		/*
		* Limpa a conexão
		*/
		$sql = null;
	?>
</table>