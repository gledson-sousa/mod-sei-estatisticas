# Módulo Estatísticas do SEI

## Instalação
Faça o download desse projeto no seguinte diretório do SEI
```
cd sei/web/modulos
git clone https://github.com/spbgovbr/mod-sei-estatisticas.git
```

Para que o SEI reconheça esse módulo é necessário editar o arquivo *sei/sei/config/ConfiguracaoSEI.php*.
Adicione a propriedade *Modulos* ao objeto *SEI*, caso nao exista, e como valor um array contendo o nome do módulo e o nome do diretório do módulo. **'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')**
```
...
  'SEI' => array(
      ...
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),
...
  ```
Ainda editando o arquivo *sei/sei/config/ConfiguracaoSEI.php* adicione uma nova chave com as configurações do módulo.
Os campos url, sigla e chave devem ser preenchidos com os valores enviados pela equipe do Pen.
```
...
  'SEI' => array(
      'URL' => getenv('SEI_HOST_URL').'/sei',
      'Producao' => false,
      'RepositorioArquivos' => '/var/sei/arquivos',
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),
...
  'MdEstatisticas' => array(
      'url' => 'https://estatistica.processoeletronico.gov.br',
      'sigla' => 'MPOG',
      'chave' => '123456'),


...
  ```

Em seguida basta criar um agendamento definindo-se a periodicidade do envio. O agendamento deverá executar o seguinte comando:

 ```
MdEstatisticasAgendamentoRN::coletarIndicadores
 ```

IMPORTANTE:
- verificar se há rota aberta do servidor do SEI onde roda o agendamento para o servidor Webservice coletor
- a rota pode ser facilmente verificada usando, por exemplo, o comando:
```
curl https://estatistica.processoeletronico.gov.br
 ```
o resultado deverá ser algo como:
```
{"sistema":"WebService Estatísticas do SEI","versao":"1.0.0"}
 ```

## Suporte
Caso precise de ajuda, ou para solicitar a sua chave de conexão, favor abrir um chamado em nossa Central de Atendimento:
http://processoeletronico.gov.br/index.php/conteudo/suporte. A categoria do chamado é PEN - MODULO ESTATISTICAS - INSTALAÇÃO.



