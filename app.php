<?php
namespace SonderCore;

final class App
{
    /**
     * @var string
     */
    private string $_endpointName = 'app';

    /**
     * @var array|null
     */
    private ?array $_middlewares = null;

    final public function __construct()
    {
        if (defined('APP_ENDPOINT')) {
            $this->_endpointName = APP_ENDPOINT;
        }

        if (defined('APP_MIDDLEWARES')) {
            $this->_middlewares = APP_MIDDLEWARES;
        }
    }

    final public function run(): void
    {
        $endpointClass = sprintf(
            '\SonderCore\Endpoints\%sEndpoint',
            mb_convert_case($this->_endpointName, MB_CASE_TITLE)
        );

        $endpoint = new $endpointClass();

        $endpoint->run($this->_middlewares);
    }
}
