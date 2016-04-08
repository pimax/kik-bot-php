<?php

namespace lib;

use \objects;

/**
 * Class KikApi
 *
 * @package lib
 */
class KikApi
{
    /**
     * Request type GET
     */
    const TYPE_GET = "get";

    /**
     * Request type POST
     */
    const TYPE_POST = "post";

    /**
     * KIK API Url
     *
     * @var string
     */
    protected $apiUrl = 'https://api.kik.com/v1/';

    /**
     * BOT username
     *
     * @var string|null
     */
    protected $botname = null;

    /**
     * BOT API Key
     *
     * @var string|null
     */
    protected $botkey = null;

    /**
     * BOT Webhook URL
     *
     * @var string|null
     */
    protected $boturl = null;

    /**
     * BOT Features
     *
     * @var array
     */
    protected $features = [
        'manuallySendReadReceipts' => false,
        'receiveReadReceipts' => false,
        'receiveIsTyping' => false,
        'receiveDeliveryReceipts' => false,
    ];

    /**
     * KikApi constructor.
     *
     * @param string $botname
     * @param string $botkey
     * @param string|null $boturl
     * @param array $features
     */
    public function __construct($botname, $botkey, $boturl = null, $features = [
        'manuallySendReadReceipts' => false,
        'receiveReadReceipts' => false,
        'receiveIsTyping' => false,
        'receiveDeliveryReceipts' => false,
    ])
    {
        $this->botname = $botname;
        $this->botkey = $botkey;
        $this->boturl = $boturl;
        $this->features = $features;
    }

    /**
     * Send Message
     *
     * @param objects\Message $message
     * @return mixed
     */
    public function send(objects\Message $message)
    {
        return $this->call('message', ['messages' => [$message->getData()]]);
    }

    /**
     * Set configuration
     *
     * @param $webhook
     * @param array $features
     * @return mixed
     */
    public function setConfiguration($webhook, $features = [
        'manuallySendReadReceipts' => false,
        'receiveReadReceipts' => false,
        'receiveIsTyping' => false,
        'receiveDeliveryReceipts' => false,
    ])
    {
        $this->features = $features;

        return $this->call('config', [
                'webhook' => $webhook,
                'features' => $features
        ]);
    }

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->call('config', [], self::TYPE_GET);
    }

    /**
     * Get User Profile
     * @param $from Username
     * @return objects\User
     */
    public function getUserProfile($from)
    {
        return new objects\User($this->call('user/'.$from, [], self::TYPE_GET));
    }

    /**
     * Request to KIK API
     *
     * @param $url Url
     * @param $data Data
     * @param string $type Type of request (GET|POST)
     * @return array
     */
    protected function call($url, $data, $type = self::TYPE_POST)
    {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic '. base64_encode($this->botname.":".$this->botkey)
        ];

        $process = curl_init($this->apiUrl.$url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);

        if($type == self::TYPE_POST) {
            curl_setopt($process, CURLOPT_POST, 1);
        }

        curl_setopt($process, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return, true);
    }
}