<?php
namespace Sonder\Middlewares;

use Exception;
use Sonder\Core\CoreMiddleware;
use Sonder\Core\CoreObject;
use Sonder\Core\Interfaces\IMiddleware;

final class ApiMiddleware extends CoreMiddleware implements IMiddleware
{
    const API_CONTROLLER = 'api';
    const API_CONTROLLER_METHOD = 'displayRun';

    /**
     * @throws Exception
     */
    final public function run(): void
    {
        $securityPlugin = CoreObject::getPlugin('security');

        $apiValues = (string) file_get_contents('php://input');
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
        $this->request->setMethod(ApiMiddleware::API_CONTROLLER_METHOD);
    }
}
