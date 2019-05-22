<?php
class SMSClubProvider implements ISMSProvider
{
    private $_response = NULL;

    private $_credentials = NULL;

    public function __construct()
    {
        $this->_credentials = new SMSClubCredentials();
        $this->_response    = new SMSClubResponse();
    }

    public function sendMessage(
        string $phone   = '',
        string $message = ''
    ) : ISMSResponse
    {
        $phone = $this->_preparePhone($phone);

        $this->_sendRemoteRequest($phone, $message);

        return $this->_response;
    }

    private function _sendRemoteRequest(
        string $phone   = '',
        string $message = ''
    ) : bool
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

            return FALSE;
        }

        if ($curlHTTPCode != 200) {
            $this->_setResponseError('HTTP Response Code #'.$curlHTTPCode);

            return FALSE;
        }

        return $this->_parseXMLResponse($curlResponse);
    }

    private function _parseXMLResponse(string $xmlResponse = '') : bool
    {
        $xmlResponse = $this->_getXMLResponse($xmlResponse);

        if ($xmlResponse == NULL) {
            $this->_setResponseError('Error Parsing XML Response');

            return NULL;
        }

        $status = $this->_getXMLResponseStatus($xmlResponse);

        if (!$status) {
            $errorMessage = $this->_getXMLResponseErrorMessage($xmlResponse);

            $this->_setResponseError($errorMessage);

            return FALSE;
        }

        $messageCode = $this->_getXMLResponseMessageCode($xmlResponse);
        $this->_setResponseSuccess($messageCode);

        return TRUE;
    }

    private function _getCurlHeaders(
        string $phone   = '',
        string $message = ''
    ) : array
    {
        $xmlRequest = $this->_getXMLRequest($phone, $message);

        return [
            CURLOPT_URL            => $this->_credentials->getURL(),
            CURLOPT_RETURNTRANSFER => TRUE,
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

    private function _preparePhone(string $phone = '') : string
    {
        $phone = preg_replace('/([^0-9]+)/su', '', $phone);
        $phone = trim($phone);

        return $phone;
    }

    private function _getXMLRequest(
        string $phone   = '',
        string $message = ''
    ) : string
    {
        $xmlRequest = file_get_contents(__DIR__.'/xml/request.xml');

        $login     = $this->_credentials->getLogin();
        $token     = $this->_credentials->getToken();
        $alphaName = $this->_credentials->getAlphaName();

        $xmlRequest = str_replace('{{login}}', $login, $xmlRequest);
        $xmlRequest = str_replace('{{token}}', $token, $xmlRequest);
        $xmlRequest = str_replace('{{alphaName}}', $alphaName, $xmlRequest);
        $xmlRequest = str_replace('{{phone}}', $phone, $xmlRequest);
        $xmlRequest = str_replace('{{message}}', $message, $xmlRequest);

        return $xmlRequest;
    }

    private function _getXMLResponse(
        string $xmlResponse = ''
    ) : SimpleXMLElement
    {
        $xmlResponse = simplexml_load_string($xmlResponse);

        if (!$xmlResponse instanceof SimpleXMLElement) {
            return NULL;
        }

        if (!property_exists($xmlResponse, 'status')) {
            return NULL;
        }

        return $xmlResponse;
    }

    private function _getXMLResponseErrorMessage(
        SimpleXMLElement $xmlResponse = NULL
    ) : string
    {
        if (!property_exists($xmlResponse, 'text')) {
            return 'Error Parsing XML Response';
        }

        return $xmlResponse->text;
    }

    private function _setResponseError(string $errorMessage = '') : void
    {
        if (strlen(trim($errorMessage)) < 1) {
            $errorMessage = 'Unknown Server Error';
        }

        $this->_response->setStatus(FALSE);
        $this->_response->setRemoteMessageCode('');
        $this->_response->setErrorMessage($errorMessage);
    }

    private function _setResponseSuccess(string $messageCode = '') : void
    {
        $messageCode = trim($messageCode);

        if (strlen($messageCode) < 1) {
            $messageCode = '';
        }

        $this->_response->setStatus(TRUE);
        $this->_response->setRemoteMessageCode($messageCode);
        $this->_response->setErrorMessage('');
    }

    private function _getXMLResponseStatus(
        SimpleXMLElement $xmlResponse = NULL
    ) : bool
    {
        $status = (string) $xmlResponse->status;
        $status = mb_convert_case($status, MB_CASE_LOWER);
        $status = trim($status);
        
        return $status === 'ok';
    }

    private function _getXMLResponseMessageCode(
        SimpleXMLElement $xmlResponse = NULL
    ) : string
    {
        if (!property_exists($xmlResponse, 'ids')) {
            return '';
        }

        if (!$xmlResponse->ids instanceof SimpleXMLElement) {
            return '';
        }

        if (!property_exists($xmlResponse->ids, 'mess')) {
            return '';
        }

        $messageCode = $xmlResponse->ids->mess;
        $messageCode = trim($messageCode);

        return $messageCode;
    }
}
?>
