<?php

namespace Sonder\Core\Interfaces;

use Sonder\Core\RequestObject;
use Sonder\Core\ResponseObject;

interface IModelApi
{
    /**
     * @return ResponseObject
     */
    public function actionCreate(): ResponseObject;

    /**
     * @return ResponseObject
     */
    public function actionGet(): ResponseObject;

    /**
     * @return ResponseObject
     */
    public function actionUpdate(): ResponseObject;

    /**
     * @return ResponseObject
     */
    public function actionDelete(): ResponseObject;

    /**
     * @param RequestObject $request
     * @param ResponseObject $response
     */
    public function init(
        RequestObject  $request,
        ResponseObject $response
    ): void;
}
