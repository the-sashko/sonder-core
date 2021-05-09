<?php
/**
 * Cron Controller Class
 */
class CronController extends CronControllerCore
{
    /**
     * Test Cron Job
     */
    public function jobTest(): void
    {
        // Test Job Logic
    }

    /**
     * Clean Router Cache Cron Job
     */
    public function jobRouter(): void
    {
        $routerPlugin = $this->getPlugin('router');
        $routerPlugin->cleanCache();
    }
}
