<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class Menu extends \Chamilo\Core\Repository\Display\Menu
{

    public function getCurrentNodeId()
    {
        return $this->getApplication()->get_current_step() - 1;
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     * @return string
     */
    protected function getItemIcon(ComplexContentObjectPathNode $node)
    {
        if ($this->getApplication()->get_parent()->is_allowed_to_view_content_object($node))
        {
            if ($node->is_completed())
            {
                return 'type_completed';
            }
            else
            {
                return parent::getItemIcon($node);
            }
        }
        else
        {
            return 'disabled type_disabled';
        }
    }

    /**
     *
     * @param ComplexContentObjectPathNode $node
     * @return boolean
     */
    protected function isSelectedItem(ComplexContentObjectPathNode $node)
    {
        return $this->getApplication()->get_action() != Manager::ACTION_REPORTING && parent::isSelectedItem($node);
    }

    /**
     *
     * @return string[]
     */
    protected function getExtraMenuItems()
    {
        $application = $this->getApplication();
        $extraMenuItems = array();

        $progressItem = array();
        $progressItem['text'] = Translation::get('Progress');
        $progressItem['href'] = $application->get_url(
            array(Manager::PARAM_ACTION => Manager::ACTION_REPORTING, Manager::PARAM_STEP => null));
        $progressItem['icon'] = 'type_statistics';

        if ($application->get_action() == Manager::ACTION_REPORTING && ! $application->is_current_step_set())
        {
            $progressItem['state'] = array('selected' => true);
        }

        $extraMenuItems[] = $progressItem;

        return $extraMenuItems;
    }
}
