<?php

/**
 * Parrent Class For All Controllers And Models
 */
class CommonCore
{
    /**
    * Get Model Instance By Name
    *
    * @param string $model Name Of Model
    * @return ModelCore Insnace Of Model
    */
    public function initModel(string $model = '') : ModelCore
    {
        $modelsDir = __DIR__.'/../../models';

        if (!is_file("{$modelsDir}/{$model}/{$model}.php")) {
            throw new Exception("Missing {$model} Model!");
        }

        require_once("{$modelsDir}/{$model}/{$model}.php");
        $modelInstance = new $model();
        $modelInstance->setConfigData();

        if (is_file("{$modelsDir}/{$model}/{$model}Object.php")) {
            require_once("{$modelsDir}/{$model}/{$model}Object.php");
            $modelInstance->setObject("{$model}Object");
        }

        return $modelInstance;
    }
}

?>