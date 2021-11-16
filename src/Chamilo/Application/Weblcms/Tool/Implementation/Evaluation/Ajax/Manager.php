<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax;

use Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Component\AjaxComponent;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Ajax
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_PROCESS_CURIOS_CSV = 'ProcessCuriosCSV';
    const ACTION_IMPORT = 'Import';

    const PARAM_ACTION = 'evaluation_display_ajax_action';

    /**
     * @var AjaxComponent
     */
    protected $ajaxComponent;

    /**
     * Manager constructor.
     *
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if (!$applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'The ajax components from the evaluation display manager can only be called from ' .
                'within the AjaxComponent of the evaluation display application'
            );
        }

        $this->ajaxComponent = $applicationConfiguration->getApplication();

        parent::__construct($applicationConfiguration);
    }

    /**
     * @return array
     */
    protected function getCourseUsers(): array
    {
        $order_property = array(
            new OrderBy(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME)));
        $courseUsers = \Chamilo\Application\Weblcms\Course\Storage\DataManager::retrieve_all_course_users(
            $this->ajaxComponent->get_course_id(), null, null, null, $order_property);
        return $courseUsers->as_array();
    }
}
