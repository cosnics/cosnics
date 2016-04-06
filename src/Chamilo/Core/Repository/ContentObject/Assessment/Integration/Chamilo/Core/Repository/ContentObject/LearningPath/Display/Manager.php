<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'type_action';

    // Actions
    const ACTION_MASTERY = 'Mastery';
    const ACTION_CONFIGURE = 'Configurer';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_MASTERY;

    public function get_node_tabs(ComplexContentObjectPathNode $node)
    {
        $tabs = array();
        $current_content_object = $node->get_content_object();

        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object($node) &&
             RightsService :: getInstance()->canEditContentObject($this->get_user(), $current_content_object))
        {
            // TODO: This used to be $this->get_action_parameter, self :: PARAM_ACTION seemed more logical but is
            // probably
            // wrong, verify once executable
            $action = Request :: get(self :: PARAM_ACTION);

            $is_selected = $this->get_application()->get_action() ==
                 \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: ACTION_TYPE_SPECIFIC &&
                 $action == self :: ACTION_MASTERY;
            $tabs[] = new DynamicVisualTab(
                self :: ACTION_MASTERY,
                Translation :: get('SetMasteryScore'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_MASTERY),
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: ACTION_TYPE_SPECIFIC,
                        self :: PARAM_ACTION => self :: ACTION_MASTERY)),
                $is_selected,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED);

            $is_selected = $this->get_application()->get_action() ==
                 \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: ACTION_TYPE_SPECIFIC &&
                 $action == self :: ACTION_CONFIGURE;
            $tabs[] = new DynamicVisualTab(
                self :: ACTION_CONFIGURE,
                Translation :: get('ConfigureAssessment'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/' . self :: ACTION_CONFIGURE),
                $this->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager :: ACTION_TYPE_SPECIFIC,
                        self :: PARAM_ACTION => self :: ACTION_CONFIGURE)),
                $is_selected,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED);

            $parameters = array();
            $parameters[Application :: PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager :: context();
            $parameters[Application :: PARAM_ACTION] = \Chamilo\Core\Repository\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT;
            $parameters[\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID] = $node->get_content_object()->get_id();
            $parameters[\Chamilo\Core\Repository\Component\BuilderComponent :: PARAM_POPUP] = 1;

            $redirect = new Redirect($parameters);
            $url = $redirect->getUrl();

            $tabs[] = new DynamicVisualTab(
                'builder',
                Translation :: get('BuilderComponent'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Tab/Builder'),
                $url,
                false,
                false,
                DynamicVisualTab :: POSITION_LEFT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED,
                DynamicVisualTab :: TARGET_POPUP);
        }

        return $tabs;
    }
}
