<?php
/**
 * Plugin For Loggin Error And Ifomation Messages
 */
class LoggerPlugin
{
    /**
     * Default Name Of Log
     */
    const DEFAULT_LOG_NAME = 'app';

    /**
     * Default Name Of Error Log
     */
    const DEFAULT_ERROR_LOG_NAME = 'error';

    /**
     * Default Type Of Log
     */
    const DEFAULT_LOG_TYPE = 'common';

    /**
     * Error Type Of Log
     */
    const ERROR_LOG_TYPE = 'error';

    /**
     * Write Message To Log File
     *
     * @param string|null $message Log Message
     * @param string|null $logName Name Of Log
     * @param string|null $logType Type Of Log
     *
     * @return bool Is Entry Successfully Saved
     */
    public function log(
        ?string $message = null,
        ?string $logName = null,
        ?string $logType = null
    ): bool
    {
        if (empty($message)) {
            return false;
        }

        if (empty($logName)) {
            $logName = static::DEFAULT_LOG_NAME;
        }

        if (empty($logType)) {
            $logType = static::DEFAULT_LOG_TYPE;
        }

        $logName = $this->_normalizeString($logName);
        $logType = $this->_normalizeString($logType);

        $message     = sprintf('%s %s', date('[Y-m-d H:i:s]'), $message);
        $logFilePath = $this->_getLogFilePath($logName, $logType);

        return $this->_writeToLogFile($message, $logFilePath);
    }

    /**
     * Write Message To Error Log File
     *
     * @param string|null $message Log Message
     * @param string|null $logName Name Of Log
     *
     * @return bool Is Entry Successfully Saved
     */
    public function logError(
        ?string $message = null,
        ?string $logName = null
    ): bool
    {
        if (empty($message)) {
            return false;
        }

        if (empty($logName)) {
            $logName = static::DEFAULT_ERROR_LOG_NAME;
        }

        return $this->log($message, $logName, static::ERROR_LOG_TYPE);
    }

    /**
     * Write Message To Log File
     *
     * @param string|null $message     Log Message
     * @param string|null $logFilePath Type Of Log
     *
     * @return bool Is Entry Successfully Saved
     */
    private function _writeToLogFile(
        ?string $message     = null,
        ?string $logFilePath = null
    ): bool
    {
        if (empty($message)) {
            return false;
        }

        if (empty($logFilePath)) {
            return false;
        }

        $logFile = fopen($logFilePath, 'a');

        if (!$logFile) {
            return false;
        }

        $message = $message."\n";

        fwrite($logFile, $message);

        fclose($logFile);

        return true;
    }

    /**
     * Remove Old Log Files
     *
     * @param string|null $logDirPath Log File Directory Path
     * @param string|null $logName    Log File Path
     *
     * @return bool Is Old Log Files Successfully Removed
     */
    private function _rotateLogFiles(
        ?string $logDirPath = null,
        ?string $logName    = null
    ): bool
    {
        if (empty($logName)) {
            return false;
        }

        if (empty($logDirPath)) {
            return false;
        }

        $oldYear = strval(intval(date('Y')) - 1);

        $oldLogFilePath = sprintf(
            '%s/%s-%s-%s.log',
            $logDirPath,
            $logName,
            $oldYear,
            date('m-d')
        );

        if (!file_exists($oldLogFilePath) || !is_file($oldLogFilePath)) {
            return true;
        }

        unlink($oldLogFilePath);

        return true;
    }

    /**
     * Get Log File Path By Log Name And Type
     *
     * @param string|null $logName Name Of Log
     * @param string|null $logType Type Of Log
     *
     * @return string Log File Path
     */
    private function _getLogFilePath(
        ?string $logName = null,
        ?string $logType = null
    ): string
    {
        if (empty($logName)) {
            $logName = static::DEFAULT_LOG_NAME;
        }

        if (empty($logType)) {
            $logType = static::DEFAULT_LOG_TYPE;
        }

        $logDirPath = $this->_getLogDirPath($logType);

        if (!file_exists($logDirPath) || !is_dir($logDirPath)) {
            mkdir ($logDirPath, 0775, true);
        }

        $logFilePath = sprintf(
            '%s/%s-%s.log',
            $logDirPath,
            $logName,
            date('Y-m-d')
        );

        if (!file_exists($logFilePath) || !is_file($logFilePath)) {
            touch($logFilePath);
            chmod($logFilePath, 0775);
        }

        $this->_rotateLogFiles($logDirPath, $logType);

        return $logFilePath;
    }

    /**
     * Get Log File Directory Path By Log Type
     *
     * @param string|null $logType Type Of Log
     *
     * @return string Log File Directory Path
     */
    private function _getLogDirPath(?string $logType = null): string
    {
        if (empty($logType)) {
            $logType = static::DEFAULT_LOG_TYPE;
        }

        return sprintf('%s/../../../res/log/%s', __DIR__, $logType);
    }

    /**
     * Get Normalized String For Log Type Or Name
     *
     * @param string|null $string Log Type Or Name
     *
     * @return string|null Normalized Log Type Or Name
     */
    private function _normalizeString(?string $string = null): ?string
    {
        if (empty($string)) {
            return null;
        }

        $string = preg_replace('/([A-Z]+)/su', '_$1', $string);
        $string = preg_replace('/([^a-z_]+)/sui', '_', $string);
        $string = preg_replace('/([_]+)/su', '_', $string);
        $string = preg_replace('/((^_)|(_$))/su', '', $string);
        $string = mb_convert_case($string, MB_CASE_LOWER);

        return $string;
    }
}
