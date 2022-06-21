<?php

namespace Sonder\Interfaces;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
interface IModelApi
{
    /**
     * @return IResponseObject
     */
    public function actionCreate(): IResponseObject;

    /**
     * @return IResponseObject
     */
    public function actionGet(): IResponseObject;

    /**
     * @return IResponseObject
     */
    public function actionUpdate(): IResponseObject;

    /**
     * @return IResponseObject
     */
    public function actionDelete(): IResponseObject;

    /**
     * @param IRequestObject $request
     * @param IResponseObject $response
     * @return void
     */
    public function init(
        IRequestObject $request,
        IResponseObject $response
    ): void;

    /**
     * @param array|null $data
     * @param bool $status
     * @return IResponseObject
     */
    public function getApiResponse(
        ?array $data = null,
        bool $status = true
    ): IResponseObject;
}
