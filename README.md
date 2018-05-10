# M�dulo Estat�sticas do SEI

## Instala��o
Fa�a o download desse projeto no seguinte diret�rio do SEI
```
cd sei/web/modulos
git clone http://softwarepublico.gov.br/gitlab/mp/mod-sei-estatisticas.git
```

Para que o SEI reconhe�a esse m�dulo � necess�rio editar o arquivo *sei/sei/config/ConfiguracaoSEI.php*.
Adicione a propriedade *Modulos* ao objeto *SEI*, caso nao exista, e como valor um array contendo o nome do m�dulo e o nome do diret�rio do m�dulo. **'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')**
```
...
  'SEI' => array(
      ...
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),
...
  ```
Ainda editando o arquivo *sei/sei/config/ConfiguracaoSEI.php* adicione uma nova chave com as configura��es do m�dulo
```
...
  'SEI' => array(
      'URL' => getenv('SEI_HOST_URL').'/sei',
      'Producao' => false,
      'RepositorioArquivos' => '/var/sei/arquivos',
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),
...
  'MdEstatisticas' => array(
      'url' => 'http://estatisticas.planejamento.gov.br/estatisticas'),
...
  ```

## Como contribuir

### 1. Com Vagrant

Para o desenvolvimento � necess�rio ter instalado

- [Vagrant](https://www.vagrantup.com/)
- [VirtualBox](https://www.virtualbox.org/)

Na raiz do projeto SEI, crie o arquivo *Vagrantfile* com o seguinte conte�do
```
Vagrant.configure("2") do |config|
    config.vm.box = "processoeletronico/sei-3.0.0"
end
```
Siga as instru��es de instala��o do m�dulo

Inicie o SEI com o comando.
 ```
sudo vagrant up
 ```
� necess�rio executar como administrador (root) porque a box est� configurado para iniciar na porta 80.
Ser� feito o download da box e no final o projeto poder� ser acessivel no endere�o.
 ```
http://localhost/sei
 ```

### 2. Com docker

� necess�rio ter instalado
- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

Siga as orienta��es para instalar o m�dulo no SEI, acesse o diret�rio do m�dulo e execute
```
docker-compose up -d
```
Ser� feito download dos containers e no final o SEI estar� acessivel em
 ```
http://localhost/sei
 ```

