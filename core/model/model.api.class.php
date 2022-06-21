<?php

namespace Sonder\Core;

use Sonder\Enums\ApiResponseStatusesEnum;
use Sonder\Enums\ContentTypesEnum;
use Sonder\Interfaces\IModel;
use Sonder\Interfaces\IModelApi;
use Sonder\Interfaces\IRequestObject;
use Sonder\Interfaces\IResponseObject;

#[IModelApi]
abstract class ModelApiCore implements IModelApi
{
    /**
     * @var IRequestObject|null
     */
    #[IRequestObject]
    protected ?IRequestObject $request = null;

    /**
     * @var IResponseObject|null
     */
    #[IResponseObject]
    protected ?IResponseObject $response = null;

    /**
     * @var IModel|null
     */
    #[IModel]
    protected ?IModel $model = null;

    /**
     * @return IResponseObject
     */
    abstract public function actionCreate(): IResponseObject;

    /**
     * @return IResponseObject
     */
    abstract public function actionGet(): IResponseObject;

    /**
     * @return IResponseObject
     */
    abstract public function actionUpdate(): IResponseObject;

    /**
     * @return IResponseObject
     */
    abstract public function actionDelete(): IResponseObject;

    /**
     * @param IModel $model
     */
    public function __construct(IModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param IRequestObject $request
     * @param IResponseObject $response
     * @return void
     */
    final public function init(
        IRequestObject $request,
        IResponseObject $response
    ): void {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param array|null $data
     * @param bool $status
     * @return IResponseObject
     */
    final public function getApiResponse(
        ?array $data = null,
        bool $status = true
    ): IResponseObject {
        $responseContent = [
            'status' => ApiResponseStatusesEnum::SUCCESS->value
        ];

        if (!$status) {
            $responseContent['status'] = ApiResponseStatusesEnum::ERROR->value;
        }

        if (!empty($data)) {
            $responseContent['data'] = $data;
        }

        $this->response->setContentType(ContentTypesEnum::JSON);

        $this->response->setContent(json_encode($responseContent));

        return $this->response;
    }
}
