<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Provider extends User
{

    public function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $hostname = $this->get_host($server['REMOTE_ADDR']);
        $provider = $this->extract_provider($hostname);
        
        $this->set_type(self::TYPE_PROVIDER);
        $this->set_name($provider);
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_PROVIDER));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_PROVIDER));
        return DataManager::retrieves(self::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     * Extracts a provider from a given hostname
     * 
     * @param $remhost string The remote hostname
     * @return the provider
     */
    public function extract_provider($remhost)
    {
        if ($remhost == "Unknown")
        {
            return $remhost;
        }
        
        $explodedRemhost = explode(".", $remhost);
        $provider = $explodedRemhost[sizeof($explodedRemhost) - 2] . "." . $explodedRemhost[sizeof($explodedRemhost) - 1];
        
        if ($provider == "co.uk" || $provider == "co.jp")
        {
            return $explodedRemhost[sizeof($explodedRemhost) - 3] . $provider;
        }
        else
        {
            return $provider;
        }
    }

    public function get_host($ip)
    {
        $ptr = implode(".", array_reverse(explode(".", $ip))) . ".in-addr.arpa";
        $host = dns_get_record($ptr, DNS_PTR);
        if ($host == null)
        {
            return $ip;
        }
        else
        {
            return $host[0]['target'];
        }
    }
}
