<?php
/**
 * Main Class For Cron Controller
 */
class CronControllerCore extends ControllerCore
{
    /**
     * Method That Running All Cron Jobs
     */
    public function actionRun() : void
    {
        if ($this->URLParam != $this->initConfig('cron')['token']) {
            throw new Exception('Invalid Token');
        }

        $logger = $this->initPlugin('logger');

        $cron = $this->initModel('cron');

        $cronJobs = $cron->getJobs();

        foreach ($cronJobs as $cronJob) {
            $method = $cronJob->getAction();
            $cronJob->setTimeNextExec();

            try {
                $logger->log('Job '.$method.' Start', 'cron');
                $this->$method();
                $cronJob->setLastExecStatus(TRUE);
                $cronJob->setErrorMessage('');
                $logger->log('Job '.$method.' Done', 'cron');
            } catch (Exception $exp) {
                $message = $exp->getMessage();
                $cronJob->setErrorMessage($message);
                $cronJob->setLastExecStatus(FALSE);
                $logger->logError($message, FALSE);
                $logger->log('Job '.$method.' Fail', 'cron');
            }

            $cron->updateByVO($cronJob);
        }
    }
}
?>