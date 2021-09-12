<?php

/**
 * FormObject Class For Example Model
 */
class ExampleApi extends ModelApiCore
{
    final public function actionCreate(): ModelApiResultObject
    {
        $this->result->setError(
            ModelApiResultObject::METHOD_IS_NOT_IMPLEMENTED
        );

        $this->result->setStatusFail();

        return $this->result;
    }

    final public function actionGet(): ModelApiResultObject
    {
        $this->result->setError(
            ModelApiResultObject::METHOD_IS_NOT_IMPLEMENTED
        );

        $this->result->setStatusFail();

        return $this->result;
    }

    final public function actionUpdate(): ModelApiResultObject
    {
        $this->result->setError(
            ModelApiResultObject::METHOD_IS_NOT_IMPLEMENTED
        );

        $this->result->setStatusFail();

        return $this->result;
    }

    final public function actionDelete(): ModelApiResultObject
    {
        $this->result->setError(
            ModelApiResultObject::METHOD_IS_NOT_IMPLEMENTED
        );

        $this->result->setStatusFail();

        return $this->result;
    }
}
