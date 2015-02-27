<?php
namespace Chamilo\Core\Install\Observer\Type;

use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;

/**
 * This class helps to notify all the observers at the same time
 */
class WebInterfaceInstaller implements InstallerObserver
{

    /**
     *
     * @var array list aof all the observers to be notified
     */
    private $observers;

    public function __construct()
    {
        $this->observers = array();
    }

    public function add_observer(InstallerObserver $observer)
    {
        $this->observers[] = $observer;
    }

    private function call_on_each_observer($method_name, $parameters = null)
    {
        if (is_null($parameters))
        {
            $parameters = array();
        }

        if (! is_array($parameters))
        {
            $parameters = array($parameters);
        }

        $html = array();

        foreach ($this->observers as $observer)
        {
            $html[] = call_user_func_array(array($observer, $method_name), $parameters);
        }

        return implode(PHP_EOL, $html);
    }

    public function before_install()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function before_preprod()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function preprod_config_file_written(StepResult $result)
    {
        return $this->call_on_each_observer(__FUNCTION__, $result);
    }

    public function preprod_db_created(StepResult $result)
    {
        return $this->call_on_each_observer(__FUNCTION__, $result);
    }

    public function after_preprod()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function before_filesystem_prepared()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function after_filesystem_prepared(StepResult $result)
    {
        return $this->call_on_each_observer(__FUNCTION__, $result);
    }

    public function before_packages_install()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function before_package_install($context)
    {
        return $this->call_on_each_observer(__FUNCTION__, $context);
    }

    public function after_package_install(StepResult $result)
    {
        return $this->call_on_each_observer(__FUNCTION__, $result);
    }

    public function after_packages_install()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }

    public function after_install()
    {
        return $this->call_on_each_observer(__FUNCTION__);
    }
}
