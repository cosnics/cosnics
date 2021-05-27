<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependencies;
use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Exception;

class Sequencer
{

    private $packages;

    private $packages_exist;

    /**
     *
     * @var string[]
     */
    private $package_contexts;

    private $sequence;

    private $unprocessed_package_contexts;

    /**
     *
     * @param string[] $package_contexts
     */
    public function __construct($package_contexts)
    {
        if (! is_array($package_contexts))
        {
            $package_contexts = array($package_contexts);
        }

        $this->package_contexts = $package_contexts;
        $this->sequence = [];
        $this->unprocessed_package_contexts = [];
    }

    public function run()
    {
        $this->expand_package_contexts();
        $this->order_package_contexts();
        return $this->sequence;
    }

    public function get_package($package_context)
    {
        if (! isset($this->packages[$package_context]))
        {
            try
            {
                $this->packages[$package_context] = Package::get($package_context);
            }
            catch (Exception $exception)
            {
                $this->packages[$package_context] = false;
            }
        }

        return $this->packages[$package_context];
    }

    public function is_package($package_context)
    {
        if (! isset($this->packages_exist[$package_context]))
        {
            $this->packages_exist[$package_context] = Package::exists($package_context);
        }

        return $this->packages_exist[$package_context];
    }

    public function expand_package_contexts()
    {
        foreach ($this->package_contexts as $package_context)
        {
            $this->check_additional_packages($package_context);
        }

        foreach ($this->package_contexts as $package_context)
        {
            $this->check_dependencies($package_context);
        }

        foreach ($this->package_contexts as $package_context)
        {
            $this->check_integrations($package_context);
        }

        $this->unprocessed_package_contexts = $this->package_contexts;
    }

    /**
     * Check and process the package context for additional packages
     *
     * @param string $package_context
     */
    public function check_additional_packages($package_context)
    {
        $class = $package_context . '\Package\Installer';
        $additional_packages = $class::get_additional_packages();

        foreach ($additional_packages as $additional_package)
        {
            if (! in_array($additional_package, $this->package_contexts))
            {
                $this->package_contexts[] = $additional_package;
                $this->check_additional_packages($additional_package);
                $this->check_dependencies($additional_package);
                $this->check_integrations($additional_package);
            }
        }
    }

    /**
     * Process the dependencies of the package context
     *
     * @param string $package_context
     */
    public function check_dependencies($package_context)
    {
        $this->process_dependency($this->get_package($package_context)->get_dependencies());
    }

    /**
     * @param \Chamilo\Configuration\Package\Properties\Dependencies\Dependencies|null $dependencies
     */
    public function process_dependency(Dependencies $dependencies = null)
    {
        if ($dependencies instanceof Dependencies)
        {
            foreach ($dependencies->get_dependencies() as $dependency)
            {
                if (! in_array($dependency->get_id(), $this->package_contexts))
                {
                    $this->package_contexts[] = $dependency->get_id();
                    $this->check_additional_packages($dependency->get_id());
                    $this->check_dependencies($dependency->get_id());
                    $this->check_integrations($dependency->get_id());
                }
            }
        }
    }

    /**
     * Process any and every kind of integration the target context might trigger in existing packages
     *
     * @param string $target_context
     */
    public function check_integrations($target_context)
    {
        foreach ($this->package_contexts as $source_context)
        {
            $integration_context = $source_context . '\Integration\\' . $target_context;
            $package = $this->is_package($integration_context);

            if ($package)
            {
                if (! in_array($integration_context, $this->package_contexts))
                {
                    $this->package_contexts[] = $integration_context;
                    $this->check_integrations($integration_context);
                    $this->check_additional_packages($integration_context);
                    $this->check_dependencies($integration_context);
                }
            }
        }
    }

    /**
     * Order the list of (in)directly selected packages so that they can succesfully be installed
     */
    public function order_package_contexts()
    {
        while (($unprocessed_package_context = $this->get_next_unprocessed_package_context()) != null)
        {

            if ($this->verify_dependency($this->get_package($unprocessed_package_context)->get_dependencies()))
            {
                $this->sequence[] = $unprocessed_package_context;
            }
            else
            {
                $this->add_unprocessed_package_context($unprocessed_package_context);
            }
        }
    }

    /**
     *
     * @return string
     */
    public function get_next_unprocessed_package_context()
    {
        return array_shift($this->unprocessed_package_contexts);
    }

    /**
     *
     * @param string $context
     */
    public function add_unprocessed_package_context($unprocessed_package_context)
    {
        array_push($this->unprocessed_package_contexts, $unprocessed_package_context);
    }

    /**
     *
     * @param Dependencies|Dependency $dependency
     * @return boolean
     */
    public function verify_dependency($dependency)
    {
        if ($dependency instanceof Dependencies)
        {
            $result = true;

            foreach ($dependency->get_dependencies() as $sub_dependency)
            {
                $result = $result && $this->verify_dependency($sub_dependency);
            }

            return $result;
        }
        elseif ($dependency instanceof Dependency)
        {
            if (! in_array($dependency->get_id(), $this->sequence))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }
}