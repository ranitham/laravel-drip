<?php
/**
 * Created by PhpStorm.
 * User: Ranitha
 * Date: 10/07/2018
 * Time: 11:13 PM
 */

namespace wouterNL\Drip;

use \Drip\Exception\InvalidArgumentException;
use wouterNL\Drip\Interfaces\DripInterface;


class DripPhp extends \Drip\Client implements DripInterface
{

    /** @var string */
    protected $account_id = '';

    /**
     * DripPhp constructor.
     * @param null $api_token
     * @param null $account_id
     * @throws InvalidArgumentException
     */
    public function __construct($api_token = null, $account_id = null)
    {
        if (!$api_token) {
            $api_token = config('drip.api_token', 'api_token');
        }
        if (!$account_id) {
            $account_id = config('drip.account_id', 'account_id');
        }
        $api_token = trim($api_token);
        $account_id = trim($account_id);

        $this->account_id = $account_id;

        if (empty($api_token) || !preg_match('#^[\w-]+$#si', $api_token)) {
            throw new InvalidArgumentException("Missing or invalid Drip API token.");
        }
        if (empty($account_id)) {
            throw new InvalidArgumentException("Missing or invalid Drip Account ID.");
        }

        parent::__construct($api_token, $account_id);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create_or_update_order($params)
    {
        if (empty($params['amount'])) {
            throw new InvalidArgumentException("amount was not specified");
        }

        if (empty($params['id']) && empty($params['email'])) {
            throw new InvalidArgumentException("neither id nor email was specified");
        }

        $req_params = array('orders' => array($params));

        return $this->make_request("$this->account_id/orders", $req_params, self::POST);

    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCampaigns($params)
    {
        return $this->get_campaigns($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchCampaign($params)
    {
        return $this->fetch_campaign($params);
    }

    /**
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccounts()
    {
        return $this->get_accounts();
    }


    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrUpdateSubscriber($params)
    {
        return $this->create_or_update_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchSubscriber($params)
    {
        return $this->fetch_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeSubscriber($params)
    {
        return $this->subscribe_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeSubscriber($params)
    {
        return $this->unsubscribe_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tagSubscriber($params)
    {
        return $this->tag_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function untagSubscriber($params)
    {
        return $this->untag_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function recordEvent($params)
    {
        return $this->record_event($params);
    }

    /**
     * @param $url
     * @param array $params
     * @param string $req_method
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeRequest($url, $params = array(), $req_method = self::GET)
    {
        return $this->make_request($url, $params, $req_method);
    }

    /**
     * @return string
     */
    public function getAccountID(){
        return $this->account_id;
    }


}