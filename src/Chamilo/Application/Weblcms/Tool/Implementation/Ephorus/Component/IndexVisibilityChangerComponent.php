<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Core\DependencyContainer;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class executes the ephorus submanager
 *
 * @author Tom Goethals - Hogeschool Gent
 */
class IndexVisibilityChangerComponent extends Manager
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
        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $document_guids = $this->get_request_guids();

            $requestManager = $this->getRequestManager();
            $failures = $requestManager->changeVisibilityOfDocumentsOnIndex($document_guids);

            $message = $this->get_result(
                $failures,
                count($document_guids),
                'VisibilityNotChanged',
                'VisibilityNotChanged',
                'VisibilityChanged',
                'VisibilityChanged'
            );

            $this->redirect_after_create($message);
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
        $request_translation = Translation::get('Request', null, \Chamilo\Core\Repository\Manager::context());

        $ids = $this->get_ids();

        if (!$ids)
        {
            throw new NoObjectSelectedException($request_translation);
        }
        $ids = (array) $ids;

        $data_manager_class = $this->get_data_manager_class();

        $request_guids = array();
        foreach ($ids as $id)
        {
            $request = $data_manager_class::retrieve_request_by_id($id);

            if (!$request)
            {
                throw new ObjectNotExistException($request_translation, $id);
            }

            $request_guids[$request->get_guid()] = !$request->is_visible_in_index();
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
        return $this->getRequest()->get(self::PARAM_REQUEST_IDS);
    }

    /**
     * Redirects after create
     *
     * @param string $message @codeCoverageIgnore
     */
    public function redirect_after_create($message, $is_error = false)
    {
        $parameters = array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE);
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

    public function get_additional_parameters()
    {
        return array(self::PARAM_PUBLICATION_ID);
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
