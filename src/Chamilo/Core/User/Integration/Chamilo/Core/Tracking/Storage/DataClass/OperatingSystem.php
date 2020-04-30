<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class OperatingSystem extends User
{

    public function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $user_agent = $server['HTTP_USER_AGENT'];
        $operating_system = $this->extract_operating_system_from_useragent($user_agent);
        
        $this->set_type(self::TYPE_OPERATING_SYSTEM);
        $this->set_name($operating_system);
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable(self::TYPE_OPERATING_SYSTEM));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_TYPE),
            new StaticConditionVariable(self::TYPE_OPERATING_SYSTEM));
        return DataManager::retrieves(self::class, new DataClassRetrievesParameters($condition));
    }

    /**
     * Extracts an operating system from the useragent
     * 
     * @param User Agent $user_agent
     * @return string The operating system
     */
    public function extract_operating_system_from_useragent($user_agent)
    {
        // default values, if nothing corresponding found
        $viewable_operating_system = "Unknown";
        $list_operating_system = $this->load_operating_system();
        
        // search for corresponding pattern in $_SERVER['HTTP_USER_AGENT']
        // for operating system
        for ($i = 0; $i < count($list_operating_system); $i ++)
        {
            $pos = strpos($user_agent, $list_operating_system[$i][0]);
            if ($pos !== false)
            {
                $viewable_operating_system = $list_operating_system[$i][1];
            }
        }
        
        return $viewable_operating_system;
    }

    /**
     * Function used to list all the available operating systems with their names
     * 
     * @return array of operating_system
     */
    public function load_operating_system()
    {
        $buffer = explode(
            "#", 
            "Windows 95|Win 95#Windows_95|Win 95#Windows 98|Win 98#Windows NT|Win NT#Windows NT 5.0|Win 2000#Windows NT 5.1|Win XP#Windows 2000|Win 2000#Windows XP|Win XP#Windows ME|Win Me#Win95|Win 95#Win98|Win 98#WinNT|Win NT#linux-2.2|Linux 2#Linux|Linux#Linux 2|Linux 2#Macintosh|Mac#Mac_PPC|Mac#Mac_PowerPC|Mac#SunOS 5|SunOS 5#SunOS 6|SunOS 6#FreeBSD|FreeBSD#beOS|beOS#InternetSeer|InternetSeer#Googlebot|Googlebot#Teleport Pro|Teleport Pro");
        
        $list_operating_system = array();
        $i = 0;
        
        foreach ($buffer as $buffer1)
        {
            $data = explode('[|]', $buffer1);
            $list_operating_system[$i][0] = $data[0];
            $list_operating_system[$i][1] = $data[1];
            
            $i ++;
        }
        
        return $list_operating_system;
    }
}
