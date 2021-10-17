<?php

namespace Sonder\Plugins\Sms\Platforms;

use Exception;
use SimpleXMLElement;
use Sonder\Plugins\Sms\Interfaces\ISmsPlatform;
use Sonder\Plugins\Sms\Interfaces\ISmsResponse;

final class SmsClubPlatform implements ISmsPlatform
{
    private SmsClubResponse $_response;

    private SmsClubCredentials $_credentials;

    /**
     * @param array $configValues
     */
    final public function __construct(array $configValues)
    {
        $this->_credentials = new SmsClubCredentials($configValues);
        $this->_response = new SmsClubResponse();
    }

    /**
     * @param string|null $phone
     * @param string|null $message
     *
     * @return ISmsResponse
     *
     * @throws Exception
     */
    final public function sendMessage(
        ?string $phone = null,
        ?string $message = null
    ): ISmsResponse
    {

        $phone = trim(preg_replace('/([^0-9]+)/su', '', (string)$phone));

        if (empty($phone)) {
            throw new Exception('SMS Plugin Phone Is Not Set');
        }

        if (empty($message)) {
            throw new Exception('SMS Plugin Message Is Not Set');
        }

        if (!$this->_sendRemoteRequest($phone, $message)) {
            throw new Exception('Can Not Send SMS');
        }

        return $this->_response;
    }

    /**
     * @param ISmsResponse|null $smsResponse
     *
     * @return bool
     */
    final public function checkMessage(
        ?ISmsResponse $smsResponse = null
    ): bool
    {
        //TODO

        return false;
    }

    /**
     * @param string $phone
     * @param string $message
     *
     * @return bool
     */
    private function _sendRemoteRequest(string $phone, string $message): bool
    {
        $curl = curl_init();


        $xmlRequest = file_get_contents(__DIR__ . '/xml/request.xml');

        $login = (string)$this->_credentials->getLogin();
        $token = (string)$this->_credentials->getToken();
        $alphaName = (string)$this->_credentials->getAlphaName();

        $xmlRequest = str_replace('{{login}}', $login, $xmlRequest);
        $xmlRequest = str_replace('{{token}}', $token, $xmlRequest);
        $xmlRequest = str_replace('{{alphaName}}', $alphaName, $xmlRequest);
        $xmlRequest = str_replace('{{phone}}', $phone, $xmlRequest);
        $xmlRequest = str_replace('{{message}}', $message, $xmlRequest);

        $curlHeaders = [
            CURLOPT_URL => $this->_credentials->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $xmlRequest,
            CURLOPT_HTTPHEADER => [
                'content-type: application/xml'
            ]
        ];

        curl_setopt_array($curl, $curlHeaders);

        $curlResponse = curl_exec($curl);
        $curlErr = curl_error($curl);
        $curlHTTPCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (strlen(trim($curlErr))) {
            $this->_setResponseError('cURL Error ' . $curlErr);

            return false;
        }

        if ($curlHTTPCode != 200) {
            $this->_setResponseError('HTTP Response Code #' . $curlHTTPCode);

            return false;
        }

        return $this->_parseXmlResponse($curlResponse);
    }

    /**
     * @param string|null $xmlResponse
     *
     * @return bool
     */
    private function _parseXmlResponse(?string $xmlResponse = null): bool
    {
        $xmlResponse = simplexml_load_string((string)$xmlResponse);

        if (
            !($xmlResponse instanceof SimpleXMLElement) ||
            !property_exists($xmlResponse, 'status')
        ) {
            $this->_setResponseError('Error Parsing XML Response');

            return false;
        }

        $status = (string)$xmlResponse->status;
        $status = mb_convert_case($status, MB_CASE_LOWER);
        $status = trim($status);

        $status = $status === 'ok';

        if (!$status) {
            $errorMessage = $this->_getXmlResponseErrorMessage($xmlResponse);

            $this->_setResponseError($errorMessage);

            return false;
        }

        $messageCode = $this->_getXmlResponseMessageCode($xmlResponse);
        $this->_setResponseSuccess($messageCode);

        return true;
    }

    /**
     * @param SimpleXMLElement|null $xmlResponse
     *
     * @return string
     */
    private function _getXmlResponseErrorMessage(
        ?SimpleXMLElement $xmlResponse = null
    ): string
    {
        if (!property_exists($xmlResponse, 'text')) {
            return 'Error Parsing XML Response';
        }

        return $xmlResponse->text;
    }

    /**
     * @param string|null $errorMessage
     */
    private function _setResponseError(?string $errorMessage = null): void
    {
        $errorMessage = trim((string)$errorMessage);

        if (empty($errorMessage)) {
            $errorMessage = 'Unknown Server Error';
        }

        $this->_response->setStatus();
        $this->_response->setRemoteMessageCode();
        $this->_response->setErrorMessage($errorMessage);
    }

    /**
     * @param string|null $messageCode
     */
    private function _setResponseSuccess(?string $messageCode = null): void
    {
        $messageCode = trim((string)$messageCode);

        $this->_response->setStatus(true);
        $this->_response->setRemoteMessageCode($messageCode);
        $this->_response->setErrorMessage();
    }

    /**
     * @param SimpleXMLElement|null $xmlResponse
     *
     * @return string|null
     */
    private function _getXmlResponseMessageCode(
        ?SimpleXMLElement $xmlResponse = null
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

        return trim($messageCode);
    }
}
