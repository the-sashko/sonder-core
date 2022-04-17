<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\ResponseObject;
use Sonder\Models\Cron;
use Sonder\Models\Cron\CronValuesObject;
use Sonder\Plugins\Database\Exceptions\DatabaseCacheException;
use Sonder\Plugins\Database\Exceptions\DatabasePluginException;
use Sonder\Plugins\Language\Exceptions\LanguageException;
use Sonder\Plugins\LanguagePlugin;
use Sonder\Plugins\RouterPlugin;

final class CronController extends CoreController implements IController
{
    /**
     * @no_cache true
     *
     * @return ResponseObject
     * @throws DatabaseCacheException
     * @throws DatabasePluginException
     * @throws Exception
     */
    final public function displayRun(): ResponseObject
    {
        /* @var $cronModel Cron */
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

        $this->response->setContent(sprintf(
            "Successful Jobs: %d\nFailed Jobs: %d\n",
            $successfulJobsCount,
            $failedJobsCount
        ));

        return $this->response;
    }

    /**
     * @return void
     * @throws Exception
     */
    final public function jobRouter(): void
    {
        /* @var $routerPlugin RouterPlugin */
        $routerPlugin = $this->getPlugin('router');

        $routerPlugin->cleanCache();
    }

    /**
     * @return void
     * @throws LanguageException
     */
    final public function jobTranslations(): void
    {
        /* @var $languagePlugin LanguagePlugin */
        $languagePlugin = $this->getPlugin('language');

        $languagePlugin->generateDictionaries();
    }
}
