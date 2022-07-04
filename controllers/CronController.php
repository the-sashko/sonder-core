<?php

namespace Sonder\Controllers;

use Sonder\Core\CoreController;
use Sonder\Exceptions\CoreException;
use Sonder\Exceptions\ModelException;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\ICronController;
use Sonder\Interfaces\IResponseObject;
use Sonder\Models\Cron\ValuesObjects\CronValuesObject;
use Sonder\Models\CronModel;
use Sonder\Plugins\Language\LanguageException;
use Sonder\Plugins\LanguagePlugin;
use Sonder\Plugins\RouterPlugin;

#[IController]
#[ICronController]
final class CronController extends CoreController implements ICronController
{
    /**
     * @return IResponseObject
     * @throws CoreException
     * @throws ModelException
     * @throws ValuesObjectException
     */
    final public function displayRun(): IResponseObject
    {
        /* @var $cronModel CronModel */
        $cronModel = $this->getModel('cron');

        /* @var $cronJobs CronValuesObject[] */
        $cronJobs = $cronModel->getJobsForRunning();

        if (empty($cronJobs)) {
            $this->response->setContent("No Cron Jobs For Running\n");

            return $this->response;
        }

        $successfulJobsCount = 0;
        $failedJobsCount = 0;

        foreach ($cronJobs as $cronVO) {
            $jobStatus = $cronModel->runJob($cronVO);

            if ($jobStatus) {
                $successfulJobsCount++;
            }

            if (!$jobStatus) {
                $failedJobsCount++;
            }
        }

        $this->response->setContent(
            sprintf(
                "Successful Jobs: %d\nFailed Jobs: %d\n",
                $successfulJobsCount,
                $failedJobsCount
            )
        );

        return $this->response;
    }

    /**
     * @return void
     * @throws CoreException
     */
    final public function jobRouter(): void
    {
        /* @var $routerPlugin RouterPlugin */
        $routerPlugin = $this->getPlugin('router');

        $routerPlugin->cleanCache();
    }

    /**
     * @return void
     * @throws CoreException
     * @throws LanguageException
     */
    final public function jobTranslations(): void
    {
        /* @var $languagePlugin LanguagePlugin */
        $languagePlugin = $this->getPlugin('language');

        $languagePlugin->generateDictionaries();
    }
}
