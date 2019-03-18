<?php
class CronControllerCore extends ControllerCore
{
    public function actionRun() : void
    {
        if ($this->URLParam != $this->initConfig('cron')['token']) {
            throw new Exception('Invalid Token');
        }

        $loger = $this->initLib('loger');

        $cron = $this->initModel('cron');

        $cronJobs = $cron->getJobs();

        foreach ($cronJobs as $cronJob) {
            $method = $cronJob->getAction();
            $cronJob->setTimeNextExec();
            try {
                $res = $loger->log('Job '.$method.' Start', 'cron');
                $this->$method();
                $cronJob->setLastExecStatus(true);
                $cronJob->setErrorMessage('');
                $loger->log('Job '.$method.' Done', 'cron');
            } catch (Exception $exp) {
                $message = $exp->getMessage();
                $cronJob->setErrorMessage($message);
                $cronJob->setLastExecStatus(false);
                $loger->logError($message, false);
                $loger->log('Job '.$method.' Fail', 'cron');
            }
            $cron->updateByVO($cronJob);
        }
    }
}
?>