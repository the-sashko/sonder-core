<?php

namespace Sonder\Plugins;

final class LoggerPlugin
{
    const DEFAULT_LOG_NAME = 'app';

    const DEFAULT_ERROR_LOG_NAME = 'error';

    const DEFAULT_LOG_TYPE = 'common';

    const ERROR_LOG_TYPE = 'error';

    const LOG_FILE_PATH_PATTERN = '%s/../../../logs/%s';

    /**
     * @param string|null $message
     * @param string|null $logName
     * @param string|null $logType
     *
     * @return bool
     */
    final public function log(
        ?string $message = null,
        ?string $logName = null,
        ?string $logType = null
    ): bool
    {
        if (empty($message)) {
            return false;
        }

        if (empty($logName)) {
            $logName = LoggerPlugin::DEFAULT_LOG_NAME;
        }

        if (empty($logType)) {
            $logType = $logName;
        }

        $logName = $this->_normalizeString($logName);
        $logType = $this->_normalizeString($logType);

        $message = sprintf(
            '%s %s',
            date('[Y-m-d H:i:s]'),
            $message
        );

        $logFilePath = $this->_getLogFilePath($logName, $logType);

        return $this->_writeToLogFile($message, $logFilePath);
    }

    /**
     * @param string|null $message
     * @param string|null $logName
     *
     * @return bool
     */
    final public function logError(
        ?string $message = null,
        ?string $logName = null
    ): bool
    {
        if (empty($message)) {
            return false;
        }

        if (empty($logName)) {
            $logName = LoggerPlugin::DEFAULT_ERROR_LOG_NAME;
        }

        return $this->log(
            $message,
            $logName,
            LoggerPlugin::ERROR_LOG_TYPE
        );
    }

    /**
     * @param string|null $message
     * @param string|null $logFilePath
     *
     * @return bool
     */
    private function _writeToLogFile(
        ?string $message = null,
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

        $message = $message . "\n";

        fwrite($logFile, $message);

        fclose($logFile);

        return true;
    }

    /**
     * @param string $logDirPath
     * @param string $logName
     */
    private function _rotateLogFiles(string $logDirPath, string $logName): void
    {
        $oldYear = strval(intval(date('Y')) - 1);

        $oldLogFilePath = sprintf(
            '%s/%s-%s-%s.log',
            $logDirPath,
            $logName,
            $oldYear,
            date('m-d')
        );

        if (file_exists($oldLogFilePath) && is_file($oldLogFilePath)) {
            unlink($oldLogFilePath);
        }
    }

    /**
     * @param string|null $logName
     * @param string|null $logType
     *
     * @return string
     */
    private function _getLogFilePath(
        ?string $logName = null,
        ?string $logType = null
    ): string
    {
        if (empty($logName)) {
            $logName = LoggerPlugin::DEFAULT_LOG_NAME;
        }

        if (empty($logType)) {
            $logType = LoggerPlugin::DEFAULT_LOG_TYPE;
        }

        $logDirPath = $this->_getLogDirPath($logType);

        if (!file_exists($logDirPath) || !is_dir($logDirPath)) {
            mkdir($logDirPath, 0775, true);
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
     * @param string|null $logType
     *
     * @return string
     */
    private function _getLogDirPath(?string $logType = null): string
    {
        if (empty($logType)) {
            $logType = LoggerPlugin::DEFAULT_LOG_TYPE;
        }

        $logFilePathPattern = self::LOG_FILE_PATH_PATTERN;

        if (defined('APP_PROTECTED_DIR_PATH')) {
            $logFilePathPattern = APP_PROTECTED_DIR_PATH . '/logs/%s';
        }

        return sprintf($logFilePathPattern, __DIR__, $logType);
    }

    /**
     * @param string|null $string
     *
     * @return string|null
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

        return mb_convert_case($string, MB_CASE_LOWER);
    }
}
