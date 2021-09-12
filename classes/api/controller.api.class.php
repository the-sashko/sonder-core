<?php

/**
 * Core API Controller Class
 */
class ApiControllerCore extends ControllerCore
{
    /**
     * @var ModelCore|null Model Instance
     */
    public ?ModelCore $model = null;

    /**
     * @param string|null $modelName
     *
     * @throws CoreException
     */
    public function __construct(?string $modelName = null)
    {
        parent::__construct();
        $this->_setModel($modelName);
    }

    /**
     * Set Model Instance
     *
     * @param string|null $action Model Action Name
     *
     * @throws CoreException
     */
    final public function execute(?string $action = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $action = $securityPlugin->escapeInput($action);

        if (empty($this->model)) {
            throw new CoreException(
                CoreException::MESSAGE_CORE_MODEL_IS_NOT_SET,
                CoreException::CODE_CORE_MODEL_IS_NOT_SET
            );
        }

        if (empty($this->model->api)) {
            $errorMessage = '%s. Model: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_MODEL_API_IS_NOT_SET,
                get_class($this->model)
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_API_IS_NOT_SET
            );
        }

        if (!$this->_isValidModelAction($this->model->api, $action)) {
            $errorMessage = '%s. Model: %s. Action: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_INVALID_API_ACTION_MODEL,
                get_class($this->model),
                $action
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_INVALID_API_ACTION_MODEL
            );
        }

        $postData = (string)file_get_contents('php://input');
        $postData = json_decode($postData, true);

        $escapeMethod = [
            $this->getPlugin('security'),
            'escapeInput'
        ];

        if (!empty($postData)) {
            $this->post = array_map($escapeMethod, $postData);
        }

        $this->model->api->loadInputData($this->get, $this->post);

        $result = $this->model->api->$action();

        if (!$result->getStatus()) {
            $this->returnJson($result->getStatus(), $result->getErrors());
        }

        $this->returnJson($result->getStatus(), $result->getValues());
    }

    /**
     * Set Model Instance
     *
     * @param string|null $modelName
     *
     * @throws CoreException
     */
    private function _setModel(?string $modelName = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $modelName = $securityPlugin->escapeInput($modelName);

        $model = $this->getModel($modelName);

        if (empty($model)) {
            $errorMessage = '%s. Model: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_MODEL_NOT_FOUND,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_NOT_FOUND
            );
        }

        $this->model = $model;

        if (empty($this->model->api)) {
            $errorMessage = '%s. Model: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_MODEL_API_IS_NOT_SET,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_API_IS_NOT_SET
            );
        }
    }

    /**
     * Check Is Method Public And Exists In Controller
     *
     * @param ModelApiCore|null $apiModel ControllerCore Instance
     * @param string|null $action Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    private function _isValidModelAction(
        ?ModelApiCore $apiModel = null,
        ?string       $action = null
    ): bool
    {
        if (empty($apiModel) || empty($action)) {
            return false;
        }

        if (!method_exists($apiModel, $action)) {
            return false;
        }

        $reflection = new ReflectionMethod($apiModel, $action);

        if (!$reflection->isPublic()) {
            return false;
        }

        return true;
    }
}
