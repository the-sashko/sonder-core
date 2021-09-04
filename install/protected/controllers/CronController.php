<?php

/**
 * Cron Controller Class
 */
class CronController extends CronControllerCore
{
    /**
     * Clean Router Cache Cron Job
     *
     * @throws CoreException
     */
    final public function jobRouter(): void
    {
        $routerPlugin = $this->getPlugin('router');
        $routerPlugin->cleanCache();
    }

    /**
     * Generate Translations
     *
     * @throws CoreException
     */
    final public function jobTranslations(): void
    {
        $this->getPlugin('language')->generateDictionaries();
    }
}
