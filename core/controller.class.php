<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IController;

class CoreController extends CoreObject implements IController
{
    const DEFAULT_RENDER_CACHE_TTL = 30 * 60;

    /**
     * @var RequestObject
     */
    protected RequestObject $request;

    /**
     * @var ResponseObject
     */
    private ResponseObject $_response;

    /**
     * @var array
     */
    private array $_renderValues = [];

    /**
     * @var string
     */
    private string $_renderTheme;

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    public function __construct(RequestObject $request)
    {
        parent::__construct();

        $this->request = $request;

        $this->_response = new ResponseObject();

        $mainConfig = $this->config->get('main');

        if (!empty($mainConfig) && array_key_exists('theme', $mainConfig)) {
            $this->_renderTheme = (string)$mainConfig['theme'];
        }
    }

    /**
     * @param string $url
     * @param bool $isPermanent
     */
    final protected function redirect(
        string $url,
        bool   $isPermanent = false
    ): void
    {
        $this->_response->redirect->setUrl($url);
        $this->_response->redirect->setIsPermanent($isPermanent);
    }

    final protected function assign(?array $values = null): void
    {
        if (!empty($values)) {
            $this->_renderValues = array_merge_recursive(
                $this->_renderValues,
                $values
            );
        }
    }

    /**
     * @param string|null $page
     *
     * @return ResponseObject
     *
     * @throws Exception
     */
    final protected function render(?string $page = null): ResponseObject
    {
        if (empty($page)) {
            throw new Exception('View Page Is Not Set');
        }

        $ttl = static::DEFAULT_RENDER_CACHE_TTL;

        if (defined('APP_CACHE_TTL') && APP_CACHE_TTL > 0) {
            $ttl = APP_CACHE_TTL;
        }

        if (!defined('APP_CACHE') || !APP_CACHE) {
            $ttl = 0;
        }

        $themeName = $this->_getRenderTheme();

        $templaterPlugin = $this->getPlugin('templater', $themeName);

        $content = $templaterPlugin->render($page, $this->_renderValues, $ttl);

        $this->_response->setContent($content);

        return $this->_response;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    private function _getRenderTheme(): string
    {
        if (empty($this->_renderTheme)) {
            throw new Exception('Frontend Theme Is Not Set');
        }

        return $this->_renderTheme;
    }
}
