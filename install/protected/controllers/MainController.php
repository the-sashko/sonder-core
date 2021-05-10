<?php
/**
 * Main Controller Class
 */
class MainController extends ControllerCore
{
    /**
     * @var string Template Scope
     */
    public $templaterScope = 'default';

    /**
     * Default Site Action
     *
     * @area  default
     * @route /
     */
    public function displayIndex(): void
    {
        $this->render('main');
    }

    /**
     * Site Action For Static Pages
     *
     * @area       default
     * @route      /page/([a-z]+)/
     * @url_params slug=$1
     */
    public function displayPage(): void
    {
        $this->displayStaticPage();
    }

    /**
     * Site Action For Error Pages
     *
     * @area       default
     * @route      /error/([0-9]+)/
     * @url_params code=$1
     */
    public function displayError(): void
    {
        $this->displayErrorPage();
    }
}

