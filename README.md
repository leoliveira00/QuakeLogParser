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

Para executar no localhost basta criar a base de dados. Para tanto, execute no MySQL o script em sql/quakelogparser_basic.sql. 

Depois configure as variáveis de conexão em conexao.class.php:
var $usr = "root";//colocar usuário do bd
var $pss = "";//colocar a senha do bd aqui
var $host = "localhost";//colocar o host do bd aqui
var $bd = "quakelogparser";//colocar o nome do bd aqui

Aumente o tempo de execução no servidor para evitar o "Maximum execution time exceeded". No php.ini procure por "max_execution_time" e substitua o valor por 60. Com isso você estará mudando o tempo máximo de execução da requisição para um minuto para que dê tempo de subir o arquivo, ler e gravar e depois deletar o arquivo.

Execute o localhost: http://localhost:{sua_porta}/QuakeLogParser-master/

Ao executar você verá a seguinte mensagem: "Nenhum registro encontrado".

Por último, é só subir o log que está em htdocs\QuakeLogParser-master\arq\games.log. Será exibido um ranking dos jogadores e disponibilizado a impressão do relatório de kills.
