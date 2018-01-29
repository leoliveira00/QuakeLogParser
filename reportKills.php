
<?php
    require_once "reportKills.class.php";
    

    /*
    * Instancia e exibe um novo relatório
    */
    $report = new reportKills("css/estilo_rel.css", "Relatório de Kills");  
	$report->BuildPDF();
	$report->Exibir('Rel_Kills.pdf');
?>
