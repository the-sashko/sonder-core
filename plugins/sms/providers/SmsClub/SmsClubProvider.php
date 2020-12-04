<?php
class SmsClubProvider implements ISmsProvider
{
    private $_response = null;

    private $_credentials = null;

    public function __construct()
    {
        $this->_credentials = new SmsClubCredentials();
        $this->_response    = new SmsClubResponse();
    }

    public function sendMessage(
        ?string $phone   = null,
        ?string $message = null
    ): ?ISmsResponse
    {

        $phone = trim(preg_replace('/([^0-9]+)/su', '', (string) $phone));

        if (empty($phone)) {
            throw new \Exception('SMS Plugin Phone Is Not Set');
        }

        if (empty($message)) {
            throw new \Exception('SMS Plugin Message Is Not Set');
        }

        $this->_sendRemoteRequest($phone, $message);

        return $this->_response;
    }

    public function checkMessage(
        ?ISmsResponse $smsResponse = null
    ): ?ISmsResponse
    {
        //To-Do

        throw new \Exception('Not Implemented Yet');

        return new SmsClubResponse();
    }

    private function _sendRemoteRequest(string $phone, string $message): bool
    {
        $curl        = curl_init();
        $curlHeaders = $this->_getCurlHeaders($phone, $message);

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

        return $this->_parseXmlResponse($curlResponse);
    }

    private function _parseXmlResponse(?string $xmlResponse = null): bool
    {
        $xmlResponse = simplexml_load_string((string) $xmlResponse);

        if (
            !($xmlResponse instanceof SimpleXMLElement) ||
            !property_exists($xmlResponse, 'status')
        ) {
            $this->_setResponseError('Error Parsing XML Response');

            return false;
        }

        $status = $this->_getXmlResponseStatus($xmlResponse);

        if (!$status) {
            $errorMessage = $this->_getXmlResponseErrorMessage($xmlResponse);

            $this->_setResponseError($errorMessage);

            return false;
        }

        $messageCode = $this->_getXmlResponseMessageCode($xmlResponse);
        $this->_setResponseSuccess($messageCode);

        return true;
    }

    private function _getCurlHeaders(string $phone, string $message): array
    {
        $xmlRequest = file_get_contents(__DIR__.'/xml/request.xml');

        $login     = (string) $this->_credentials->getLogin();
        $token     = (string) $this->_credentials->getToken();
        $alphaName = (string) $this->_credentials->getAlphaName();

        $xmlRequest = str_replace('{{login}}', $login, $xmlRequest);
        $xmlRequest = str_replace('{{token}}', $token, $xmlRequest);
        $xmlRequest = str_replace('{{alphaName}}', $alphaName, $xmlRequest);
        $xmlRequest = str_replace('{{phone}}', $phone, $xmlRequest);
        $xmlRequest = str_replace('{{message}}', $message, $xmlRequest);

        return [
            CURLOPT_URL            => $this->_credentials->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $xmlRequest,
            CURLOPT_HTTPHEADER     => [
                'content-type: application/xml'
            ]
        ];
    }

    private function _getXmlResponseErrorMessage(
        ?\SimpleXMLElement $xmlResponse = null
    ): string
    {
        if (!property_exists($xmlResponse, 'text')) {
            return 'Error Parsing XML Response';
        }

        return $xmlResponse->text;
    }

    private function _setResponseError(?string $errorMessage = null): void
    {
        $errorMessage = trim((string) $errorMessage);

        if (empty($errorMessage)) {
            $errorMessage = 'Unknown Server Error';
        }

        $this->_response->setStatus(false);
        $this->_response->setRemoteMessageCode(null);
        $this->_response->setErrorMessage($errorMessage);
    }

    private function _setResponseSuccess(?string $messageCode = null): void
    {
        $messageCode = trim((string) $messageCode);

        $this->_response->setStatus(true);
        $this->_response->setRemoteMessageCode($messageCode);
        $this->_response->setErrorMessage(null);
    }

    private function _getXmlResponseStatus(
        ?\SimpleXMLElement $xmlResponse = null
    ): bool
    {
        $status = (string) $xmlResponse->status;
        $status = mb_convert_case($status, MB_CASE_LOWER);
        $status = trim($status);
        
        return $status === 'ok';
    }

    private function _getXmlResponseMessageCode(
        ?\SimpleXMLElement $xmlResponse = null
    ): ?string
    {
        if (!property_exists($xmlResponse, 'ids')) {
            return null;
        }

        if (!$xmlResponse->ids instanceof SimpleXMLElement) {
            return null;
        }

        if (!property_exists($xmlResponse->ids, 'mess')) {
            return null;
        }

        $messageCode = $xmlResponse->ids->mess;
        $messageCode = trim($messageCode);

        return $messageCode;
    }
}
