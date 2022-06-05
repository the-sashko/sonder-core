<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IController;
use Sonder\Plugins\TemplaterPlugin;

class CoreController extends CoreObject implements IController
{
    const DEFAULT_LANGUAGE = 'en';

    const DEFAULT_RENDER_CACHE_TTL = 30 * 60;

    /**
     * @var RequestObject
     */
    protected RequestObject $request;

    /**
     * @var ResponseObject
     */
    protected ResponseObject $response;

    /**
     * @var string
     */
    private string $_language;

    /**
     * @var array
     */
    private array $_renderValues = [];

    /**
     * @var string|mixed|null
     */
    protected ?string $renderTheme = null;

    /**
     * @param RequestObject $request
     * @throws Exception
     */
    public function __construct(RequestObject $request)
    {
        parent::__construct();

        $this->request = $request;

        $this->response = new ResponseObject();

        $this->_language = static::DEFAULT_LANGUAGE;

        $mainConfig = $this->config->get('main');
        $seoConfig = $this->config->get('seo');

        if (
            empty($this->renderTheme) &&
            !empty($mainConfig) &&
            array_key_exists('theme', $mainConfig)
        ) {
            $this->renderTheme = (string)$mainConfig['theme'];
        }

        if (!empty($mainConfig)) {
            $this->assign([
                'meta' => $mainConfig
            ]);
        }

        if (!empty($seoConfig)) {
            $this->assign([
                'meta' => $seoConfig
            ]);
        }

        $this->assign([
            'current_host' => $this->request->getHost(),
            'current_url' => $this->request->getUrl(),
            'current_full_url' => $this->request->getFullUrl(),
            'current_language' => $this->_language,
            'csrf_token' => $this->request->getCsrfToken()
        ]);

        $values = (new CoreEvent)->run(
            CoreEvent::TYPE_INIT_CONTROLLER,
            [
                'request' => $this->request,
                'response' => $this->response,
                'render_values' => $this->_renderValues,
                'render_theme' => $this->renderTheme
            ]
        );

        $this->request = $values['request'];
        $this->response = $values['response'];
        $this->_renderValues = $values['render_values'];
        $this->renderTheme = $values['render_theme'];
    }

    /**
     * @param string $url
     * @param bool $isPermanent
     * @return ResponseObject
     */
    final protected function redirect(
        string $url,
        bool   $isPermanent = false
    ): ResponseObject
    {
        $this->response->redirect->setUrl($url);
        $this->response->redirect->setIsPermanent($isPermanent);

        return $this->response;
    }

    /**
     * @param array|null $values
     */
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
     * @return ResponseObject
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

        /* @var TemplaterPlugin $templaterPlugin */
        $templaterPlugin = $this->getPlugin('templater', $themeName);

        $values = (new CoreEvent)->run(
            CoreEvent::TYPE_BEFORE_RENDER,
            [
                'render_values' => $this->_renderValues
            ]
        );

        $this->_renderValues = $values['render_values'];

        $content = $templaterPlugin->render($page, $this->_renderValues, $ttl);

        $values = (new CoreEvent)->run(
            CoreEvent::TYPE_AFTER_RENDER,
            [
                'content' => $content
            ]
        );

        $content = $values['content'];

        $this->response->setContent($content);

        return $this->response;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function _getRenderTheme(): string
    {
        if (empty($this->renderTheme)) {
            throw new Exception('Frontend Theme Is Not Set');
        }

        return $this->renderTheme;
    }
}
