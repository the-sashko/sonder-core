<?php

namespace Sonder\Controllers;

use Exception;
use Sonder\Core\CoreController;
use Sonder\Core\CoreModel;
use Sonder\Core\Interfaces\IController;
use Sonder\Core\Interfaces\IModel;
use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Plugins\LoggerPlugin;
use Throwable;

final class ApiController extends CoreController implements IController
{
    const CRUD_HTTP_ACTIONS = [
        'create' => RequestObject::HTTP_METHOD_POST,
        'get' => RequestObject::HTTP_METHOD_GET,
        'update' => RequestObject::HTTP_METHOD_PUT,
        'delete' => RequestObject::HTTP_METHOD_DELETE
    ];

    final public function displayRun(): ResponseObject
    {
        $response = new ResponseObject();
        $response->setContentType('json');

        try {
            $this->_checkUrlFormat();

            /* @var $model CoreModel */
            $model = $this->_getModelFromUrl();
            $crudAction = $this->_getCrudActionFromUrl();

            $crudAction = sprintf(
                'action%s',
                mb_convert_case($crudAction, MB_CASE_TITLE)
            );

            $model->api->init($this->request, $response);

            return $model->api->$crudAction();
        } catch (ApiException $exp) {
            /* @var $loggerPlugin LoggerPlugin */
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = sprintf(
                'Error: %s. Debug data: %s',
                $exp->getMessage(),
                json_encode([
                    'url' => $this->request->getUrl(),
                    'http_method' => $this->request->getHttpMethod(),
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getIp(),
                    'api_values' => $this->request->getApiValues()
                ])
            );

            $loggerPlugin->logError($errorMessage, 'api');

            $response->setContent(json_encode([
                'status' => 'error',
                'data' => [
                    'message' => $exp->getMessage()
                ]
            ]));

            return $response;
        }
    }

    /**
     * @return void
     * @throws ApiException
     */
    private function _checkUrlFormat(): void
    {
        if (!preg_match(
            '/^\/api\/([a-z]+)\/([a-z]+)\/$/su',
            $this->request->getUrl()
        )) {
            throw new ApiException(
                ApiException::MESSAGE_API_URL_HAS_BAD_FORMAT,
                AppException::CODE_API_URL_HAS_BAD_FORMAT
            );
        }
    }

    /**
     * @return IModel
     * @throws Exception
     */
    private function _getModelFromUrl(): IModel
    {
        $modelName = preg_replace(
            '/^\/api\/([a-z]+)\/(.*?)\/$/su',
            '$1',
            $this->request->getUrl()
        );

        try {
            $model = $this->getModel($modelName);
        } catch (Throwable $thr) {
            /* @var $loggerPlugin LoggerPlugin */
            $loggerPlugin = $this->getPlugin('logger');

            $errorMessage = sprintf(
                'Error: %s. Debug data: %s',
                $thr->getMessage(),
                json_encode([
                    'url' => $this->request->getUrl(),
                    'http_method' => $this->request->getHttpMethod(),
                    'user_agent' => $this->request->getUserAgent(),
                    'ip' => $this->request->getIp(),
                    'api_values' => $this->request->getApiValues()
                ])
            );

            $loggerPlugin->logError($errorMessage, 'api');

            $errorMessage = sprintf(
                ApiException::MESSAGE_API_CAN_NOT_RETRIEVE_MODEL,
                $modelName
            );

            throw new ApiException(
                $errorMessage,
                AppException::CODE_API_CAN_NOT_RETRIEVE_MODEL
            );
        }

        if (empty($model->api)) {
            $errorMessage = sprintf(
                ApiException::MESSAGE_API_NOT_SUPPORTED_API_CALLS,
                $modelName
            );

            throw new ApiException(
                $errorMessage,
                AppException::CODE_API_NOT_SUPPORTED_API_CALLS
            );
        }

        return $model;
    }

    /**
     * @return string
     * @throws ApiException
     */
    private function _getCrudActionFromUrl(): string
    {
        $crudAction = preg_replace(
            '/^\/api\/([a-z]+)\/([a-z]+)\/$/su',
            '$2',
            $this->request->getUrl()
        );

        if (!array_key_exists(
            $crudAction,
            ApiController::CRUD_HTTP_ACTIONS
        )) {
            throw new ApiException(
                ApiException::MESSAGE_API_INVALID_CRUD_ACTION,
                AppException::CODE_API_INVALID_CRUD_ACTION
            );
        }

        $httpMethod = $this->request->getHttpMethod();

        if (ApiController::CRUD_HTTP_ACTIONS[$crudAction] != $httpMethod) {
            throw new ApiException(
                ApiException::MESSAGE_API_INVALID_HTTP_METHOD,
                AppException::CODE_API_INVALID_HTTP_METHOD
            );
        }

        return $crudAction;
    }
}
