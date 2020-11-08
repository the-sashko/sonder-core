<?php
/**
 * Main Class For Cron Controller
 */
class CronControllerCore extends ControllerCore
{
    /**
     * Method That Running All Cron Jobs
     */
    public function actionRun(): void
    {
        $token = $this->getValueFromUrl('token');

        if ($token !== $this->getConfig('cron')['token']) {
            throw new Exception('Invalid Token');
        }

        $logger = $this->getPlugin('logger');

        $cron     = $this->getModel('cron');
        $cronJobs = $cron->getJobs();

        foreach ($cronJobs as $cronJob) {
            $method = $cronJob->getAction();

            $cronJob->setTimeNextExec();

            try {
                $logMessage = sprintf('Job %s Start', $method);
                $logger->log($logMessage, 'cron');

                $this->$method();

                $cronJob->setLastExecStatus(true);
                $cronJob->setErrorMessage(null);

                $logMessage = sprintf('Job %s Done', $method);
                $logger->log($logMessage, 'cron');
            } catch (Exception $exp) {
                $logMessage = $exp->getMessage();

                $cronJob->setErrorMessage($logMessage);
                $cronJob->setLastExecStatus(false);

                $logger->logError($logMessage, false);

                $logMessage = sprintf('Job %s Fail', $logMessage);
                $logger->log($logMessage, 'cron');
            }

            $cron->updateByVO($cronJob);
        }
    }
}
