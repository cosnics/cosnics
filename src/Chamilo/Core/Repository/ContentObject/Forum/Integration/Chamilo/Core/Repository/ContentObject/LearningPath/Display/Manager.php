<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Translation;

abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'type_action';

    // Actions
    const ACTION_SUBSCRIBE = 'Subscribe';
    const ACTION_UNSUBSCRIBE = 'Unsubscribe';

    // Default action
    const DEFAULT_ACTION = self::ACTION_SUBSCRIBE;

    public function get_node_tabs(
        ButtonGroup $primaryActions, ButtonGroup $secondaryActions,
        ComplexContentObjectPathNode $node
    )
    {
        if ($this->get_parent()->canEditComplexContentObjectPathNode($node))
        {
            $secondaryActions->addButton(
                new Button(
                    Translation::get('Subscribe'),
                    new BootstrapGlyph('envelope'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID =>
                                $this->get_application()->get_current_content_object()->getId(),
                            self::PARAM_ACTION => self::ACTION_SUBSCRIBE
                        )
                    )
                )
            );

            $secondaryActions->addButton(
                new Button(
                    Translation::get('Unsubscribe'),
                    new BootstrapGlyph('envelope-o'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID =>
                                $this->get_application()->get_current_content_object()->getId(),
                            self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE
                        )
                    )
                )
            );
        }
    }
}
