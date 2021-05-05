<?php
/**
 * Main Class For Cron Controller
 */
class CronControllerCore extends ControllerCore
{
    /**
     * Method That Running All Cron Jobs
     */
    public function displayRun(): bool
    {
        $token      = $this->getValueFromUrl('token');
        $cronConfig = $this->getConfig('cron');

        if (empty($cronConfig) || !array_key_exists('token', $cronConfig)) {
            throw new \Exception('Cron Config Has Bad Format');
        }

        if (empty($token) || $token !== $cronConfig['token']) {
            throw new \Exception('Invalid Token');
        }

        $jobs = (new Cron)->getJobs();

        if (empty($jobs)) {
            return false;
        }

        foreach ($jobs as $job) {
            $this->_runJob($job);
        }

        return true;
    }

    /**
     * Method That Running Cron Job
     *
     * @param CronValuesObject $job Cron Values Object
     */
    private function _runJob(CronValuesObject $job): void
    {
        $logger = $this->getPlugin('logger');

        $jobName = $job->getAction();

        $method = mb_convert_case($jobName, MB_CASE_TITLE);
        $method = sprintf('job%s', $method);

        $job->setTimeNextExec();

        try {
            $logMessage = sprintf('Job %s Start', $jobName);
            $logger->log($logMessage, 'cron');

            set_error_handler([$this, 'errorHandler']);

            $this->$method();

            restore_error_handler();

            $job->setLastExecStatus(true);
            $job->setErrorMessage(null);

            $logMessage = sprintf('Job %s Done', $jobName);
            $logger->log($logMessage, 'cron');
        } catch (\Throwable $exp) {
            $logMessage = $exp->getMessage();

            $job->setErrorMessage($logMessage);
            $job->setLastExecStatus(false);

            $logger->logError($logMessage, 'cron');

            $logMessage = sprintf(
                'Job %s Failed. Error: %s',
                $jobName,
                $logMessage
            );

            $logger->log($logMessage, 'cron');
        }

        (new Cron)->updateByVO($job);
    }

    /**
     * Errors Handler
     *
     * @param int    $errorCode    HTTP Response Code
     * @param string $errorMessage Error Message
     * @param string $errorFile    File With Error
     * @param int    $errorLine    Line In File With Error
     */
    public function errorHandler(
        int    $errorCode,
        string $errorMessage,
        string $errorFile,
        int    $errorLine
    ): void
    {
        $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        foreach ($debugBacktrace as $key => $debugBacktraceStep) {
            $debugBacktrace[$key] = '…';

            if (array_key_exists('file', $debugBacktraceStep)) {
                $debugBacktrace[$key] = $debugBacktraceStep['file'];
            }

            if (array_key_exists('line', $debugBacktraceStep)) {
                $debugBacktrace[$key] = sprintf(
                    '%s (%d)',
                    $debugBacktrace[$key],
                    $debugBacktraceStep['line']
                );
            }
        }

        $logMessage = sprintf(
            'Error #%d: %s. File: %s (%d). Trace: %s',
            $errorCode,
            $errorMessage,
            $errorFile,
            $errorLine,
            implode(' -> ', array_reverse($debugBacktrace))
        );

        (new LoggerPlugin)->logError($logMessage);

        throw new \Exception($errorMessage, $errorCode);
    }
}
