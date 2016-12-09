<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Country extends User
{

    public function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $hostname = $this->get_host($server['REMOTE_ADDR']);
        
        $country = $this->extract_country($hostname);
        
        $this->set_type(self::TYPE_COUNTRY);
        $this->set_name($country);
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_COUNTRY));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_COUNTRY));
        return DataManager::retrieves(self::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     * Extracts the country code from the remote host
     * 
     * @param Remote Host $remhost instance of $_SERVER['REMOTE_ADDR']
     * @return string country code
     */
    public function extract_country($remhost)
    {
        if ($remhost == "Unknown")
        {
            return $remhost;
        }
        
        // country code is the last value of remote host
        $explodedRemhost = explode(".", $remhost);
        $code = $explodedRemhost[sizeof($explodedRemhost) - 1];
        
        if ($code == 'localhost')
        {
            return "Unknown";
        }
        else
        {
            return $code;
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
