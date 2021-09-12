<?php

/**
 * Class For API Model
 */
abstract class ModelApiCore
{
    /**
     * @var ModelApiResultObject|null
     */
    protected ?ModelApiResultObject $result = null;

    /**
     * @var ModelCore|null
     */
    protected ?ModelCore $model = null;

    /**
     * @var array|null
     */
    protected ?array $get = null;

    /**
     * @var array|null
     */
    protected ?array $post = null;

    public function __construct(ModelCore $model)
    {
        $this->model = $model;
        $this->result = new ModelApiResultObject();
    }

    /**
     * @param array|null $get
     * @param array|null $post
     */
    final public function loadInputData(
        ?array $get = null,
        ?array $post = null
    ): void
    {
        $this->get = $get;
        $this->post = $post;
    }

    abstract public function actionCreate(): ModelApiResultObject;

    abstract public function actionGet(): ModelApiResultObject;

    abstract public function actionUpdate(): ModelApiResultObject;

    abstract public function actionDelete(): ModelApiResultObject;
}
