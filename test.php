<?php
/**
 * Application Class For Unit Tests
 */
class Test extends App
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Main Method For Application Test
     */
    public function run() : void
    {
        session_start();
    }
}
