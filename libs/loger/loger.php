<?php
    class LogerLib
    {
        public function log(
            string $message = '',
            string $type = 'default'
        ) : bool
        {
            if (strlen($message) < 3) {
                return false;
            }

            if (strlen($type) < 3) {
                return false;
            }

            $dateString = date('[Y-m-d H:i:s]');
            $message = "\n{$dateString} {$message}";
            $logFileName = $this->_getLogFileName($type);

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

        private function _rotateLogFiles(string $type = 'default') : bool
        {
            if (strlen($type) < 3) {
                $type = 'default';
            }

            $oldYear = strval(intval(date('Y'))-1);
            $logDir = $this->_getLogDir();
            $oldLogFileName = $logDir.'/'.$type.
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

        private function _getLogFileName(string $type = 'default') : string
        {
            if (strlen($type) < 3) {
                $type = 'default';
            }

            $logDir = $this->_getLogDir();

            $logFileName = $logDir.'/'.$type.
                           '-'.date('Y').'-'.date('m').'-'.date('d').'.log';

            if (!file_exists($logFileName)) {
                touch($logFileName);
                chmod($logFileName, 0755);
            }

            if (!is_file($logFileName)) {
                touch($logFileName);
                chmod($logFileName, 0755);
            }

            $this->_rotateLogFiles($type);

            return $logFileName;
        }

        private function _getLogDir() : string
        {
            return __DIR__.'/../../../res/logs';
        }
    }
?>