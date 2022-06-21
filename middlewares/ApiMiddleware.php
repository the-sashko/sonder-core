<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Exceptions\CoreException;
use Sonder\Interfaces\IApiMiddleware;
use Sonder\Interfaces\IMiddleware;

#[IMiddleware]
#[IApiMiddleware]
final class ApiMiddleware extends CoreMiddleware implements IApiMiddleware
{
    private const API_CONTROLLER = 'api';
    private const API_CONTROLLER_METHOD = 'displayRun';

    /**
     * @return void
     * @throws CoreException
     */
    final public function run(): void
    {
        $securityPlugin = $this->getPlugin('security');

        $apiValues = (string)file_get_contents('php://input');
        $apiValues = (array)json_decode($apiValues, true);

        $apiValues = array_map(
            [
                $securityPlugin,
                'escapeInput'
            ],
            $apiValues
        );

        $postValues = $this->request->getPostValues();

        $postValues = empty($postValues) ? null : $postValues;
        $apiValues = empty($apiValues) ? $postValues : $apiValues;

        $this->request->setApiValues($apiValues);
        $this->request->setPostValues();

        $this->request->setController(ApiMiddleware::API_CONTROLLER);

        $this->request->setControllerMethod(
            ApiMiddleware::API_CONTROLLER_METHOD
        );
    }
}
