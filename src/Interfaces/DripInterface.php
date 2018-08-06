<?php
/**
 * Created by PhpStorm.
 * User: Ranitha
 * Date: 6/08/2018
 * Time: 11:06 PM
 */

namespace wouterNL\Drip\Interfaces;


interface DripInterface
{
    public function getCampaigns($params);
    public function fetchCampaign($params);
    public function getAccounts();
    public function createOrUpdateSubscriber($params);
    public function fetchSubscriber($params);
    public function subscribeSubscriber($params);
    public function unsubscribeSubscriber($params);
    public function tagSubscriber($params);
    public function untagSubscriber($params);
    public function recordEvent($params);
    public function makeRequest($url, $params = array(), $req_method = self::GET);
}
