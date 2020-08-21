# M�dulo Estat�sticas do SEI

## Conex�o via Proxy

Caso o seu servidor use proxy para acessar a internet o php e o apache dever�o estar configurados para usarem esse proxy de prefer�ncia de forma transparente. 

H� casos em que essa configura��o ou n�o foi feita ou existe algum impedimento t�cnico para a realiza��o da mesma.

Nesse caso h� a possibilidade, a partir da vers�o 1.1.3, de indicar nos par�metros de configura��o um servidor proxy e a porta.

Segue um exemplo de como ficar� o array de configura��o do m�dulo com as chaves proxy e proxyPort:

```

'MdEstatisticas' => array(
      'url' => 'https://estatistica.processoeletronico.gov.br',
      'sigla' => 'MPOG',
      'chave' => '123456',
      'proxy' => 'meuproxy.gov.br',
      'proxyPort'=> '8080',
      'ignorar_arquivos' => array('sei/temp', 'sei/config/ConfiguracaoSEI.php', 'sei/config/ConfiguracaoSEI.exemplo.php', '.vagrant', '.git')),
      
``` 
**Importante:**
- Aten��o acima a vari�vel proxyPort (com o segundo P mai�sculo)
- A configura��o acima apenas dever� ser usada quando concluir-se que o seu apache/php n�o est� conseguindo acessar a internet usando a configura��o transparente de proxy do servidor

