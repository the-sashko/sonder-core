<?php
class CronControllerCore extends ControllerCore
{
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
                $res = $logger->log('Job '.$method.' Start', 'cron');
                $this->$method();
                $cronJob->setLastExecStatus(true);
                $cronJob->setErrorMessage('');
                $logger->log('Job '.$method.' Done', 'cron');
            } catch (Exception $exp) {
                $message = $exp->getMessage();
                $cronJob->setErrorMessage($message);
                $cronJob->setLastExecStatus(false);
                $logger->logError($message, false);
                $logger->log('Job '.$method.' Fail', 'cron');
            }
            $cron->updateByVO($cronJob);
        }
    }
}
?>