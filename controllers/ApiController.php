<?php

namespace Sonder\Controllers;

use Sonder\Enums\ContentTypesEnum;
use Sonder\Core\CoreController;
use Sonder\Core\CoreModel;
use Sonder\Enums\HttpMethodsEnum;
use Sonder\Interfaces\IApiController;
use Sonder\Interfaces\IController;
use Sonder\Interfaces\IModel;
use Sonder\Core\ResponseObject;
use Sonder\Exceptions\ApiException;
use Sonder\Exceptions\AppException;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IResponseObject;
use Sonder\Plugins\LoggerPlugin;
use Throwable;

#[IController]
#[IApiController]
final class ApiController extends CoreController implements IApiController
{
    private const API_METHODS = [
        'create' => HttpMethodsEnum::POST,
        'get' => HttpMethodsEnum::GET,
        'update' => HttpMethodsEnum::PATCH,
        'delete' => HttpMethodsEnum::DELETE
    ];

    /**
     * @return IResponseObject
     * @throws CoreException
     */
    final public function displayRun(): IResponseObject
    {
        $response = new ResponseObject(ContentTypesEnum::JSON);

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

            $response->setContent(
                json_encode([
                    'status' => 'error',
                    'data' => [
                        'message' => $exp->getMessage()
                    ]
                ])
            );

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
            '/^\/api\/([a-z]+)\/([a-z]+)\/$/u',
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
     * @throws ApiException
     * @throws CoreException
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
            '/^\/api\/([a-z]+)\/([a-z]+)\/$/u',
            '$2',
            $this->request->getUrl()
        );

        if (!array_key_exists($crudAction, ApiController::API_METHODS)) {
            throw new ApiException(
                ApiException::MESSAGE_API_INVALID_CRUD_ACTION,
                AppException::CODE_API_INVALID_CRUD_ACTION
            );
        }

        $httpMethod = $this->request->getHttpMethod();

        if (ApiController::API_METHODS[$crudAction] != $httpMethod) {
            throw new ApiException(
                ApiException::MESSAGE_API_INVALID_HTTP_METHOD,
                AppException::CODE_API_INVALID_HTTP_METHOD
            );
        }

        return $crudAction;
    }
}
