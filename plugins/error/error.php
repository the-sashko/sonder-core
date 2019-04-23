<?php
/**
 * Plugin For Handling Errors And Exceptions
 */
class ErrorPlugin
{
    /**
     * Display Error
     *
     * @param int    $errCode        HTTP Responce Code
     * @param string $errMessage     Error Message
     * @param string $errFile        PHP File That Contain Error
     * @param int    $errLine        Number Of Line PHP File That Contain Error
     * @param array  $debugBacktrace Backtrace Of Calls That Contain Error
     * @param bool   $isJSONOutput   Display Error In JSON Format
     *
     * @return bool Is Successfully Displayed Error
     */
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

    /**
     * Display Error In HTML Format
     *
     * @param int    $errCode        HTTP Responce Code
     * @param string $errMessage     Error Message
     * @param string $errFile        PHP File That Contain Error
     * @param int    $errLine        Number Of Line PHP File That Contain Error
     * @param array  $debugBacktrace Backtrace Of Calls That Contain Error
     *
     * @return bool Is Successfully Displayed Error
     */
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

    /**
     * Display Error In JSON Format
     *
     * @param int    $errCode        HTTP Responce Code
     * @param string $errMessage     Error Message
     * @param string $errFile        PHP File That Contain Error
     * @param int    $errLine        Number Of Line PHP File That Contain Error
     * @param array  $debugBacktrace Backtrace Of Calls That Contain Error
     *
     * @return bool Is Successfully Displayed Error
     */
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
        $output['data']   = [];

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

    /**
     * Display Exception
     *
     * @param string $expMessage   Exception Message
     * @param bool   $isJSONOutput Display Exception In JSON Format
     *
     * @return bool Is Successfully Displayed Exception
     */
    public function displayException(
        string $expMessage,
        bool   $isJSONOutput = false
    ) : bool {
        if (!$isJSONOutput) {
            return $this->_displayHTMLException($expMessage);
        }

        return $this->_displayJSONException($expMessage);
    }

    /**
     * Display Exception In HTML Format
     *
     * @param string $expMessage Exception Message
     *
     * @return bool Is Successfully Displayed Exception
     */
    private function _displayHTMLException(string $expMessage) : bool
    {
        include __DIR__.'/tpl/exception.tpl';

        return true;
    }

    /**
     * Display Exception In JSON Format
     *
     * @param string $expMessage Exception Message
     *
     * @return bool Is Successfully Displayed Exception
     */
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
