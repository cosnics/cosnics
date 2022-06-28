<?php
namespace Chamilo\Core\Metadata\Relation;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Metadata\Schema
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_ELEMENT = 'Element';
    public const ACTION_UPDATE = 'Updater';

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public const PARAM_ACTION = 'relation_action';
    public const PARAM_RELATION_ID = 'relation_id';

    /**
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     */
    public function getDataClassEntityFactory()
    {
        return $this->getService(DataClassEntityFactory::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityTranslationService
     */
    public function getEntityTranslationService()
    {
        return $this->getService(EntityTranslationService::class);
    }
}
