<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;

/**
 *
 * @package Chamilo\Libraries\Architecture\Application
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ApplicationFactory
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface
     */
    private $applicationConfiguration;

    /**
     *
     * @var string
     */
    private $context;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $context
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct($context, ApplicationConfigurationInterface $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->context = $context;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface
     */
    public function getApplicationConfiguration()
    {
        return $this->applicationConfiguration;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getApplicationConfiguration()->getRequest();
    }

    /**
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->getApplicationConfiguration()->getApplication();
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUser()
    {
        return $this->getApplicationConfiguration()->getUser();
    }

    /**
     * Constructs the application and runs it
     */
    public function run()
    {
        return $this->getComponent()->run();
    }

    /**
     *
     * @throws \Exception
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getComponent()
    {
        $actionParameter = $this->getActionParameter();
        $action = $this->getAction($actionParameter);
        $component = $this->createComponent($action);

        if (! $component instanceof NoContextComponent)
        {
            $breadcrumbGenerator = $component->get_breadcrumb_generator();
            $breadcrumbGenerator->generate_breadcrumbs();
        }

        return $component;
    }

    /**
     *
     * @throws \Exception
     * @return string
     */
    private function getManagerClass()
    {
        $managerClass = $this->getContext() . '\Manager';

        if (! class_exists($managerClass))
        {
            throw new \Exception(Translation :: get('NoManagerFound', array('CONTEXT' => $this->getContext())));
        }

        return $managerClass;
    }

    /**
     *
     * @return string
     */
    private function getActionParameter()
    {
        $managerClass = $this->getManagerClass();
        return $managerClass :: PARAM_ACTION;
    }

    /**
     *
     * @param string $action
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    private function createComponent($action)
    {
        $class = $this->getClassName($action);

        $component = new $class($this->getApplicationConfiguration());

        $component->set_parameter($this->getActionParameter(), $action);

        if (! $this->getApplication() instanceof Application)
        {
            $component->set_parameter(Application :: PARAM_CONTEXT, $this->getContext());
        }

        $parameters = $component->get_additional_parameters();

        foreach ($parameters as $parameter)
        {
            $component->set_parameter($parameter, $this->getRequest()->query->get($parameter));
        }

        return $component;
    }

    /**
     *
     * @param string $actionParameter
     * @return string
     */
    private function getAction($actionParameter)
    {
        $managerClass = $this->getManagerClass();
        $level = $this->determineLevel();
        $actions = $this->getRequestedAction($actionParameter);

        if (is_array($actions))
        {
            if (isset($actions[$level]))
            {
                $action = $actions[$level];
            }
            else
            {
                // TODO: Catch the fact that there might not be a default action
                $action = $managerClass :: DEFAULT_ACTION;
            }
        }
        else
        {
            $action = $this->getRequestedAction($actionParameter);
        }

        $tableAction = $this->processTableAction($actionParameter);

        if ($tableAction)
        {
            $action = $tableAction;
        }

        return $action;
    }

    /**
     *
     * @return integer
     */
    private function determineLevel()
    {
        if ($this->getApplication() instanceof Application)
        {
            $level = $this->getApplication()->get_level();
            $level ++;
        }
        else
        {

            $level = 0;
        }

        return $level;
    }

    /**
     *
     * @param string $actionParameter
     * @param string $defaultAction
     * @return string
     */
    private function getRequestedAction($actionParameter)
    {
        $getAction = $this->getRequest()->query->get($actionParameter);

        if (! $getAction)
        {
            $postAction = $this->getRequest()->request->get($actionParameter);

            if (! $postAction)
            {
                // TODO: Catch the fact that there might not be a default action
                $managerClass = $this->getManagerClass();
                return $managerClass :: DEFAULT_ACTION;
            }
            else
            {
                return $postAction;
            }
        }
        else
        {
            return $getAction;
        }
    }

    /**
     *
     * @param string $action
     * @return string
     */
    private function getClassName($action)
    {
        $classname = $this->getContext() . '\Component\\' . $action . 'Component';

        if (! class_exists($classname))
        {
            // TODO: Temporary fallback for backwards compatibility
            $classname = $this->getContext() . '\Component\\' .
                 (string) StringUtilities :: getInstance()->createString($action)->upperCamelize() . 'Component';

            if (! class_exists($classname))
            {
                $trail = BreadcrumbTrail :: get_instance();
                $trail->add(new Breadcrumb('#', Translation :: get($classname)));

                throw new ClassNotExistException($classname);
            }
        }

        return $classname;
    }

    /**
     *
     * @return string
     */
    private function processTableAction($actionParameter)
    {
        $tableName = $this->getRequest()->request->get('table_name');

        if (isset($tableName))
        {
            $namespace = $this->getRequest()->request->get($tableName . '_namespace');
            $class = (string) StringUtilities :: getInstance()->createString($tableName)->upperCamelize();
            $classname = $namespace . '\\' . $class;

            if (class_exists($classname))
            {
                $ids = $classname :: get_selected_ids();

                $this->getRequest()->query->set($classname :: TABLE_IDENTIFIER, $ids);
                Request :: set_get($classname :: TABLE_IDENTIFIER, $ids);

                $tableParameters = unserialize(base64_decode(Request :: post($tableName . '_action_value')));

                foreach ($tableParameters as $parameter => $value)
                {
                    $this->getRequest()->query->set($parameter, $value);
                    Request :: set_get($parameter, $value);
                }

                if (array_key_exists($actionParameter, $tableParameters))
                {
                    return $tableParameters[$actionParameter];
                }
            }
        }

        return null;
    }
}
