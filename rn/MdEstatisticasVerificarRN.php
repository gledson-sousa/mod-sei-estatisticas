<?php

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Classe respons�vel pela verifica��o da correta��o instala��o e configura��o do m�dulo no sistema
 */
class MdEstatisticasVerificarRN extends InfraRN
{

    public function __construct() {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance();
    }

    /**
     * Verifica se o m�dulo foi devidamente ativado nas configura��es do sistema
     *
     * @return bool
     */
    public function verificarAtivacaoModulo()
    {
        global $SEI_MODULOS;

        if(!array_key_exists("MdEstatisticas", $SEI_MODULOS)){
            throw new InfraException("Chave de ativa��o do m�dulo mod-sei-estatisticas (MdEstatisticas) n�o definido nas configura��es de m�dulos do SEI");
        }

        if(is_null($SEI_MODULOS['MdEstatisticas'])){
            $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();

            if (!$objConfiguracaoSEI->isSetValor('SEI','Modulos')){
                throw new InfraException("Chave de configura��o de M�dulos n�o definida nas configura��es do sistema. (ConfiguracaoSEI.php | SEI > Modulos)");
            }

            $arrModulos = $objConfiguracaoSEI->getValor('SEI','Modulos');
            $strDiretorioModEstatisticas = basename($arrModulos['MdEstatisticas']);
            $strDiretorioModulos = dirname ($arrModulos['MdEstatisticas']);
            throw new InfraException("Diret�rio do m�dulo ($strDiretorioModEstatisticas) n�o pode ser localizado em $strDiretorioModulos");
        }

        return true;
    }


    /**
    * Verifica a correta defini��o de todos os par�metros de configura��o do m�dulo
    *
    * @return bool
    */
    public function verificarArquivoConfiguracao()
    {

        // Valida se todos os par�metros de configura��o est�o presentes no arquivo de configura��o
        $arrStrChavesConfiguracao = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
        if(!array_key_exists("MdEstatisticas", $arrStrChavesConfiguracao)){
            $strMensagem = "Grupo de parametriza��o MdEstatisticas nao pode ser localizado no arquivo de configura��o do SEI";
            $strDetalhes = "Verifique se o arquivo de configura��o encontra-se �ntegro.";
            throw new InfraException($strMensagem, null, $strDetalhes);
        }

        // Valida se todas as chaves de configura��o obrigat�rias foram atribu�das
        $arrStrChavesConfiguracao = $arrStrChavesConfiguracao["MdEstatisticas"];
        $arrStrParametrosExperados = array("url", "sigla", "chave");
        foreach ($arrStrParametrosExperados as $strChaveConfiguracao) {
            if(!array_key_exists($strChaveConfiguracao, $arrStrChavesConfiguracao)){
                $strMensagem = "Par�metro 'MdEstatisticas > $strChaveConfiguracao' n�o pode ser localizado no arquivo de configura��o";
                $strDetalhes = "Verifique se o arquivo de configura��o  encontra-se �ntegro.";
                throw new InfraException($strMensagem, null, $strDetalhes);
            }
        }

        return true;
    }

    /**
    * Verifica a conex�o com o WebService Rest, utilizando o endere�o e certificados informados
    *
    * @return bool
    */
    public function verificarConexao()
    {
        $objConfiguracaoSEI = ConfiguracaoSEI::getInstance();
        $url = $objConfiguracaoSEI->getValor('MdEstatisticas', 'url');        
        $urlApi = $url . '/api/estatisticas';
        $urllogin = $url . '/login';
        $orgaoSigla = $objConfiguracaoSEI->getValor('MdEstatisticas', 'sigla', false, '');
        $orgaoSenha = $objConfiguracaoSEI->getValor('MdEstatisticas', 'chave', false, '');
        $header = array('Content-Type: application/json');
        
        $json = array(
            username => $orgaoSigla,
            password => $orgaoSenha
        );
        $data = json_encode($json);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urllogin);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($info['http_code'] == 200) {
            $output = explode("\r\n", $output);
            foreach ($output as $value) {
                if (strpos(strtoupper($value), 'AUTHORIZATION') !== false) {
                    $this->header[] = $value;
                    return true;
                }
            }
        }
        
        //se chegou ate aqui deu problema
        throw new InfraException("Falha ao autenticar http code " . $info['http_code'] . ". Caso o http code seja 200 verifique se o token Authorization est� presente " . print_r($output, false));
    }


    /**
    * Verifica a conex�o com o Barramento de Servi�os do PEN, utilizando o endere�o e certificados informados
    *
    * @return bool
    */
    public function verificarAcessoPendenciasTramitePEN()
    {
        // Processa uma chamada ao Barramento de Servi�os para certificar que o atual certificado est� corretamente vinculado � um
        // comit� de protocolo v�lido
        $objProcessoEletronicoRN = new ProcessoEletronicoRN();
        $objProcessoEletronicoRN->listarPendencias(false);
        return true;
    }

    /**
    * Verifica se Gearman foi corretamente configurado e se o mesmo se encontra ativo
    *
    * @return bool
    */
    public function verificarConfiguracaoGearman()
    {
        $objConfiguracaoModPEN = ConfiguracaoModPEN::getInstance();
        $arrObjGearman = $objConfiguracaoModPEN->getValor("PEN", "Gearman", false);
        $strGearmanServidor = trim(@$arrObjGearman["Servidor"] ?: null);
        $strGearmanPorta = trim(@$arrObjGearman["Porta"] ?: null);

        if(empty($strGearmanServidor)) {
            // N�o processa a verifica��o da instala��o do Gearman caso n�o esteja configurado
            return false;
        }

        if(!class_exists("GearmanClient")){
            throw new InfraException("N�o foi poss�vel localizar as bibliotecas do PHP para conex�o ao GEARMAN./n" .
                "Verifique os procedimentos de instala��o do mod-sei-pen para maiores detalhes");
        }

        try{
            $objGearmanClient = new GearmanClient();
            $objGearmanClient->addServer($strGearmanServidor, $strGearmanPorta);
            $objGearmanClient->ping("health");
        } catch (\Exception $e) {
            $strMensagemErro = "N�o foi poss�vel conectar ao servidor Gearman (%s, %s). Erro: %s";
            $strMensagem = "N�o foi poss�vel conectar ao servidor Gearman ($this->strGearmanServidor, $this->strGearmanPorta). Erro:" . $objGearmanClient->error();
            $strMensagem = sprintf($strMensagemErro, $this->strGearmanServidor, $this->strGearmanPorta, $objGearmanClient->error());
            throw new InfraException($strMensagem);
        }

        return true;
    }

    private function verificarExistenciaArquivo($parStrLocalizacaoArquivo)
    {
        if(!file_exists($parStrLocalizacaoArquivo)){
            $strNomeArquivo = basename($parStrLocalizacaoArquivo);
            $strDiretorioArquivo = dirname($parStrLocalizacaoArquivo);
            throw new InfraException("Arquivo do $strNomeArquivo n�o pode ser localizado em $strDiretorioArquivo");
        }
    }
}
