<?php
/**
 * Plugin For Generating Short Links From API
 */
class ShortenerPlugin
{
    /**
     * @var Shortener API Data
     */
    public $APIData = [
        'url'        => '#',
        'user_id'    => -1,
        'user_token' => ''
    ];

    /**
     * Set Shortener API Options
     *
     * @param string $url       Shortener API URL
     * @param int    $userID    Shortener API User
     * @param string $userToken Shortener API Token
     */
    public function setAPIOptions(
        string $url = '#',
        int    $userID = -1,
        string $userToken = ''
    ) : void {
        $this->APIOptions['url']        = $url;
        $this->APIOptions['user_id']    = $userID;
        $this->APIOptions['user_token'] = $userToken;
    }

    /**
     * Get Short URL From Shortener API
     *
     * @param string $url    Full URL For Shorting
     * @param string $source Full URL Source Param
     *
     * @return string Short Link URL
     */
    public function send(
        string $link = '#',
        string $source = 'default'
    ) : string {
        $curlOptions = $this->_getCurlOptions($link, $source);

        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        $curlErr = curl_error($curl);

        curl_close($curl);

        if ($curlErr) {
            $message = 'Error Processing API Request. cURL Error #:'. $curlErr;
            throw new Exception($message);
        }

        $response = json_decode($response, true);

        if (
            !array_key_exists('status', $response) ||
            !array_key_exists('data', $response) ||
            !is_array($response['data']) ||
            !array_key_exists('message', $response['data'])
        ) {
            throw new Exception('API Response Has Bad Format');
        }

        $status = (bool) $response['status'];

        if (!$status) {
            throw new Exception($response['data']['message']);
        }

        $shortURL = $response['data']['message'];

        if (!preg_match(
            '/^((http)|(https))\:\/\/(.*?)\.(.*?)\/(.*?)\/$/su',
            $shortURL
        )) {
            throw new Exception('API Response Has Bad Format');
        }

        return $shortURL;
    }

    /**
     * Get Curl Options For Shortener API Request
     *
     * @param string $url    Full URL For Shorting
     * @param string $source Full URL Source Param
     *
     * @return array List Of Curl Options
     */
    private function _getCurlOptions(
        string $link = '#',
        string $source = 'default'
    ) : array {
        $curlFields = $this->_getCurlFields($link, $source);

        $curlHeaders = $this->_getCurlHeaders();

        return [
            CURLOPT_URL            => $this->APIOptions['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $curlFields,
            CURLOPT_HTTPHEADER     => $curlHeaders
        ];
    }

    /**
     * Get Curl Fields For Shortener API Request
     *
     * @param string $url    Full URL For Shorting
     * @param string $source Full URL Source Param
     *
     * @return string Curl Fields
     */
    private function _getCurlFields(
        string $link = '#',
        string $source = 'default'
    ) : string {
        $curlFields = [
            'link'   => $link,
            'source' => $source
        ];

        return json_encode($curlFields);
    }

    /**
     * Get Curl Headers For Shortener API Request
     *
     * @return array List Of Curl Headers
     */
    private function _getCurlHeaders() : array
    {
        return [
            'Access-Id:    '.$this->APIOptions['user_id'],
            'Access-Token: '.$this->APIOptions['user_token'],
            'Content-Type: application/json'
        ];
    }
}
?>
