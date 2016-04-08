<?php
namespace Chamilo\Core\Metadata\Element;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Metadata\Element$Manager
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_ACTION = 'element_action';
    const PARAM_ELEMENT_ID = 'element_id';
    const PARAM_MOVE = 'move';
    
    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const ACTION_MOVE = 'Mover';
    const ACTION_VOCABULARY = 'Vocabulary';
    
    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @return integer
     */
    public function getSchemaId()
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Metadata\Schema\Manager :: PARAM_SCHEMA_ID);
    }
}
