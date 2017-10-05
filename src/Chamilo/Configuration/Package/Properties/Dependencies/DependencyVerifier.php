<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: package_dependency_verifier.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 *
 * @package admin.lib.package_installer
 */
class DependencyVerifier
{

    /**
     *
     * @var \configuration\package\storage\data_class\Package
     */
    private $package;

    protected $logger;
    const TYPE_REMOVE = 'remove';
    const TYPE_UPDATE = 'update';

    /**
     *
     * @param \configuration\package\storage\data_class\Package $package
     */
    public function __construct($package)
    {
        $this->package = $package;
        $this->logger = MessageLogger::getInstance($this);
    }

    /**
     *
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package
     */
    public function get_package()
    {
        return $this->package;
    }

    public function get_logger()
    {
        return $this->logger;
    }

    public function is_installable()
    {
        $dependencies = $this->get_package()->get_dependencies();

        if (is_null($dependencies))
        {
            return true;
        }

        if (! $dependencies->check())
        {
            $this->logger->add_message($dependencies->get_logger()->render());
            return false;
        }
        else
        {
            $this->logger->add_message($dependencies->get_logger()->render());
        }

        return true;
    }

    public function is_removable()
    {
        $condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Registration::class_name(), Registration::PROPERTY_CONTEXT),
                new StaticConditionVariable($this->get_package()->get_context())));

        $registrations = DataManager::retrieves(
            Registration::class_name(),
            new DataClassRetrievesParameters($condition));

        while ($registration = $registrations->next_result())
        {
            $package = Package::get($registration->get_context());
            $dependencies = $package->get_dependencies();

            if (! is_null($dependencies) && $dependencies->needs($this->get_package()->get_context()))
            {
                return false;
            }
        }

        return true;
    }
}
