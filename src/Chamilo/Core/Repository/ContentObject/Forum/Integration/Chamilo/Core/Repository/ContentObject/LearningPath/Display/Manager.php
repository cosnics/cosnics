<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    // Parameters
    const ACTION_SUBSCRIBE = 'Subscribe';

    // Actions

    const ACTION_UNSUBSCRIBE = 'Unsubscribe';

    const DEFAULT_ACTION = self::ACTION_SUBSCRIBE;

    // Default action

    const PARAM_ACTION = 'type_action';

    /**
     *
     * @param ButtonGroup $primaryActions
     * @param ButtonGroup $secondaryActions
     * @param TreeNode $node
     */
    public function get_node_tabs(ButtonGroup $primaryActions, ButtonGroup $secondaryActions, TreeNode $node)
    {
        $translator = Translation::getInstance();

        $contentObject = $node->getContentObject();

        $subscribed = DataManager::retrieve_subscribe(
            $contentObject->getId(), $this->getUser()->getId()
        );

        if (!$subscribed)
        {
            $secondaryActions->addButton(
                new Button(
                    $translator->getTranslation('Subscribe'), new FontAwesomeGlyph('envelope'), $this->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(
                        ),
                        self::PARAM_ACTION => self::ACTION_SUBSCRIBE
                    )
                )
                )
            );
        }
        else
        {
            $secondaryActions->addButton(
                new Button(
                    $translator->getTranslation('UnSubscribe'), new FontAwesomeGlyph('envelope', array(), null, 'far'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId(
                            ),
                            self::PARAM_ACTION => self::ACTION_UNSUBSCRIBE
                        )
                    )
                )
            );
        }
    }
}
