<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Interfaces\EphorusSupportInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    /** Dummy component to make the manager launchable */
    const ACTION_DEFAULT = 'Default';
    const DEFAULT_ACTION = self::ACTION_DEFAULT;

    const PARAM_ACTION = 'type_action';

    public function get_node_tabs(ButtonGroup $primaryActions, ButtonGroup $secondaryActions, TreeNode $node)
    {
        if ($this->get_parent()->canEditTreeNode($node))
        {
            $parentApplication = $this->get_application()->get_application();

            if ($parentApplication instanceof EphorusSupportInterface)
            {
                $button = new Button(
                    Translation::get('Ephorus'),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Ephorus', 'Logo/16'
                    ),
                    $parentApplication->getAssignmentEphorusURL($this->getCurrentTreeNode()),
                    Button::DISPLAY_ICON_AND_LABEL, false, null, '_blank'
                );

                $secondaryActions->addButton($button);
            }
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
