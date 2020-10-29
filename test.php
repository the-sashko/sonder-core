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

    /**
     *  Mock Redirect Rules
     */
    public function routeRedirect(): void
    {
        //Mock For App::routeRedirect()
    }

    /**
     *  Mock Rewrite Rules
     */
    public function routeRewrite(): ?string
    {
        //Mock For App::routeRewrite()

        return null;
    }
}
