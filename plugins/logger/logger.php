<?php
    class LoggerPlugin
    {
        public function log(
            string $message = '',
            string $logType = 'default'
        ) : bool
        {
            if (strlen($message) < 3) {
                return false;
            }

            if (strlen($logType) < 3) {
                return false;
            }

            $dateString = date('[Y-m-d H:i:s]');
            $message = "{$dateString} {$message}\n";
            $logFileName = $this->_getLogFileName($logType);

            return $this->_writeToLogFile($message, $logFileName);
        }

        public function logError(
            string $message = '',
            bool   $isThrowException = false
        ) : bool
        {
            if (strlen($message) < 3) {
                return false;
            }

            $res = $this->log($message, 'error');

            if (!$isThrowException) {
                return $res;
            }

            throw new Exception($message);            
        }

        public function _writeToLogFile(
            string $message = '',
            string $logFileName = ''
        ) : bool
        {
            if (strlen($message) < 3) {
                return false;
            }

            $logFile = fopen($logFileName, 'a');

            if (!$logFile) {
                return false;
            }

            fwrite($logFile, $message);

            fclose($logFile);

            return true;
        }

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
                return true;
            }

            if (!is_file($oldLogFileName)) {
                return true;
            }

            unlink($oldLogFileName);

            return true;
        }

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

        private function _getLogDir(string $logType = 'default') : string
        {
            return __DIR__.'/../../../res/logs/'.$logType;
        }
    }
?>