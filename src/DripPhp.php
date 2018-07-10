<?php
/**
 * Created by PhpStorm.
 * User: Ranitha
 * Date: 10/07/2018
 * Time: 11:13 PM
 */

namespace wouterNL\Drip;

use \Drip\Exception\InvalidArgumentException;


class DripPhp extends \Drip\Client
{

    /** @var string */
    private $account_id = '';

    /**
     * DripClient constructor.
     * @param $api_token
     * @param $account_id
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
            throw new Exception("Missing or invalid Drip API token.");
        }
        if (empty($account_id)) {
            throw new Exception("Missing or invalid Drip Account ID.");
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

}