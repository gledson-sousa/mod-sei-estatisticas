# M�dulo Estat�sticas do SEI

## Instala��o
Fa�a o download desse projeto no seguinte diret�rio do SEI
```
cd sei/web/modulos
git clone http://softwarepublico.gov.br/gitlab/mp/mod-sei-estatisticas.git
```

Edite o arquivo *sei/sei/config/ConfiguracaoSEI.php* e adicione o nome do projeto e seu diret�rio na propriedade *Modulos*.
```
...

  'SEI' => array(
      'URL' => 'http://localhost/sei',
      'Producao' => false,
      'RepositorioArquivos' => '/var/sei/arquivos',
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),

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

