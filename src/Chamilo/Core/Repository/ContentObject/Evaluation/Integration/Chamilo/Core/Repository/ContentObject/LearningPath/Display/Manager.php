<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

abstract class Manager extends Application implements DelegateComponent
{
    // Parameters
    const PARAM_ACTION = 'type_action';

    // Actions
    const ACTION_CONFIGURE = 'Configurer';

    // Default action
    const DEFAULT_ACTION = self::ACTION_CONFIGURE;

    public function get_node_tabs(ButtonGroup $primaryActions, ButtonGroup $secondaryActions, TreeNode $node)
    {
        if ($this->get_parent()->canEditTreeNode($node))
        {
            $secondaryActions->addButton(
                new Button(
                    $this->getTranslator()->trans('Configure', [], 'Chamilo\Core\Repository\ContentObject\Evaluation\Integration\Chamilo\Core\Repository\ContentObject\LearningPath'),
                    new FontAwesomeGlyph('wrench'),
                    $this->get_url(
                        array(
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $node->getContentObject()->getId(),
                            self::PARAM_ACTION => self::ACTION_CONFIGURE
                        )
                    )
                )
            );
        }
    }

    /**
     *
     * @return TreeNode
     */
    public function getCurrentTreeNode()
    {
        return $this->get_application()->getCurrentTreeNode();
    }
}
