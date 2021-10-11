<?php

namespace Sonder\Middlewares;

use Sonder\Core\CoreMiddleware;
use Sonder\Core\Interfaces\IMiddleware;
use Sonder\Plugins\SecurityPlugin;

final class SecurityMiddleware extends CoreMiddleware implements IMiddleware
{
    final public function run(): void
    {
        $securityPlugin = new SecurityPlugin();

        $postValues = $this->request->getPostValues();
        $urlValues = $this->request->getUrlValues();
        $apiValues = $this->request->getApiValues();

        if (!empty($postValues)) {
            $postValues = array_map(
                [
                    $securityPlugin,
                    'escapeInput'
                ],
                $postValues
            );
        }

        if (!empty($urlValues)) {
            $urlValues = array_map(
                [
                    $securityPlugin,
                    'escapeInput'
                ],
                $urlValues
            );
        }

        if (!empty($apiValues)) {
            $apiValues = array_map(
                [
                    $securityPlugin,
                    'escapeInput'
                ],
                $apiValues
            );
        }

        $this->request->setPostValues($postValues);
        $this->request->setUrlValues($urlValues);
        $this->request->setApiValues($apiValues);
    }
}
