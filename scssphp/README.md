![CSCore Logo](https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/PHP-logo.svg/1200px-PHP-logo.svg.png)

# Utilizando a biblioteca SCSS #
---
## Para a utilização da biblioteca, faça o Download da mesma:

- [SCSS TO PHP]
---
## Depois de fazer o Download da biblioteca, podemos ir diretamente para a codificação:
```sh
<?php

  //É necessário incluir a biblioteca na sua página
  require_once("scssphp/scss.inc.php");
  
  //Agora é necessário instanciar...
  use Leafo\ScssPhp\Compiler;
  $compilar = new Compiler();
  
  //Agora para fim de teste, iremos fazer diretamente no php o SASS...
  echo $compilar->compile('
    $padrao: #333;
    p {
	    color: lighten($padrao, 20%);
    }
  
  ');

?>
```
---
## Se aparece: p { color: #666; } , ocorreu tudo bem.
---
## Agora caso queira ler um arquivo, e torna-lo dinâmico...
### Arquivo: estilo.sass
```sh
p {
	color: lighten($padrao, 20%);
}

div {

	width: 50px;
	height: 50px;
	margin-left: 5px;
	margin-top: 5px;
	border: solid $padrao 1px;
	background-color: lighten($padrao, 50%);

}
```
### Arquivo: index.php
```sh
<?php

	require_once("scssphp-master/scss.inc.php");

	use Leafo\ScssPhp\Compiler;

	$compilar = new Compiler();

	$arquivo = file_get_contents("estilo.css");

	if( isset($_POST['corescolhida']) ){

		$corescolhida = $_POST['corescolhida'];

	}else{

		$corescolhida = '#abc';

	}

	$arquivo = str_replace('$padrao', $corescolhida, $arquivo);

	$estilo = $compilar->compile($arquivo);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Teste</title>
	<style>
		
		<?php echo $estilo; ?>

	</style>
</head>
<body>
	
	<p>Olá mundo!</p>

	<div></div>

	<form action="#" method="post">
		
		<input type="text" name="corescolhida">

		<button type="submit">Alterar</button>

	</form>

</body>
</html>
```
---
# Agora com essa base, você poderá utilizar da forma que bem desejar!

[SCSS TO PHP]: <https://github.com/ManualDeveloper/Compiler-SASS-To-PHP>
