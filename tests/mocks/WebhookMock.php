<?php
namespace RAAS\CMS\Bitrix24;

use VolumNet\Bitrix24\Webhook;
use VolumNet\CURL\CURL;

class WebhookMock extends Webhook
{
    /**
     * Class constructor
     * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
     * @param string $webhook Webhook ID
     */
    public function __construct($domain, $webhook)
    {
        parent::__construct($domain, $login, $password);
        $this->url = $domain;
    }


    /**
     * Calls certain method
     * @param string $methodName Method name, without transport extension (i.e. .xml or .json)
     * @param array $data Method data
     * @return mixed Parsed data from method
     * @throws Exception Exception with error response from the method
     */
    public function method($methodName, array $data = array())
    {
        $curl = new CURL();
        $url = 'http://httpbin.org/post';
        $result = $curl->getURL($url, $data);
        $json = json_decode($result);
        if (!$result) {
            throw new Exception('No response retrieved');
        } elseif (!$json) {
            throw new Exception('Cannot parse JSON');
        } elseif ($json->error) {
            throw new Exception($result);
        }
        return $json;
    }
}
