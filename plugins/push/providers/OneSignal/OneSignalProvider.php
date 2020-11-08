<?php
class OneSignalProvider implements IPushProvider
{
    private $_response = null;

    private $_credentials = null;

    public function __construct()
    {
        $this->_credentials = new OneSignalCredentials();
        $this->_response    = new OneSignalResponse();
    }

    public function sendMessage(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): IPushResponse
    {
        $title = $this->_getMessageTitle($title);
        $image = $this->_getMessageImage($image);
        $url   = $this->_getMessageURL($url);

        $this->_sendRemoteRequest($message, $title, $image, $url);

        return $this->_response;
    }

    public function getHTMLSnippet(): string
    {
        $htmlSnippet = file_get_contents(__DIR__.'/static/client.html');
        $htmlSnippet = str_replace(
            '{{login}}',
            $this->_credentials->getLogin(),
            $htmlSnippet
        );

        return $htmlSnippet;
    }

    private function _sendRemoteRequest(
        string $message = '',
        string $title   = '',
        string $image   = '',
        string $url     = '#'
    ): bool
    {
        $curl        = curl_init();
        $curlHeaders = $this->_getCurlHeaders($message, $title, $image, $url);

        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlErr      = (string) curl_error($curl);
        $curlHTTPCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (strlen(trim($curlErr))) {
            $this->_setResponseError('cURL Error '.$curlErr);

            return false;
        }

        if ($curlHTTPCode != 200) {
            $this->_setResponseError('HTTP Response Code #'.$curlHTTPCode);

            return false;
        }

        return $this->_parseJSONResponse($curlResponse);
    }

    private function _parseJSONResponse(string $jsonResponse = ''): bool
    {
        $response = (array) json_decode($jsonResponse, true);

        if (!count($response) > 0) {
            $this->_setResponseError('Error Parsing JSON Response');

            return false;
        }

        $messageCode     = $this->_getResponseRemoteCode($response);
        $recipientsCount = $this->_getResponseRecipientsCount($response);
        $errorMessage    = $this->_getResponseErrorMessage($response);

        if (strlen($messageCode) > 0) {
            $this->_setResponseSuccess($messageCode, $recipientsCount);

            return true;
        }

        $this->_setResponseError($errorMessage);

        return false;
    }

    private function _getCurlHeaders(
        string $message = '',
        string $title   = '',
        string $image   = '',
        string $url     = '#'
    ) : array
    {
        $jsonRequest = $this->_getJSONRequest($message, $title, $image, $url);

        return [
            CURLOPT_URL            => $this->_credentials->getURL(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $jsonRequest,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic '.$this->_credentials->getToken()
            ]
        ];
    }

    private function _getJSONRequest(
        string $message = '',
        string $title   = '',
        string $image   = '',
        string $url     = '#'
    ) : string
    {
        $request = [
            'app_id'   => $this->_credentials->getLogin(),
            'contents' => [
                'en' => $message
            ],
            'included_segments' => [
                $this->_credentials->getSubscribersGroup()
            ]
        ];

        if (strlen(trim($title)) > 0 ) {
            $request['headings'] = [
                'en' => $title
            ];
        }

        if (strlen(trim($image)) > 0 ) {
            $request['large_icon'] = $image;
            $request['small_icon'] = $image;
        }

        if (strlen(trim($url)) > 0 && $url != '#') {
            $request['url'] = $url;
        }
    
        return json_encode($request);
    }

    private function _setResponseError(string $errorMessage = '') : void
    {
        if (strlen(trim($errorMessage)) < 1) {
            $errorMessage = 'Unknown Server Error';
        }

        $this->_response->setStatus(false);
        $this->_response->setRemoteCode(null);
        $this->_response->setErrorMessage($errorMessage);
        $this->_response->setRecipientsCount(0);
    }
    
    private function _setResponseSuccess(
        string $remoteCode   = '',
        int    $recipientsCount = 0
    ) : void
    {
        $remoteCode = trim($remoteCode);

        if (strlen($remoteCode) < 1) {
            $remoteCode = '';
        }
        
        if ($recipientsCount < 0) {
            $recipientsCount = 0;
        }

        $this->_response->setStatus(true);
        $this->_response->setRemoteCode($remoteCode);
        $this->_response->setErrorMessage(null);
        $this->_response->setRecipientsCount($recipientsCount);
    }

    private function _getResponseRemoteCode(array $response = []) : string
    {
        if (!array_key_exists('id', $response)) {
            return '';
        }

        return (string) $response['id'];
    }

    private function _getResponseErrorMessage(array $response = []) : string
    {
        if (!array_key_exists('errors', $response)) {
            return '';
        }

        $errors = $response['errors'];

        if (is_scalar($errors)) {
            return (string) $errors;
        }

        return implode('. ', $errors);
    }

    private function _getResponseRecipientsCount(array $response = []) : int
    {
        if (!array_key_exists('recipients', $response)) {
            return 0;
        }

        return (int) $response['recipients'];
    }

    private function _getMessageTitle(?string $title = null): string
    {
        if (empty($title) {
            $title = $this->_credentials->getDefaultMessageTitle();
        }

        return $title;
    }

    private function _getMessageImage(string $image = '') : string
    {
        if (strlen(trim($image)) < 1) {
            $image = $this->_credentials->getDefaultMessageImage();
        }

        return $image;
    }

    private function _getMessageURL(string $url = '#') : string
    {
        if (strlen(trim($url)) < 10) {
            $url = $this->_credentials->getDefaultMessageURL();
        }

        if (strlen(trim($url)) < 10) {
            $url = '#';
        }

        return $url;
    }
}
