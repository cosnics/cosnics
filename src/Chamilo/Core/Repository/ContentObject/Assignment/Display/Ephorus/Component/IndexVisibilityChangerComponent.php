<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core\DependencyContainer;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Interfaces\RequestSupport;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment\EntryRequestTable;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class executes the ephorus submanager
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class IndexVisibilityChangerComponent extends Manager implements RequestSupport
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
        parent::__construct($applicationConfiguration);

        $this->initialize_dependencies($this->get_dependency_container());
    }

    /**
     * Runs this component @codeCoverageIgnore
     */
    public function run()
    {
        $request = $this->getRequest();

        $requestAction = $request->get(
            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION
        );

        if (!isset($requestAction))
        {
            $request->query->set(
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::PARAM_ACTION,
                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::ACTION_CHANGE_INDEX_VISIBILITY
            );
        }

        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager::context(),
            new ApplicationConfiguration($request, $this->get_user(), $this)
        )->run();
    }

    /**
     * Initializes the dependencies
     *
     * @param DependencyContainer $dependency_container @codeCoverageIgnore
     */
    public function initialize_dependencies(DependencyContainer $dependency_container)
    {
        $dependency_container->add(
            self::DEPENDENCY_DATA_MANAGER_CLASS,
            'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager'
        );
        $dependency_container->add(self::DEPENDENCY_REQUEST_CLASS, 'Chamilo\Libraries\Platform\Session\Request');
    }

    /**
     * Returns the request guids whose visibilities should be changed
     *
     * @return array
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     */
    public function get_request_guids()
    {
        $assignmentTableIds = $this->getRequest()->getFromPostOrUrl(self::PARAM_ENTRY_ID);
        if (isset($assignmentTableIds))
        {
            return $this->get_request_guids_from_assignment_submissions($assignmentTableIds);
        }
    }

    public function get_request_guids_from_assignment_submissions($assignmentTableIds)
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_ID),
            $assignmentTableIds
        );

        $requests = $this->getDataProvider()->findAssignmentEntriesWithRequests(
            new RecordRetrievesParameters(null, $condition)
        );

        $request_guids = array();

        foreach ($requests as $request)
        {
            if ($request->get_optional_property(Request::PROPERTY_GUID))
            {
                $request_guids[$request->get_optional_property(Request::PROPERTY_GUID)] =
                    !$request->get_optional_property(
                        Request::PROPERTY_VISIBLE_IN_INDEX
                    );
            }
        }

        return $request_guids;
    }

    /**
     * Gets the id's for which the visibility should be toggled.
     *
     * @return array
     */
    public function get_ids()
    {
        return $this->getRequest()->get(self::PARAM_ENTRY_ID);
    }

    /**
     * Redirects after create
     *
     * @param string $message @codeCoverageIgnore
     */
    public function redirect_after_create($message, $is_error = false)
    {
        $parameters = array(Manager::PARAM_ACTION => self::ACTION_BROWSE);
        $this->redirect($message, $is_error, $parameters);
    }

    private function get_publication_id()
    {
        return \Chamilo\Libraries\Platform\Session\Request::get(
            \Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION
        );
    }

    public function get_base_requests()
    {
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
        return $this->get_dependency_container()->get(self::DEPENDENCY_DATA_MANAGER_CLASS);
    }

    /*
     * Sets the data manager class dependency
     */
    public function set_data_manager_class($class)
    {
        $this->get_dependency_container()->add(self::DEPENDENCY_DATA_MANAGER_CLASS, $class);
    }

    /*
     * Gets the request class dependency
     */
    public function get_request_class()
    {
        return $this->get_dependency_container()->get(self::DEPENDENCY_REQUEST_CLASS);
    }

    /*
     * Sets the request class dependency
     */
    public function set_request_class($class)
    {
        $this->get_dependency_container()->add(self::DEPENDENCY_REQUEST_CLASS, $class);
    }
}
