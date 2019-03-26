<?php
class ErrorPlugin
{
    public function displayError(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine,
        array  $debugBacktrace = [],
        bool   $isJSONOutput = false
    ) : bool {
        if (!$isJSONOutput) {
            return $this->_displayHTMLError(
                $errCode,
                $errMessage,
                $errFile,
                $errLine,
                $debugBacktrace
            );
        }

        return $this->_displayJSONError(
            $errCode,
            $errMessage,
            $errFile,
            $errLine,
            $debugBacktrace
        );
    }

    private function _displayHTMLError(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine,
        array  $debugBacktrace
    ) : bool {
        $isDisplay = (bool) ini_get('display_errors');

        if (!$isDisplay) {
            echo 'Internal Server Error!';
            return false;
        }

        include __DIR__.'/tpl/error.tpl';
        return true;
    }

    private function _displayJSONError(
        int    $errCode,
        string $errMessage,
        string $errFile,
        int    $errLine,
        array  $debugBacktrace
    ) : bool {
        $output = [];
        $isDisplay = (bool) ini_get('display_errors');

        $output['status'] = 'error';
        $output['data'] = [];

        $output['data']['message'] = 'Internal Server Error!';

        if ($isDisplay) {
            $output['data']['error'] = [
                'code'    => $errCode,
                'message' => $errMessage
            ];

            $output['data']['file'] = [
                'file' => $errFile,
                'line' => $errLine
            ];

            $output['data']['trace'] = $debugBacktrace;
        }

        header('Content-Type: application/json');
        echo json_encode($output);

        return true;
    }

    public function displayException(
        string $expMessage,
        bool   $isJSONOutput = false
    ) : bool {
        if (!$isJSONOutput) {
            return $this->_displayHTMLException($expMessage);
        }

        return $this->_displayJSONException($expMessage);
    }

    private function _displayHTMLException(string $expMessage) : bool
    {
        include __DIR__.'/tpl/exception.tpl';
        return true;
    }

    private function _displayJSONException(string $expMessage) : bool
    {
        $output = [];
        $output['status'] = 'error';
        $output['data'] = [
            'message' => $expMessage
        ];

        header('Content-Type: application/json');
        echo json_encode($output);

        return true;
    }
}
?>