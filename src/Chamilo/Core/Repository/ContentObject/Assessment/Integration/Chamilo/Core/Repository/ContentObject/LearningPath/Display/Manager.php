<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    public const ACTION_BUILDER = 'Builder';
    public const ACTION_CONFIGURE = 'Configurer';
    public const ACTION_MASTERY = 'Mastery';

    public const DEFAULT_ACTION = self::ACTION_MASTERY;
    public const PARAM_ACTION = 'type_action';

    /**
     *
     * @return TreeNode
     */
    public function getCurrentTreeNode()
    {
        return $this->get_application()->getCurrentTreeNode();
    }

    public function get_node_tabs(ButtonGroup $primaryActions, ButtonGroup $secondaryActions, TreeNode $node)
    {
        if ($this->get_parent()->canEditTreeNode($node))
        {
            $splitDropDownButton = new SplitDropdownButton(
                Translation::get('BuilderComponent'), new FontAwesomeGlyph('cubes'), $this->get_url(
                array(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $node->getContentObject(
                    )->getId(),
                    self::PARAM_ACTION => self::ACTION_BUILDER
                )
            )
            );

            $splitDropDownButton->addSubButton(
                new SubButton(
                    Translation::get('SetMasteryScore'), new FontAwesomeGlyph('signal'), $this->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $node->getContentObject(
                        )->getId(),
                        self::PARAM_ACTION => self::ACTION_MASTERY
                    )
                )
                )
            );

            $splitDropDownButton->addSubButton(
                new SubButton(
                    Translation::get('ConfigureAssessment'), new FontAwesomeGlyph('wrench'), $this->get_url(
                    array(
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_TYPE_SPECIFIC,
                        \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CONTENT_OBJECT_ID => $node->getContentObject(
                        )->getId(),
                        self::PARAM_ACTION => self::ACTION_CONFIGURE
                    )
                )
                )
            );

            $secondaryActions->addButton($splitDropDownButton);
        }
    }
}
