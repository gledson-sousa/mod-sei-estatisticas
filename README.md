# M�dulo Estat�sticas do SEI

## Como contribuir

Para o desenvolvimento � necess�rio ter instalado

- [Vagrant](https://www.vagrantup.com/)
- [VirtualBox](https://www.virtualbox.org/)

Fa�a o download do projeto SEI e na raiz crie o arquivo *Vagrantfile* com o seguinte conte�do
```
Vagrant.configure("2") do |config|
config.vm.box = "processoeletronico/sei-3.0.0"
end
```
Fa�a o download desse projeto no seguinte diret�rio do SEI
```
cd sei/web/modulos
git clone http://softwarepublico.gov.br/gitlab/mp/mod-sei-estatisticas.git
```

Edite o arquivo *sei/sei/config/ConfiguracaoSEI.php* e adicione a propriedade *Modulos*, caso n�o exista, com o nome desse m�dulo.
```
...

  'SEI' => array(
      'URL' => 'http://localhost/sei',
      'Producao' => false,
      'RepositorioArquivos' => '/var/sei/arquivos',
      'Modulos' => array('MdEstatisticas' => 'mod-sei-estatisticas')),

...
  ```

Inicie o SEI com o comando

 ```
vagrant up
 ```


