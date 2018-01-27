<?php
    require_once "reportKills.class.php"; 

    $report = new reportKills("css/estilo_rel.css", "Relatório de Kills");  
    $report->BuildPDF();  
    $report->Exibir("Relatório de Kills");  
?>
