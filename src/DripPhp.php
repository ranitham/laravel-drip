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
use Drip\SuccessResponse;
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


    public function __call($name, $arguments)
    {
        // Check if the method exists in the Client
        if (method_exists($this->dripClient, $name) && count($arguments) >= 1) {
            $tries = array_pop($arguments);

            do {
                switch (count($arguments)) {
                    case 0:
                        $res = $this->dripClient->$name();
                        break;
                    case 1:
                        $res = $this->dripClient->$name($arguments[0]);
                        break;
                    case 2:
                        $res = $this->dripClient->$name($arguments[0], $arguments[1]);
                        break;
                    case 3:
                        $res = $this->dripClient->$name($arguments[0], $arguments[1], $arguments[2]);
                }

                if ($res instanceof SuccessResponse) {
                    return $res;
                }
                $tries--;

            } while ($tries > 0);

            return $res;

        } else {
            throw new \InvalidArgumentException("No such method: $name");
        }

    }


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
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     */
    public function createOrUpdateOrder($params, $tries = 1)
    {
        if (!isset($params['amount'])) {
            throw new InvalidArgumentException("amount was not specified");
        }

        if (empty($params['id']) && empty($params['email'])) {
            throw new InvalidArgumentException("neither id nor email was specified");
        }

        $req_params = array('orders' => array($params));

        return $this->make_request("$this->account_id/orders", $req_params, Client::POST, $tries);

    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrUpdateRefund($params, $tries = 1)
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

        return $this->make_request("$this->account_id/refunds", $req_params, Client::POST, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCampaigns($params, $tries = 1)
    {
        return $this->get_campaigns($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchCampaign($params, $tries = 1)
    {
        return $this->fetch_campaign($params, $tries);
    }

    /**
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccounts($tries = 1)
    {
        return $this->get_accounts($tries);
    }


    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrUpdateSubscriber($params, $tries = 1)
    {
        return $this->create_or_update_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchSubscriber($params, $tries = 1)
    {
        return $this->fetch_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeSubscriber($params, $tries = 1)
    {
        return $this->subscribe_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeSubscriber($params, $tries = 1)
    {
        return $this->unsubscribe_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tagSubscriber($params, $tries = 1)
    {
        return $this->tag_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function untagSubscriber($params, $tries = 1)
    {
        return $this->untag_subscriber($params, $tries);
    }

    /**
     * @param $params
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function recordEvent($params, $tries = 1)
    {
        return $this->record_event($params, $tries);
    }

    /**
     * @param $url
     * @param array $params
     * @param string $req_method
     * @param int $tries
     * @return \Drip\ResponseInterface
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidArgumentException
     */
    public function makeRequest($url, $params = array(), $req_method = Client::GET, $tries = 1)
    {
        return $this->dripClient->make_request($url, $params, $req_method, $tries);
    }

    /**
     * @return string
     */
    public function getAccountID()
    {
        return $this->account_id;
    }


}