<?php
/**
 * Plugin For Loggin Error And Ifomation Messages
 */
class LoggerPlugin
{
    /**
     * Write Message To Log File
     *
     * @param string $message Log Message
     * @param string $logType Type Of Log
     *
     * @return bool Is Entry Successfully Saved
     */
    public function log(
        string $message = '',
        string $logType = 'default'
    ) : bool
    {
        if (strlen($message) < 3) {
            return FALSE;
        }

        if (strlen($logType) < 3) {
            return FALSE;
        }

        $dateString  = date('[Y-m-d H:i:s]');
        $message     = "{$dateString} {$message}\n";
        $logFileName = $this->_getLogFileName($logType);

        return $this->_writeToLogFile($message, $logFileName);
    }

    /**
     * Write Message To Error Log File
     *
     * @param string $message          Log Message
     * @param bool   $isThrowException Thow Exception After Writing Message
     *
     * @return bool Is Entry Successfully Saved
     */
    public function logError(
        string $message          = '',
        bool   $isThrowException = FALSE
    ) : bool
    {
        if (strlen($message) < 3) {
            return FALSE;
        }

        $res = $this->log($message, 'error');

        if (!$isThrowException) {
            return $res;
        }

        throw new Exception($message);
    }

    /**
     * Write Message To Log File
     *
     * @param string $message Log Message
     * @param string $logType Type Of Log
     *
     * @return bool Is Entry Successfully Saved
     */
    private function _writeToLogFile(
        string $message     = '',
        string $logFileName = ''
    ) : bool
    {
        if (strlen($message) < 3) {
            return FALSE;
        }

        $logFile = fopen($logFileName, 'a');

        if (!$logFile) {
            return FALSE;
        }

        fwrite($logFile, $message);

        fclose($logFile);

        return TRUE;
    }

    /**
     * Remove Old Log Files
     *
     * @param string $logType Type Of Log Files
     *
     * @return bool Is Old Log Files Successfully Removed
     */
    private function _rotateLogFiles(string $logType = 'default') : bool
    {
        if (strlen($logType) < 3) {
            $logType = 'default';
        }

        $oldYear = strval(intval(date('Y'))-1);

        $logDir = $this->_getLogDir($logType);

        $oldLogFileName = $logDir.'/'.$logType.
                       '-'.$oldYear.'-'.date('m').'-'.date('d').'.log';

        if (!file_exists($oldLogFileName)) {
            return TRUE;
        }

        if (!is_file($oldLogFileName)) {
            return TRUE;
        }

        unlink($oldLogFileName);

        return TRUE;
    }

    /**
     * Get Log File Name By Log Type
     *
     * @param string $logType Type Of Log
     *
     * @return string Log File Name
     */
    private function _getLogFileName(string $logType = 'default') : string
    {
        if (strlen($logType) < 3) {
            $logType = 'default';
        }

        $logDir = $this->_getLogDir($logType);

        if (!file_exists($logDir)) {
            mkdir($logDir);
            chmod($logDir, 0775);
        }

        if (!is_dir($logDir)) {
            mkdir($logDir);
            chmod($logDir, 0775);
        }

        $logFileName = $logDir.'/'.$logType.
                       '-'.date('Y').'-'.date('m').'-'.date('d').'.log';

        if (!file_exists($logFileName)) {
            touch($logFileName);
            chmod($logFileName, 0775);
        }

        if (!is_file($logFileName)) {
            touch($logFileName);
            chmod($logFileName, 0775);
        }

        $this->_rotateLogFiles($logType);

        return $logFileName;
    }

    /**
     * Get Log File Directory Path By Log Type
     *
     * @param string $logType Type Of Log
     *
     * @return string Log File Directory Path
     */
    private function _getLogDir(string $logType = 'default') : string
    {
        return __DIR__.'/../../../res/logs/'.$logType;
    }
}
?>
