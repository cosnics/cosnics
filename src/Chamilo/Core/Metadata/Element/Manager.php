<?php
namespace Chamilo\Core\Metadata\Element;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Metadata\Element$Manager
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_MOVE = 'Mover';
    public const ACTION_UPDATE = 'Updater';
    public const ACTION_VOCABULARY = 'Vocabulary';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'element_action';
    public const PARAM_ELEMENT_ID = 'element_id';
    public const PARAM_MOVE = 'move';

    /**
     * @return int
     */
    public function getSchemaId()
    {
        return $this->getRequest()->query->get(\Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID);
    }
}
