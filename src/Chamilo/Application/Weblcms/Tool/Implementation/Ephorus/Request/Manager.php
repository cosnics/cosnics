<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request;

/**
 * User: Pieterjan Broekaert Date: 30/07/12 Time: 12:38
 */
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\EphorusRequest\EphorusRequestSupport;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Interfaces\RequestSupport;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;

abstract class Manager extends Application
{

    /**
     * **************************************************************************************************************
     * PARAMETERS *
     * **************************************************************************************************************
     */
    const PARAM_ACTION = 'request_action';
    const PARAM_REQUEST_ID = 'request_id';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';
    const ACTION_VIEW_RESULT = 'ResultViewer';
    const ACTION_EXPORT_RESULT = 'ResultExporter';
    const ACTION_CHANGE_INDEX_VISIBILITY = 'IndexVisibilityChanger';
    const DEFAULT_ACTION = self :: ACTION_VIEW_RESULT;

    /**
     * Constructor Check if the parent component inherits the required marker interface
     *
     * @param $parent
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (! $applicationConfiguration->getApplication() instanceof RequestSupport)
        {
            throw new \Exception(
                'Components that use the ephorus request submanager need to implement the EphorusRequestSupport');
        }

        parent :: __construct($applicationConfiguration);
    }

    /**
     * Returns a url for a given action
     *
     * @param $action string
     * @param $parameters string[] - Optional parameters
     * @return string
     */
    protected function get_action_url($action, $parameters = array())
    {
        $parameters[self :: PARAM_ACTION] = $action;

        return $this->get_url($parameters);
    }

    /**
     * Returns the parent component
     *
     * @return EphorusRequestSupport
     */
    public function get_parent()
    {
        return parent :: get_parent();
    }
}
