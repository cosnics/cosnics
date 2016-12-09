<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass;

use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\User;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class Browser extends User
{

    public function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $user_agent = $server['HTTP_USER_AGENT'];
        $browser = $this->extract_browser_from_useragent($user_agent);
        
        $this->set_type(self::TYPE_BROWSER);
        $this->set_name($browser);
    }

    public function empty_tracker($event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_BROWSER));
        return $this->remove($condition);
    }

    public function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_TYPE), 
            new StaticConditionVariable(self::TYPE_BROWSER));
        return DataManager::retrieves(self::class_name(), new DataClassRetrievesParameters($condition));
    }

    /**
     * Extracts a browser from the useragent
     * 
     * @param User Agent $user_agent
     * @return string The browser
     */
    public function extract_browser_from_useragent($user_agent)
    {
        // default values, if nothing corresponding found
        $viewable_browser = "Unknown";
        $list_browsers = $this->load_browser();
        
        // search for corresponding pattern in $_SERVER['HTTP_USER_AGENT']
        // for browser
        for ($i = 0; $i < count($list_browsers); $i ++)
        {
            $pos = strpos($user_agent, $list_browsers[$i][0]);
            if ($pos !== false)
            {
                $viewable_browser = $list_browsers[$i][1];
            }
        }
        
        return $viewable_browser;
    }

    /**
     * Function used to list all the available browser with their names
     * 
     * @return array of browsers
     */
    public function load_browser()
    {
        $buffer = explode(
            "#", 
            "Gecko|Gecko#Mozilla/3|Mozilla 3.x#Mozilla/4.0|Mozilla 4.0x#Mozilla/4.5|Mozilla 4.5x#Mozilla/4.6|Mozilla 4.6x#Mozilla/4.7|Mozilla 4.7x#Mozilla/5.0|Mozilla 5.0x#MSIE 1.2|MSIE 1.2#MSIE 3.01|MSIE 3.x#MSIE 3.02|MSIE 3.x#MSIE 4.0|MSIE 4.x#MSIE 4.01|MSIE 4.x#MSIE 4.5|MSIE 4.5#MSIE 5.0b1|MSIE 5.0x#MSIE 5.0b2|MSIE 5.0x#MSIE 5.0|MSIE 5.0x#MSIE 5.01|MSIE 5.0x#MSIE 5.1|MSIE 5.1#MSIE 5.1b1|MSIE 5.1#MSIE 5.5|MSIE 5.5#MSIE 5.5b1|MSIE 5.5#MSIE 5.5b2|MSIE 5.5#MSIE 6.0|MSIE 6#MSIE 6.0b|MSIE 6#MSIE 6.5a|MSIE 6.5#Lynx/2.8.0|Lynx 2#Lynx/2.8.1|Lynx 2#Lynx/2.8.2|Lynx 2#Lynx/2.8.3|Lynx 2#Lynx/2.8.4|Lynx 2#Lynx/2.8.5|Lynx 2#HTTrack 3.0x|HTTrack#OmniWeb/4.0.1|OmniWeb#Opera 3.60|Opera 3.60#Opera 4.0|Opera 4#Opera 4.01|Opera 4#Opera 4.02|Opera 4#Opera 5|Opera 5#Opera/3.60|Opera 3.60#Opera/4|Opera 4#Opera/5|Opera 5#Opera/6|Opera 6#Opera 6|Opera 6#Netscape6|NS 6#Netscape/6|NS 6#Netscape7|NS 7#Netscape/7|NS 7#Konqueror/2.0|Konqueror 2#Konqueror/2.0.1|Konqueror 2#Konqueror/2.1|Konqueror 2#Konqueror/2.1.1|Konqueror 2#Konqueror/2.1.2|Konqueror 2#Konqueror/2.2|Konqueror 2#Teleport Pro|Teleport Pro#WebStripper|WebStripper#WebZIP|WebZIP#Netcraft Web|NetCraft#Googlebot|Googlebot#WebCrawler|WebCrawler#InternetSeer|InternetSeer#ia_archiver|ia archiver");
        
        // $list_browser[x][0] is the name of browser as in $_SERVER['HTTP_USER_AGENT']
        // $list_browser[x][1] is the name of browser that will be used in display and tables
        $i = 0;
        foreach ($buffer as $buffer1)
        {
            $list_browsers[] = explode('[|]', $buffer1);
        }
        return $list_browsers;
    }
}
