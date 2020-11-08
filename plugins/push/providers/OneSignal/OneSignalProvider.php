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
        $url   = $this->_getMessageUrl($url);

        $this->_sendRemoteRequest($message, $title, $image, $url);

        return $this->_response;
    }

    public function getHTMLSnippet(): string
    {
        $htmlSnippet = file_get_contents(__DIR__.'/static/client.html');

        $htmlSnippet = str_replace(
            '{{login}}',
            (string) $this->_credentials->getLogin(),
            (string) $htmlSnippet
        );

        return $htmlSnippet;
    }

    private function _sendRemoteRequest(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): bool
    {
        $curl        = curl_init();
        $curlHeaders = $this->_getCurlHeaders($message, $title, $image, $url);

        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlErr      = (string) curl_error($curl);
        $curlHTTPCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (!empty(trim($curlErr))) {
            $this->_setResponseError(sprintf('cURL Error: %s', $curlErr));

            return false;
        }

        if ($curlHTTPCode != 200) {
            $errorMessage = sprintf(
                'HTTP Response Code #%d',
                (int) $curlHTTPCode
            );

            $this->_setResponseError($errorMessage);

            return false;
        }

        return $this->_parseJsonResponse($curlResponse);
    }

    private function _parseJsonResponse(?string $jsonResponse = null): bool
    {
        $response = (array) json_decode((string) $jsonResponse, true);

        if (!count($response) > 0) {
            $this->_setResponseError('Error Parsing JSON Response');

            return false;
        }

        $messageCode     = $this->_getResponseRemoteCode($response);
        $recipientsCount = $this->_getResponseRecipientsCount($response);
        $errorMessage    = $this->_getResponseErrorMessage($response);

        if (!empty($messageCode)) {
            $this->_setResponseSuccess($messageCode, $recipientsCount);

            return true;
        }

        $this->_setResponseError($errorMessage);

        return false;
    }

    private function _getCurlHeaders(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): array
    {
        $jsonRequest = $this->_getJsonRequest($message, $title, $image, $url);

        return [
            CURLOPT_URL            => $this->_credentials->getUrl(),
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

    private function _getJsonRequest(
        ?string $message = null,
        ?string $title   = null,
        ?string $image   = null,
        ?string $url     = null
    ): string
    {
        $title = (string) $title;
        $image = (string) $image;
        $url   = (string) $url;

        $request = [
            'app_id'   => $this->_credentials->getLogin(),
            'contents' => [
                'en' => $message
            ],
            'included_segments' => [
                $this->_credentials->getSubscribersGroup()
            ]
        ];

        if (!empty(trim($title))) {
            $request['headings'] = [
                'en' => $title
            ];
        }

        if (!empty(trim($image))) {
            $request['large_icon'] = $image;
            $request['small_icon'] = $image;
        }

        if (!empty(trim($url))) {
            $request['url'] = $url;
        }
    
        return json_encode($request);
    }

    private function _setResponseError(?string $errorMessage = null): void
    {
        $errorMessage = (string) $errorMessage;

        if (!empty(trim($errorMessage))) {
            $errorMessage = 'Unknown Server Error';
        }

        $this->_response->setStatus(false);
        $this->_response->setRemoteCode(null);
        $this->_response->setErrorMessage($errorMessage);
        $this->_response->setRecipientsCount(0);
    }

    private function _setResponseSuccess(
        ?string $remoteCode      = null,
        ?int    $recipientsCount = null
    ): void
    {
        $remoteCode = (string) $remoteCode;
        $remoteCode = trim($remoteCode);

        $recipientsCount = (int) $recipientsCount;
        $recipientsCount = $recipientsCount > 0 ? $recipientsCount : 0;

        $this->_response->setStatus(true);
        $this->_response->setRemoteCode($remoteCode);
        $this->_response->setErrorMessage(null);
        $this->_response->setRecipientsCount($recipientsCount);
    }

    private function _getResponseRemoteCode(?array $response = null): ?string
    {
        if (!is_array($response) || !array_key_exists('id', $response)) {
            return null;
        }

        return (string) $response['id'];
    }

    private function _getResponseErrorMessage(?array $response = null): ?string
    {
        if (empty($response) || !array_key_exists('errors', $response)) {
            return null;
        }

        $errors = $response['errors'];

        if (is_scalar($errors)) {
            return (string) $errors;
        }

        return implode('. ', $errors);
    }

    private function _getResponseRecipientsCount(?array $response = null): int
    {
        if (empty($response) || !array_key_exists('recipients', $response)) {
            return 0;
        }

        return (int) $response['recipients'];
    }

    private function _getMessageTitle(?string $title = null): ?string
    {
        if (empty($title)) {
            $title = $this->_credentials->getDefaultMessageTitle();
        }

        return $title;
    }

    private function _getMessageImage(?string $image = null): ?string
    {
        $image = (string) $image;

        if (empty(trim($image))) {
            $image = $this->_credentials->getDefaultMessageImage();
        }

        return $image;
    }

    private function _getMessageURL(?string $url = null): ?string
    {
        $url = (string) $url;

        if (empty(trim($url))) {
            return $this->_credentials->getDefaultMessageURL();
        }

        return $url;
    }
}
