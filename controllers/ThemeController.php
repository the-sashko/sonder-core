<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\ResponseObject;

final class ThemeController extends CoreController implements IController
{
    /**
     * @return ResponseObject
     * @throws Exception
     */
    final public function displayInstall(): ResponseObject
    {
        $theme = $this->request->getCliValue('theme');

        if (empty($theme)) {
            $theme = $this->_getThemeFromConfig();
        }

        if (!empty($theme)) {
            $themePlugin = $this->getPlugin('theme', $theme);
            $themePlugin->moveAssets();
            $themePlugin->compileLessFiles();

            //TODO: is it needed?
            //$themePlugin->minifyJsFiles();
        }

        $this->response->setContent(sprintf(
            'Assets From "%s" Theme Are Successfully Installed',
            $theme
        ));

        return $this->response;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    private function _getThemeFromConfig(): ?string
    {
        $mainConfig = $this->config->get('main');

        if (
            array_key_exists('theme', $mainConfig) &&
            !empty($mainConfig['theme'])
        ) {
            return $mainConfig['theme'];
        }

        return null;
    }
}
