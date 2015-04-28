<?php
namespace Chamilo\Core\Metadata\Provider;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

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
    // Parameters
    const PARAM_ACTION = 'provider_action';
    const PARAM_PROVIDER_LINK_ID = 'provider_link_id';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     *
     * @var \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    private $entities;

    /**
     *
     * @return \Chamilo\Core\Metadata\Interfaces\EntityInterface[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     *
     * @param \Chamilo\Core\Metadata\Interfaces\EntityInterface[] $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function verifySetup()
    {
        if (count($this->getEntities()) == 0)
        {
            throw new \InvalidArgumentException(Translation :: get('VerifyEntitiesProviderLinkSetup'));
        }
    }
}
