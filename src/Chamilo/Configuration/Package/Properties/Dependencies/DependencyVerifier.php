<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Format\MessageLogger;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
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
        $this->logger = MessageLogger :: get_instance($this);
    }

    /**
     *
     * @return \configuration\package\storage\data_class\Package
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
        $dependencies = $this->get_package()->get_pre_depends();

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

    public function is_updatable()
    {
        return $this->check_reliabilities(self :: TYPE_UPDATE);
    }

    public function is_removable()
    {
        $condition = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_CONTEXT),
                new StaticConditionVariable($this->get_package()->get_context())));

        $registrations = DataManager :: retrieves(
            Registration :: class_name(),
            new DataClassRetrievesParameters($condition));

        while ($registration = $registrations->next_result())
        {
            $package = Package :: get($registration->get_context());
            $dependencies = $package->get_pre_depends();

            if (! is_null($dependencies) && $dependencies->needs($this->get_package()->get_context()))
            {
                return false;
            }
        }

        return true;
    }

    public function check_reliabilities($type)
    {
        $conditions = array();
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_TYPE),
                new StaticConditionVariable($this->get_package()->get_section())));
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_NAME),
                new StaticConditionVariable($this->get_package()->get_code())));
        $condition = new OrCondition($conditions);

        $registrations = DataManager :: retrieves(
            Registration :: class_name(),
            new DataClassRetrievesParameters($condition));

        $failures = 0;

        while ($registration = $registrations->next_result())
        {
            $package_data = Package :: get($registration->get_context());

            if ($package_data)
            {
                switch ($this->get_package()->get_section())
                {
                    case Registration :: TYPE_APPLICATION :
                        $dependency_type = Dependency :: TYPE_APPLICATIONS;
                        break;
                    case Registration :: TYPE_CONTENT_OBJECT :
                        $dependency_type = Dependency :: TYPE_CONTENT_OBJECTS;
                        break;
                    default :
                        return true;
                }

                $dependencies = $package_data->get_dependencies();

                if (isset($dependencies[$dependency_type]))
                {
                    foreach ($dependencies[$dependency_type]['dependency'] as $dependency)
                    {
                        if ($dependency['id'] === $this->get_package()->get_code())
                        {
                            if ($type == self :: TYPE_REMOVE)
                            {
                                $message = Translation :: get('PackageDependency') . ': <em>' . $package_data->get_name() .
                                     ' (' . $package_data->get_code() . ')</em>';
                                $this->logger->add_message($message);
                                $failures ++;
                            }
                            elseif ($type == self :: TYPE_UPDATE)
                            {
                                $package_dependency = Dependency :: factory($dependency_type, $dependency);
                                $result = Dependency :: version_compare(
                                    $package_dependency->get_operator(),
                                    $package_dependency->get_version_number(),
                                    $this->get_package()->get_version());
                                $message = '<em>' . $package_data->get_name() . ' (' . $package_data->get_code() . ') ' .
                                     $package_dependency->get_operator_name($package_dependency->get_operator()) . ' ' .
                                     $package_dependency->get_version_number() . '</em>';

                                if (! $result && $package_dependency->is_fatal())
                                {
                                    $failures ++;
                                    $this->logger->add_message($message, MessageLogger :: TYPE_ERROR);
                                }
                                elseif (! $result && ! $package_dependency->is_fatal())
                                {
                                    $this->logger->add_message($message, MessageLogger :: TYPE_WARNING);
                                }
                                else
                                {
                                    $this->logger->add_message($message);
                                }
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        if ($failures > 0)
        {
            $message = Translation :: get('VerificationFailed');
            $this->logger->add_message($message, MessageLogger :: TYPE_ERROR);
            return false;
        }
        else
        {
            $message = Translation :: get('VerificationSuccess');
            $this->logger->add_message($message, MessageLogger :: TYPE_CONFIRM);
            return true;
        }
    }
}
