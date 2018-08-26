<?php
/**
 * Created by PhpStorm.
 * User: Ranitha
 * Date: 10/07/2018
 * Time: 11:13 PM
 */

namespace wouterNL\Drip;

use Drip\Client;
use Drip\Exception\InvalidAccountIdException;
use Drip\Exception\InvalidApiTokenException;
use \Drip\Exception\InvalidArgumentException;
use wouterNL\Drip\Interfaces\DripInterface;


class BaseDripClient extends Client
{
    /**
     *
     * @param string $url
     * @param array $params
     * @param string $req_method
     * @return \Drip\ResponseInterface
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidArgumentException
     */
    public function make_request($url, $params = array(), $req_method = self::GET)
    {
        return parent::make_request($url, $params, $req_method);
    }
}

class DripPhp implements DripInterface
{

    /** @var string */
    protected $account_id = '';

    /** @var BaseDripClient */
    private $dripClient;

    /**
     * DripPhp constructor.
     * @param null $api_token
     * @param null $account_id
     * @throws InvalidArgumentException
     * @throws InvalidAccountIdException
     * @throws InvalidApiTokenException
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

        $this->dripClient = new BaseDripClient($api_token, $account_id);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function createOrUpdateOrder($params)
    {
        if (!isset($params['amount'])) {
            throw new InvalidArgumentException("amount was not specified");
        }

        if (empty($params['id']) && empty($params['email'])) {
            throw new InvalidArgumentException("neither id nor email was specified");
        }

        $req_params = array('orders' => array($params));

        return $this->dripClient->make_request("$this->account_id/orders", $req_params, Client::POST);

    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrUpdateRefund($params)
    {
        if (!isset($params['amount'])) {
            throw new InvalidArgumentException("amount was not specified");
        }

        if (empty($params['provider'])) {
            throw new InvalidArgumentException("the refund provider was not specified");
        }

        if (empty($params['order_upstream_id'])) {
            throw new InvalidArgumentException("order_upstream_id was not specified");
        }

        $req_params = array('refunds' => array($params));

        return $this->dripClient->make_request("$this->account_id/refunds", $req_params, Client::POST);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCampaigns($params)
    {
        return $this->dripClient->get_campaigns($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchCampaign($params)
    {
        return $this->dripClient->fetch_campaign($params);
    }

    /**
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccounts()
    {
        return $this->dripClient->get_accounts();
    }


    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrUpdateSubscriber($params)
    {
        return $this->dripClient->create_or_update_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchSubscriber($params)
    {
        return $this->dripClient->fetch_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeSubscriber($params)
    {
        return $this->dripClient->subscribe_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeSubscriber($params)
    {
        return $this->dripClient->unsubscribe_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tagSubscriber($params)
    {
        return $this->dripClient->tag_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function untagSubscriber($params)
    {
        return $this->dripClient->untag_subscriber($params);
    }

    /**
     * @param $params
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function recordEvent($params)
    {
        return $this->dripClient->record_event($params);
    }

    /**
     * @param $url
     * @param array $params
     * @param string $req_method
     * @return \Drip\ResponseInterface
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidArgumentException
     */
    public function makeRequest($url, $params = array(), $req_method = Client::GET)
    {
        return $this->dripClient->make_request($url, $params, $req_method);
    }

    /**
     * @return string
     */
    public function getAccountID()
    {
        return $this->account_id;
    }


}