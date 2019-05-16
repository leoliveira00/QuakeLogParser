# QuakeLogParser

Este repositório foi criado com o objetivo de resolver questão proposta para avaliação de desempenho profissional. 


O problema proposto consiste em implementar um parser para o arquivo de log do jogo Quake 3 Arena. O parser deve:

* Ler o arquivo e agrupar os dados;
* Gravar os dados agrupados em um banco de dados relacional;
* Implemenar um ranking com os dados gravados conforme modelo proposto;
* Gerar relatório conforme modelo proposto.


Recursos Utilizados:

* A linguagem de programação escolhida foi o PHP (https://secure.php.net/) e o banco de dados, o MySQL (https://www.mysql.com);
* Para testes locais foi utilizado WAMP versão 3.1.0 (http://www.wampserver.com/);
* Foi utilizado também a biblioteca JQuery na versão 3.3.1 (https://jquery.com) e o plugin jquery-confirm (https://craftpip.github.io/jquery-confirm/);
* Utilizei ainda compilador SASS gentilmente cedido aqui: https://github.com/ManualDeveloper/Compiler-SASS-To-PHP. 
* A biblioteca MPDF versão 6.0 foi usada para geração do relatório (https://mpdf.github.io).


Todos os arquivos, inclusive bibliotecas, serão anexados ao projeto. O arquivo para a criação do banco está na pasta "sql".


Para ver a versão final em funcionamento acesse: http://arteonze.com.br/quakelogparser/

<h2>INSTRUÇÕES</h2>

<ol>
<li>Instale o composer:&nbsp;<a href="https://getcomposer.org/" rel="nofollow">https://getcomposer.org/</a></li>
<li>A pasta mpdf60 está obsoleta, ignore ou delete. Instale então o mpdf para o relatório:
<ul>
<li>Na pasta raiz do projeto execute: $ composer require mpdf/mpdf</li>
</ul>
</li>
<li>Enquanto o mpdf &eacute; instalado, crie a base de dados executando no MySQL o script que est&aacute; em sql/quakelogparser_basic.sql.</li>
<li>Configure as vari&aacute;veis de conex&atilde;o em conexao.class.php:
<ul>
<li>var $usr = "root";//colocar usu&aacute;rio do bd</li>
<li>var $pss = "";//colocar a senha do bd aqui</li>
<li>var $host = "localhost";//colocar o host do bd aqui</li>
<li>var $bd = "quakelogparser";//colocar o nome do bd aqui</li>
</ul>
</li>
<li>Aumente o tempo de execu&ccedil;&atilde;o no servidor para evitar o "Maximum execution time exceeded":
<ul>
<li>No php.ini procure por "max_execution_time" e substitua o valor por 60. Com isso voc&ecirc; estar&aacute; mudando o tempo m&aacute;ximo de execu&ccedil;&atilde;o da requisi&ccedil;&atilde;o para um minuto para que d&ecirc; tempo de subir o arquivo, ler e gravar e depois deletar o arquivo.</li>
</ul>
</li>
<li>Execute o localhost: http://localhost:{sua_porta}/QuakeLogParser/. Ao executar voc&ecirc; ver&aacute; a seguinte mensagem: "Nenhum registro encontrado".</li>
<li>Por &uacute;ltimo, &eacute; s&oacute; subir o log que est&aacute; em htdocs\QuakeLogParser\arq\games.log. Ser&aacute; exibido um ranking dos jogadores e disponibilizado a impress&atilde;o do relat&oacute;rio de kills.</li>
</ol>
