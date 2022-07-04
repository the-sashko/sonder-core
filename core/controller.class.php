<?php

namespace Sonder\Core;

use Sonder\Enums\ConfigNamesEnum;
use Sonder\Enums\EventTypesEnum;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\ConfigException;
use Sonder\Exceptions\ControllerException;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IRequestObject;
use Sonder\Interfaces\IResponseObject;
use Sonder\Plugins\TemplaterPlugin;

#[IController]
class CoreController extends CoreObject implements IController
{
    final protected const DEFAULT_LANGUAGE = 'en';

    final protected const DEFAULT_RENDER_CACHE_TTL = 1800; // 30 min

    protected const THEME_CONFIG_VALUE = 'theme';

    /**
     * @var IResponseObject
     */
    #[IResponseObject]
    protected IResponseObject $response;

    /**
     * @var string
     */
    private string $_language = CoreController::DEFAULT_LANGUAGE;

    /**
     * @var array
     */
    private array $_renderValues = [];

    /**
     * @var string|null
     */
    protected ?string $renderTheme = null;

    /**
     * @param IRequestObject $request
     * @throws ConfigException
     * @throws ControllerException
     */
    public function __construct(
        #[IRequestObject]
        protected IRequestObject $request
    ) {
        parent::__construct();

        $this->response = new ResponseObject();

        $mainConfig = $this->config->get(ConfigNamesEnum::MAIN);
        $seoConfig = $this->config->get(ConfigNamesEnum::SEO);

        if (
            empty($this->renderTheme) &&
            !empty($mainConfig) &&
            $this->config->hasValue(
                ConfigNamesEnum::MAIN,
                static::THEME_CONFIG_VALUE
            )
        ) {
            $this->renderTheme = $this->config->getValue(
                ConfigNamesEnum::MAIN,
                static::THEME_CONFIG_VALUE
            );
        }

        $this->assign([
            'meta' => array_merge_recursive($mainConfig, $seoConfig),
            'current_host' => $this->request->getHost(),
            'current_url' => $this->request->getUrl(),
            'current_full_url' => $this->request->getFullUrl(),
            'current_language' => $this->_language,
            'csrf_token' => $this->request->getCsrfToken()
        ]);

        $values = (new CoreEvent)->run(
            EventTypesEnum::INIT_CONTROLLER,
            [
                'request' => $this->request,
                'response' => $this->response,
                'render_theme' => $this->renderTheme,
                'render_values' => $this->_renderValues
            ]
        );

        /* @var IRequestObject|null $request */
        $request = $values['request'] ?? null;

        /* @var IResponseObject|null $request */
        $response = $values['response'] ?? null;

        $renderTheme = $values['render_theme'] ?? null;
        $renderValues = $values['render_values'] ?? null;

        if (empty($request)) {
            throw new ControllerException(
                ControllerException::MESSAGE_CONTROLLER_REQUEST_IS_EMPTY,
                AppException::CODE_CONTROLLER_REQUEST_IS_EMPTY
            );
        }

        if (empty($response)) {
            throw new ControllerException(
                ControllerException::MESSAGE_CONTROLLER_RESPONSE_IS_EMPTY,
                AppException::CODE_CONTROLLER_RESPONSE_IS_EMPTY
            );
        }

        $this->request = $request;
        $this->response = $response;
        $this->renderTheme = empty($renderTheme) ? null : (string)$renderTheme;
        $this->_renderValues = (array)$renderValues;
    }

    /**
     * @param string $url
     * @param bool $isPermanent
     * @return IResponseObject
     */
    final protected function redirect(
        string $url,
        bool $isPermanent = false
    ): IResponseObject {
        $this->response->redirect->setUrl($url);
        $this->response->redirect->setIsPermanent($isPermanent);

        return $this->response;
    }

    /**
     * @param array|null $values
     * @return void
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
     * @return IResponseObject
     * @throws ControllerException
     * @throws ConfigException
     * @throws CoreException
     */
    final protected function render(?string $page = null): IResponseObject
    {
        if (empty($page)) {
            throw new ControllerException(
                ControllerException::MESSAGE_CONTROLLER_VIEW_PAGE_IS_NOT_SET,
                AppException::CODE_CONTROLLER_VIEW_PAGE_IS_NOT_SET
            );
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
            EventTypesEnum::BEFORE_RENDER,
            [
                'render_values' => $this->_renderValues
            ]
        );

        $this->_renderValues = $values['render_values'];

        $content = $templaterPlugin->render($page, $this->_renderValues, $ttl);

        $values = (new CoreEvent)->run(
            EventTypesEnum::AFTER_RENDER,
            [
                'content' => $content
            ]
        );

        $content = $values['content'] ?? null;

        $this->response->setContent($content);

        return $this->response;
    }

    /**
     * @return string
     * @throws ControllerException
     */
    private function _getRenderTheme(): string
    {
        if (empty($this->renderTheme)) {
            throw new ControllerException(
                ControllerException::MESSAGE_CONTROLLER_FRONTEND_THEME_IS_NOT_SET,
                AppException::CODE_CONTROLLER_FRONTEND_THEME_IS_NOT_SET
            );
        }

        return $this->renderTheme;
    }
}
