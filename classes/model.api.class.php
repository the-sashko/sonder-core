<?php
/**
 * Class For API Model
 */
abstract class ModelApiCore
{
    public $result = null;

    public $get = null;

    public $post = null;

    public function __construct()
    {
        $this->result = new ModelApiResultObject();
    }
    
    public function loadInputData(
        ?array $get = null,
        ?array $post = null
    ): void {
        $this->get  = $get;
        $this->post = $post;
    }

    abstract public function actionCreate(): ModelApiResultObject;

    abstract public function actionGet(): ModelApiResultObject;

    abstract public function actionUpdate(): ModelApiResultObject;

    abstract public function actionDelete(): ModelApiResultObject;
}
