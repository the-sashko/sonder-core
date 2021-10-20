<?php

namespace Sonder\Plugins;

final class ErrorPlugin
{
    const OUTPUT_FORMAT_HTML = 'html';

    const OUTPUT_FORMAT_JSON = 'json';

    const HTML_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_html.phtml';

    const TEXT_ERROR_TEMPLATE_PATH = __DIR__ . '/templates/error_text.phtml';

    const HTTP_ERROR_CODE = 500;

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     * @param string|null $outputFormat
     *
     * @return bool
     */
    final public function displayError(
        int     $code,
        string  $message,
        string  $file,
        int     $line,
        ?array  $debugBacktrace = null,
        ?string $outputFormat = null
    ): bool
    {
        http_response_code(ErrorPlugin::HTTP_ERROR_CODE);

        if ($outputFormat == ErrorPlugin::OUTPUT_FORMAT_HTML) {
            return $this->_displayHtmlError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        if ($outputFormat == ErrorPlugin::OUTPUT_FORMAT_JSON) {
            return $this->_displayJsonError(
                $code,
                $message,
                $file,
                $line,
                $debugBacktrace
            );
        }

        return $this->_displayTextError(
            $code,
            $message,
            $file,
            $line,
            $debugBacktrace
        );
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayHtmlError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool)ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include ErrorPlugin::HTML_ERROR_TEMPLATE_PATH;

        return true;
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayJsonError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $output = [];

        $isDisplay = (bool)ini_get('display_errors');

        $output['status'] = 'error';
        $output['data'] = [];

        $output['data']['message'] = 'Internal Server Error!';

        if ($isDisplay) {
            $output['data']['error'] = [
                'code' => $code,
                'message' => $message
            ];

            $output['data']['file'] = [
                'file' => $file,
                'line' => $line
            ];

            $output['data']['trace'] = $debugBacktrace;
        }

        if (!defined('PHP_UNIT')) {
            header('Content-Type: application/json');
        }

        echo json_encode($output);

        return true;
    }

    /**
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array|null $debugBacktrace
     *
     * @return bool
     */
    private function _displayTextError(
        int    $code,
        string $message,
        string $file,
        int    $line,
        ?array $debugBacktrace
    ): bool
    {
        $isDisplay = (bool)ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';

            return false;
        }

        include ErrorPlugin::TEXT_ERROR_TEMPLATE_PATH;

        return true;
    }
}
