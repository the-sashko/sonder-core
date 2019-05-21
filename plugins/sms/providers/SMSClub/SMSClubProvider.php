<?php
class SMSCluProvider implements ISMSProvider
{
    private $_response = NULL;

    private $_credentials = NULL;

    public function __construct()
    {
        $_response = new SMSClubCredentials();
    }

    public function sendMessage(
        string $phone   = '',
        string $message = ''
    ) //: ISMSResponse
    {
        ///

        $this->_response;
    }
}
?>