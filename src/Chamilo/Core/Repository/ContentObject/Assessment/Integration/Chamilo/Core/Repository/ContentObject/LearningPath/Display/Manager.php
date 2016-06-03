<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Platform\Translation;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'type_action';

    // Actions
    const ACTION_MASTERY = 'Mastery';
    const ACTION_CONFIGURE = 'Configurer';

    // Default action
    const DEFAULT_ACTION = self::ACTION_MASTERY;

    public function get_node_tabs(ButtonGroup $primaryActions, ButtonGroup $secondaryActions,
        ComplexContentObjectPathNode $node)
    {
        $tabs = array();
        $current_content_object = $node->get_content_object();

        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object($node) &&
             RightsService::getInstance()->canEditContentObject($this->get_user(), $current_content_object))
        {
            $secondaryActions->addButton(
                new Button(
                    Translation::get('SetMasteryScore'),
                    new BootstrapGlyph('signal'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            self::PARAM_ACTION => self::ACTION_MASTERY))));

            $secondaryActions->addButton(
                new Button(
                    Translation::get('ConfigureAssessment'),
                    new BootstrapGlyph('wrench'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            self::PARAM_ACTION => self::ACTION_CONFIGURE))));

            $parameters = array();
            $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
            $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_BUILD_COMPLEX_CONTENT_OBJECT;
            $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $node->get_content_object()->get_id();
            $parameters[\Chamilo\Core\Repository\Component\BuilderComponent::PARAM_POPUP] = 1;

            $redirect = new Redirect($parameters);
            $url = $redirect->getUrl();

            $primaryActions->addButton(
                new Button(
                    Translation::get('BuilderComponent'),
                    new BootstrapGlyph('th-list'),
                    $url,
                    Button::DISPLAY_ICON_AND_LABEL,
                    false,
                    null,
                    '" onclick="javascript:openPopup(\'' . $url . '\'); return false;'));
        }
    }
}
