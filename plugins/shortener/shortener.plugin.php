<?php
/**
 * Plugin For Generating Short Links From API
 */
class ShortenerPlugin
{
    /**
     * @var string Default Source
     */
    const DEFAULT_SOURCE = 'default';

    /**
     * @var array|null Shortener API Data
     */
    private $_apiOptions = null;

    /**
     * Set Shortener API Options
     *
     * @param string $url       Shortener API URL
     * @param int    $idUser    Shortener API User
     * @param string $userToken Shortener API Token
     */
    public function setApiOptions(
        string $url,
        int    $idUser,
        string $userToken
    ): void
    {
        $this->_apiOptions = [
            'url'        => $url,
            'id_user'    => $idUser,
            'user_token' => $userToken
        ];
    }

    /**
     * Get Short URL From Shortener API
     *
     * @param string|null $link   Full URL For Shorting
     * @param string|null $source Full URL Source Param
     *
     * @return string|null Short Link URL
     */
    public function send(
        ?string $link   = null,
        ?string $source = null
    ): ?string
    {
        if (empty($link)) {
            return null;
        }

        if (empty($source)) {
            $source = static::DEFAULT_SOURCE;
        }

        $curlOptions = $this->_getCurlOptions($link, $source);
        $curl        = curl_init();

        curl_setopt_array($curl, (array) $curlOptions);

        $response  = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($curlError) {
            $errorMessage = sprintf(
                'Error Processing API Request. Error (cURL): %s',
                $curlError
            );

            throw new \Exception($errorMessage);
        }

        $response = json_decode($response, true);

        if (
            !array_key_exists('status', $response) ||
            !array_key_exists('data', $response) ||
            !is_array($response['data']) ||
            !array_key_exists('message', $response['data'])
        ) {
            throw new \Exception('API Response Has Bad Format');
        }

        $status = (bool) $response['status'];

        if (!$status) {
            throw new \Exception($response['data']['message']);
        }

        $shortUrl = (string) $response['data']['message'];

        if (!preg_match(
            '/^((http)|(https))\:\/\/(.*?)\.(.*?)\/(.*?)\/$/su',
            $shortUrl
        )) {
            throw new \Exception('API Response Has Bad Format');
        }

        return $shortUrl;
    }

    /**
     * Get Curl Options For Shortener API Request
     *
     * @param string $link   Full URL For Shorting
     * @param string $source Full URL Source Param
     *
     * @return array|null List Of Curl Options
     */
    private function _getCurlOptions(string $link, string $source): ?array
    {
        $curlFields  = $this->_getCurlFields($link, $source);
        $curlHeaders = $this->_getCurlHeaders();

        if (
            empty($this->_apiOptions) ||
            !array_key_exists('url', $this->_apiOptions) ||
            empty($this->_apiOptions['url'])
        ) {
            return null;
        }

        return [
            CURLOPT_URL            => $this->_apiOptions['url'],
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
     * @param string $link   Full URL For Shorting
     * @param string $source Full URL Source Param
     *
     * @return string Curl Fields
     */
    private function _getCurlFields(string $link, string $source): string
    {
        $curlFields = [
            'link'   => $link,
            'source' => $source
        ];

        return json_encode($curlFields);
    }

    /**
     * Get Curl Headers For Shortener API Request
     *
     * @return array|null List Of Curl Headers
     */
    private function _getCurlHeaders(): ?array
    {
        if (
            empty($this->_apiOptions) ||
            !array_key_exists('id_user', $this->_apiOptions) ||
            empty($this->_apiOptions['id_user'])
        ) {
            return null;
        }

        if (
            empty($this->_apiOptions) ||
            !array_key_exists('user_token', $this->_apiOptions) ||
            empty($this->_apiOptions['user_token'])
        ) {
            return null;
        }

        return [
            sprintf('Access-Id:    %d', $this->_apiOptions['id_user']),
            sprintf('Access-Token: %s', $this->_apiOptions['user_token']),
            'Content-Type: application/json'
        ];
    }
}
