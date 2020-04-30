<?php
namespace Chamilo\Core\Metadata\Schema;

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
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_DELETE = 'Deleter';
    const ACTION_ELEMENT = 'Element';
    const ACTION_UPDATE = 'Updater';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_ACTION = 'schema_action';
    const PARAM_SCHEMA_ID = 'schema_id';

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
