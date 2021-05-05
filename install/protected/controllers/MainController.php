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
     */
    public function displayIndex(): void
    {
        $this->render('main');
    }

    /**
     * Site Action For Static Pages
     */
    public function displayPage(): void
    {
        $this->displayStaticPage();
    }

    /**
     * Site Action For Error Pages
     */
    public function displayError(): void
    {
        $this->displayErrorPage();
    }
}

