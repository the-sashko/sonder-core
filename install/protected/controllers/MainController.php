<?php

/**
 * Main Controller Class
 */
class MainController extends ControllerCore
{
    /**
     * Default Site Action
     *
     * @area  default
     * @route /
     *
     * @throws Exception
     */
    final public function displayIndex(): void
    {
        $this->render('main');
    }

    /**
     * Site Action For Static Pages
     *
     * @area       default
     * @route      /page/([a-z]+)/
     * @url_params slug=$1
     *
     * @throws CoreException
     * @throws LanguageException
     */
    final public function displayPage(): void
    {
        $this->displayStaticPage();
    }

    /**
     * Site Action For Error Pages
     *
     * @area       default
     * @route      /error/([0-9]+)/
     * @url_params code=$1
     *
     * @throws CoreException
     */
    final public function displayError(): void
    {
        $this->displayErrorPage();
    }
}

