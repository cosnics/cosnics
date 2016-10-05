<?php
namespace Dropbox;

class Autoloader
{

    /**
     * The array mapping class names to paths
     * 
     * @var multitype:string
     */
    private static $map = array(
        'Dropbox\API' => '/API.php', 
        'Dropbox\Exception' => '/Exception.php', 
        'Dropbox\OAuth\Storage\Encrypter' => '/OAuth/Storage/Encrypter.php', 
        'Dropbox\OAuth\Storage\Session' => '/OAuth/Storage/Session.php', 
        'Dropbox\OAuth\Storage\StorageInterface' => '/OAuth/Storage/StorageInterface.php', 
        'Dropbox\OAuth\Consumer\ConsumerAbstract' => '/OAuth/Consumer/ConsumerAbstract.php', 
        'Dropbox\OAuth\Consumer\Curl' => '/OAuth/Consumer/Curl.php');

    /**
     * Try to load the class
     * 
     * @param $classname string
     * @return boolean
     */
    public static function load($classname)
    {
        if (isset(self :: $map[$classname]))
        {
            require_once __DIR__ . self :: $map[$classname];
            return true;
        }
        
        return false;
    }
}
