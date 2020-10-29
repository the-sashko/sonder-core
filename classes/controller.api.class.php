<?php
/**
 * Core API Controller Class
 */
class ApiControllerCore extends ControllerCore
{
    /**
     * @var ModelCore Model Instance
     */
    public $model = null;

    public function __construct(?string $modelName = null)
    {
        parent::__construct();
        $this->_setModel($modelName);
    }

    /**
     * Set Model Instance
     *
     * @param string|null $action Model Action Name
     */
    public function execute(?string $action = null): void
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
                get_class($model)
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
                get_class($model),
                $action
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_INVALID_API_ACTION_MODEL
            );
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
     * @param string|null $modelName Model Name
     */
    private function _setModel(?string $modelName = null): void
    {
        $securityPlugin = $this->getPlugin('security');

        $modelName = $securityPlugin->escapeInput($modelName);

        $this->model = $this->getModel($modelName);

        if (empty($this->model)) {
            $errorMessage = '%s. Model: %s';

            $errorMessage = sprintf(
                $errorMessage,
                CoreException::MESSAGE_CORE_MODEL_NOT_FOUND,
                $modelName
            );

            throw new CoreException(
                $errorMessage,
                CoreException::CODE_CORE_MODEL_API_NOT_FOUND
            );
        }

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
     * @param ModelApiCore|null $model  ControllerCore Instance
     * @param string|null       $action Name Of Method
     *
     * @return bool Is Method Public And Exists In Controller
     */
    private function _isValidModelAction(
        ?ModelApiCore $apiModel = null,
        ?string       $action   = null
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
