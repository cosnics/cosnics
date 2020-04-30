<?php
namespace Chamilo\Core\Metadata\Provider;

use Chamilo\Core\Metadata\Service\EntityConditionService;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;
use InvalidArgumentException;

/**
 *
 * @package Chamilo\Core\Metadata\Provider
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends Application
{
    const ACTION_BROWSE = 'Browser';
    const ACTION_CONFIGURE = 'Configurer';
    const ACTION_DELETE = 'Deleter';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_ACTION = 'provider_action';
    const PARAM_ENTITY_TYPE = 'entity_type';
    const PARAM_PROVIDER_LINK_ID = 'provider_link_id';

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    private $entities;

    /**
     *
     * @var \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    private $expandedEntities;

    /**
     *
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Entity\EntityInterface[] $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\EntityConditionService
     */
    public function getEntityConditionService()
    {
        return $this->getService(EntityConditionService::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface[]
     * @throws \Exception
     */
    public function getExpandedEntities()
    {
        if (!isset($this->expandedEntities))
        {
            $this->expandedEntities = $this->getEntityConditionService()->expandEntities($this->getEntities());
        }

        return $this->expandedEntities;
    }

    public function verifySetup()
    {
        if (count($this->getEntities()) == 0)
        {
            throw new InvalidArgumentException(Translation::get('VerifyEntitiesProviderLinkSetup'));
        }
    }
}
