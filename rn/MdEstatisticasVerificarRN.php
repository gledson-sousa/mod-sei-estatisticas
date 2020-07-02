<?php

require_once DIR_SEI_WEB . '/SEI.php';


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

        // Verifica se chave de config presente
        $arrPrincipal = $objConfiguracaoSEI->getValor('MdEstatisticas', 'ignorar_arquivos');
        
        // Valida se todos os par�metros de configura��o est�o presentes no arquivo de configura��o
        $arrStrChavesConfiguracao = ConfiguracaoModPEN::getInstance()->getArrConfiguracoes();
        if(!array_key_exists("PEN", $arrStrChavesConfiguracao)){
            $strMensagem = "Grupo de parametriza��o 'PEN' n�o pode ser localizado no arquivo de configura��o do m�dulo de integra��o do SEI com o Barramento PEN (mod-sei-pen)";
            $strDetalhes = "Verifique se o arquivo de configura��o localizado em $strArquivoConfiguracao encontra-se �ntegro.";
            throw new InfraException($strMensagem, null, $strDetalhes);
        }


        // Valida se todas as chaves de configura��o obrigat�rias foram atribu�das
        $arrStrChavesConfiguracao = $arrStrChavesConfiguracao["PEN"];
        $arrStrParametrosExperados = array("WebService", "LocalizacaoCertificado", "SenhaCertificado");
        foreach ($arrStrParametrosExperados as $strChaveConfiguracao) {
            if(!array_key_exists($strChaveConfiguracao, $arrStrChavesConfiguracao)){
                $strMensagem = "Par�metro 'PEN > $strChaveConfiguracao' n�o pode ser localizado no arquivo de configura��o do m�dulo de integra��o do SEI com o Barramento PEN (mod-sei-pen)";
                $strDetalhes = "Verifique se o arquivo de configura��o localizado em $strArquivoConfiguracao encontra-se �ntegro.";
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
        $objConfiguracaoModPEN = ConfiguracaoModPEN::getInstance();
        $strEnderecoWebService = $objConfiguracaoModPEN->getValor("PEN", "WebService");
        $strLocalizacaoCertificadoDigital = $objConfiguracaoModPEN->getValor("PEN", "LocalizacaoCertificado");
        $strSenhaCertificadoDigital = $objConfiguracaoModPEN->getValor("PEN", "SenhaCertificado");

        $strEnderecoWSDL = $strEnderecoWebService . '?wsdl';
        $curl = curl_init($strEnderecoWSDL);

        try{
            curl_setopt($curl, CURLOPT_URL, $strEnderecoWSDL);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSLCERT, $strLocalizacaoCertificadoDigital);
            curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $strSenhaCertificadoDigital);

            $strOutput = curl_exec($curl);

            $objXML = simplexml_load_string($strOutput);
            if(is_null($objXML)){
                throw new InfraException("Falha na valida��o do WSDL do webservice de integra��o com o Barramento de Servi�os do PEN localizado em $strEnderecoWSDL");
            }

        } finally{
            curl_close($curl);
        }

        return true;
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