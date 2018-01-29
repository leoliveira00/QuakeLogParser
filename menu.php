<script type="text/javascript">
    
    /*
    * Dispara o input file com o click do link
    */
    $(function(){
        $("#uploadLink").on('click', function(e){
            e.preventDefault();
            $("#select_arq:hidden").trigger('click');
        });
    });

    /*
    * Exibe div de carregamento
    */
    function carregar(){
        $('.divCarregando').fadeIn('fast');
        document.getElementById("frm_upld").submit();
    }

</script>

<nav id="menu">
    <ul>
        <li><a href="index.php">Ranking</a></li>
        <li><a href="#" id="uploadLink">Upload Arquivo</a></li>
        <li><a href="reportKills.php" target="_blank">Ralatório de Kills</a></li>
    </ul>
</nav>

<!--formulário "escondido"-->
<form id="frm_upld" action="uplArq.php" method="POST" enctype="multipart/form-data" onsubmit="return submitForm()">
    <input id="select_arq" type="file" name="fileUpload" onChange="carregar()" style="display: none">
</form>