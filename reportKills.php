<?php

    	/*require_once "reportKills.class.php";
    	$report = new reportKills("css/estilo_rel.css", "Relatório de Kills");  
	$report->BuildPDF();
	$report->Exibir('Rel_Kills.pdf');*/
	
	include("conexao.class.php");
	require_once __DIR__ . '/vendor/autoload.php';
	
	class reportKills{  
 
        private $pdo  = null;  
        private $pdf  = null;
        private $css  = null;  
        private $titulo = null;
     
        /*  
        * Construtor  
        * @param $css - Arquivo CSS  
        * @param $titulo - Título do relatório   
        */  
        public function __construct($css, $titulo) {  
            $this->pdo = new CONEXAO();
            $this->titulo = $titulo;  
            $this->setarCSS($css);  
        }
      
        /*  
        * Seta o conteúdo do arquivo CSS para o atributo css  
        * @param $file - Arquivo CSS
        */  
        public function setarCSS($file){  
            if (file_exists($file)){
                $this->css = file_get_contents($file);
            }
        }
     
        /*  
        * Monta o cabeçalho
        */  
        protected function getHeader(){ 
            date_default_timezone_set("Brazil/East"); 
            $data = date('j/m/Y H:i:s');  
            $retorno = "
                <table class='tbl_header' width='1000'>  
                   <tr>  
                     <td align='left'>Quake Log Parser</td>  
                     <td align='right'>Gerado em: $data</td>  
                   </tr>  
                </table>";  
            return $retorno;  
        }  
     
        /*  
        * Monta o rodapé  
        */  
        protected function getFooter(){  
            $retorno = "
                <table class='tbl_footer' width='1000'>  
                    <tr>  
                     <td align='left'><a href='http://arteonze.com.br/' target='_blank'>arteonze.com.br/</a></td>  
                   </tr>  
                </table>";  
            return $retorno;  
        }  
     
        /*   
        * Monta a tabela com os dados  
        */  
        public function getTabela(){  
            $color  = false;  
            
			$retorno .= $this->getHeader();

            $sqlGames = "SELECT G.Game_Id
                               ,G.Game_Name
                               ,G.Tot_Kills
                           FROM GAMES G
                         ORDER BY G.Game_Id";
            $queryGames = $this->pdo->link->query($sqlGames);            
            $dadosGames = $queryGames->fetchAll(PDO::FETCH_ASSOC);

            if(count($dadosGames)>0){
     
                $retorno .= "<h2 style='text-align:center'>".$this->titulo."</h2>";                 
                $retorno .= "<table style='width: 100%'>";
                
                for($i=0; $i<count($dadosGames); $i++){

                    $retorno .= "<tr>";
                    $retorno .= "    <td colspan='2' class='rowGame'>".$dadosGames[$i]['Game_Name']." - Total Kills: ".$dadosGames[$i]['Tot_Kills']."</td>";
                    $retorno .= "</tr>";

                    $retorno .= "<tr>";
                    $retorno .= "    <td style width'400px'>&nbsp;</td>";
                    $retorno .= "    <td align='right'>";
                    $retorno .= "        <table style='width: 80%' border='0' class='tbl_itens'>";


                    $sqlKills = "SELECT RPAD(K.Causa_Mortis, (145-CHAR_LENGTH(k.Causa_Mortis)), '.') AS Causa_Mortis
                                       ,COUNT(K.Causa_Mortis) AS kills
                                   FROM GAMES G
                                       ,KILLS K
                                  WHERE K.Game_id = G.Game_Id
                                    AND G.Game_Id = ".$dadosGames[$i]['Game_Id']."
                                 GROUP BY K.Causa_Mortis;";
                    $queryKills = $this->pdo->link->query($sqlKills);            
                    $dadosKills = $queryKills->fetchAll(PDO::FETCH_ASSOC);


                    for($j=0; $j<count($dadosKills); $j++){

                        $retorno .= "       <tr>";
                        $retorno .= "           <td align='left'>".$dadosKills[$j]['Causa_Mortis']."</td>";
                        $retorno .= "           <td align='right'>".$dadosKills[$j]['kills']."</td>";
                        $retorno .= "       </tr>";

                    }

                    $retorno .= "        </table>";
                    $retorno .= "    </td>";
                    $retorno .= "</tr>";
                }

                $retorno .= "</table>";

            }
			
			$retorno .= $this->getFooter();
			
            return $retorno;  
        }
    }
	
	$reportKills = new reportKills("css/estilo_rel.css", "Relatório de Kills");

	$html = $reportKills->getTabela();

	$mpdf = new \Mpdf\Mpdf();
	$mpdf->WriteHTML($html);
	$mpdf->Output();
 
?>
