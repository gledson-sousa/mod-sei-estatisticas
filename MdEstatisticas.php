<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 29/04/2016 - criado por mga@trf4.jus.br
 *
 */

 /*
 No SIP criar os recursos md_abc_processo_processar, md_abc_documento_processar e md_abc_andamento_lancar e adicionar em um novo perfil chamado MD_ABC_B�sico.
  */

class MdEstatisticas extends SeiIntegracao{

  public function __construct(){
  }

  public function getNome(){
    return 'M�dulo de Estatisticas do SEI';
  }

  public function getVersao() {
    return '1.0.0';
  }

  public function getInstituicao(){
    return 'MPDG - Minist�rio do Planejamento, Desenvolvimento e Gest�o';
  }

  public function inicializar($strVersaoSEI){
    /*
    if (substr($strVersaoSEI, 0, 2) != '3.'){
      die('M�dulo "'.$this->getNome().'" ('.$this->getVersao().') n�o � compat�vel com esta vers�o do SEI ('.$strVersaoSEI.').');
    }
     */
  }
}
?>
