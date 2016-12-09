<?php
namespace Chamilo\Core\Metadata\Vocabulary;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Metadata\Vocabulary
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'vocabulary_action';
    const PARAM_VOCABULARY_ID = 'vocabulary_id';
    const PARAM_USER_ID = 'user_id';
    
    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_USER = 'User';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_DEFAULT = 'Default';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_BROWSE;

    /**
     *
     * @return integer
     */
    public function getSelectedElementId()
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID);
    }

    public function getSelectedUserId()
    {
        return $this->getRequest()->query->get(self::PARAM_USER_ID, 0);
    }
}
