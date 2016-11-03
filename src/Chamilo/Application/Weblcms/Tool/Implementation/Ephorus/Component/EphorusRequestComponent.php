<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core\DependencyContainer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Interfaces\RequestSupport;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class executes the ephorus submanager
 *
 * @author Tom Goethals - Hogeschool Gent
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EphorusRequestComponent extends Manager implements RequestSupport, DelegateComponent
{

    private $dependency_container;
    const DEPENDENCY_DATA_MANAGER_CLASS = 'repository_datamanager';
    const DEPENDENCY_REQUEST_CLASS = 'request';

    /**
     * Initializes this component
     *
     * @param $parent Application - The component in which this tool runs @codeCoverageIgnore
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent:: __construct($applicationConfiguration);

        $this->initialize_dependencies($this->get_dependency_container());
    }

    /**
     * Runs this component @codeCoverageIgnore
     */
    public function run()
    {
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $request = $this->getRequest();
            
            $requestAction = $request->get(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION
            );

            if(!isset($requestAction))
            {
                $request->query->set(
                    \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_CREATE
                );
            }

            $factory = new ApplicationFactory(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager:: context(),
                new ApplicationConfiguration($request, $this->get_user(), $this)
            );

            return $factory->run();
        }
        else
        {
            throw new NotAllowedException(false);
        }
    }

    /**
     * Initializes the dependencies
     *
     * @param DependencyContainer $dependency_container @codeCoverageIgnore
     */
    public function initialize_dependencies(DependencyContainer $dependency_container)
    {
        $dependency_container->add(
            self :: DEPENDENCY_DATA_MANAGER_CLASS, 'Chamilo\Core\Repository\Storage\DataManager'
        );
        $dependency_container->add(self :: DEPENDENCY_REQUEST_CLASS, 'Chamilo\Libraries\Platform\Session\Request');
    }

    public function get_base_request()
    {
        $requests = $this->get_base_requests();

        return $requests[0];
    }

    public function get_request_guids()
    {
    }

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     */
    public function get_base_requests()
    {
        $content_object_translation = Translation:: get(
            'ContentObject',
            null,
            \Chamilo\Core\Repository\Manager:: context()
        );

        $request_class = $this->get_request_class();

        $ids = $request_class:: get(self :: PARAM_CONTENT_OBJECT_IDS);

        if (!$ids)
        {
            throw new NoObjectSelectedException($content_object_translation);
        }
        $ids = (array) $ids;

        $data_manager_class = $this->get_data_manager_class();

        $requests = array();
        foreach ($ids as $id)
        {
            $content_object = $data_manager_class:: retrieve_by_id(ContentObject:: class_name(), $id);

            if (!$content_object)
            {
                throw new ObjectNotExistException($content_object_translation, $id);
            }

            $request = new Request();
            $request->set_process_type(Request :: PROCESS_TYPE_CHECK_AND_INVISIBLE);
            $request->set_course_id($this->get_course_id());
            $request->set_content_object_id($id);
            $request->set_author_id($content_object->get_owner_id());
            $request->set_request_user_id($this->get_user_id());
            $requests[] = $request;
        }

        return $requests;
    }

    /**
     * Redirects after create
     *
     * @param string $message
     * @param boolean $is_error @codeCoverageIgnore
     */
    public function redirect_after_create($message, $is_error)
    {
        $parameters = array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_BROWSE);
        $this->redirect($message, $is_error, $parameters);
    }

    /**
     * **************************************************************************************************************
     * Dependency container *
     * **************************************************************************************************************
     */

    /*
     * Gets the dependency container for this class
     */
    public function get_dependency_container()
    {
        if ($this->dependency_container == null)
        {
            $this->dependency_container = new DependencyContainer();
        }

        return $this->dependency_container;
    }

    /*
     * Sets the dependency container for this class @codeCoverageIgnore
     */
    public function set_dependency_container($container)
    {
        $this->dependency_container = $container;
    }

    /**
     * **************************************************************************************************************
     * Dependency properties *
     * **************************************************************************************************************
     */

    /*
     * Gets the data manager class dependency
     */
    public function get_data_manager_class()
    {
        return $this->get_dependency_container()->get(self :: DEPENDENCY_DATA_MANAGER_CLASS);
    }

    /*
     * Sets the data manager class dependency
     */
    public function set_data_manager_class($class)
    {
        $this->get_dependency_container()->add(self :: DEPENDENCY_DATA_MANAGER_CLASS, $class);
    }

    /*
     * Gets the request class dependency
     */
    public function get_request_class()
    {
        return $this->get_dependency_container()->get(self :: DEPENDENCY_REQUEST_CLASS);
    }

    /*
     * Sets the request class dependency
     */
    public function set_request_class($class)
    {
        $this->get_dependency_container()->add(self :: DEPENDENCY_REQUEST_CLASS, $class);
    }
}
