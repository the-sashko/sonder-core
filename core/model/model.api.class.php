<?php

namespace Sonder\Core;

use Exception;
use Sonder\Core\Interfaces\IModelApi;

abstract class ModelApiCore implements IModelApi
{
    /**
     * @var RequestObject|null
     */
    protected ?RequestObject $request = null;

    /**
     * @var ResponseObject|null
     */
    protected ?ResponseObject $response = null;

    /**
     * @var CoreModel|null
     */
    protected ?CoreModel $model = null;

    /**
     * @return ResponseObject
     */
    abstract public function actionCreate(): ResponseObject;

    /**
     * @return ResponseObject
     */
    abstract public function actionGet(): ResponseObject;

    /**
     * @return ResponseObject
     */
    abstract public function actionUpdate(): ResponseObject;

    /**
     * @return ResponseObject
     */
    abstract public function actionDelete(): ResponseObject;

    /**
     * @param CoreModel $model
     */
    public function __construct(CoreModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param RequestObject $request
     * @param ResponseObject $response
     */
    final public function init(
        RequestObject  $request,
        ResponseObject $response
    ): void
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param array|null $data
     * @param bool $status
     * @return ResponseObject
     * @throws Exception
     */
    final public function getApiResponse(
        ?array $data = null,
        bool   $status = true
    ): ResponseObject
    {
        $responseContent = [];

        $responseContent['status'] = $status ? 'success' : 'error';

        if (!empty($data)) {
            $responseContent['data'] = $data;
        }

        $this->response->setContentType('json');

        $this->response->setContent(json_encode($responseContent));

        return $this->response;
    }
}
