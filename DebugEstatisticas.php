<?

require_once '../../SEI.php';

class DebugEstatisticas
{
    private $objInfraDebug;
    private $strDebugTag;
    private $numTempoUltimoLog;
    private static $instance = array();

    public static function getInstance($parStrDebugTag=null)
    {
        if (!array_key_exists($parStrDebugTag, self::$instance)) {
            self::$instance[$parStrDebugTag] = new DebugEstatisticas($parStrDebugTag);
        }

        return self::$instance[$parStrDebugTag];
    }

    public function __construct($parStrDebugTag)
    {
        $this->objInfraDebug = InfraDebug::getInstance();
        $this->strDebugTag = $parStrDebugTag;
    }

    public function gravar($str, $numIdentacao=0, $bolLogTempoProcessamento=true, $bolLogDataHora=true)
    {
        $strDataLog = ($bolLogDataHora) ? "[".date("d/m/Y H:i:s")."]" : "";
        $strTag = (!is_null($this->strDebugTag)) ? "[" . $this->strDebugTag . "]" : "";
        $strProcId = (!is_null($this->strDebugTag)) ? "(" . getmypid() . ")" : "";
        $strLog = sprintf("%s %s %s %s %s", $strDataLog, $strProcId, $strTag, str_repeat(" ", $numIdentacao * 4), $str);

        //Registro de tempo de processamento desde �ltimo log
        if($bolLogTempoProcessamento){
            $numTempoFinal = microtime(true);
            if(is_null($this->numTempoUltimoLog)){
                $this->numTempoUltimoLog = $numTempoFinal;
            } else {
                $numTempoProcessamento = round($numTempoFinal - $this->numTempoUltimoLog, 2);
                $strLog .= " [+{$numTempoProcessamento}s]";
                $this->numTempoUltimoLog = $numTempoFinal;
            }
        }

        $this->objInfraDebug->gravar(utf8_encode($strLog));
    }
}
