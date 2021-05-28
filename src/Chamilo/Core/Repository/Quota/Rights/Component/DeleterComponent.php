<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * @return void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserSetRightsForQuotaRequests($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $rightsLocationEntityRightGroups = $this->getRightsLocationEntityRightGroups();
        $failures = 0;

        foreach ($rightsLocationEntityRightGroups as $rightsLocationEntityRightGroup)
        {
            if (!$this->getRightsService()->deleteRightsLocationEntityRightGroup($rightsLocationEntityRightGroup))
            {
                $failures ++;
            }
        }

        $message = $this->get_result(
            $failures, count($rightsLocationEntityRightGroups), 'ObjectNotDeleted', 'ObjectsNotDeleted',
            'ObjectDeleted', 'ObjectsDeleted'
        );

        $this->redirect($message, (bool) $failures, array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE));
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup[]
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ParameterNotDefinedException
     */
    protected function getRightsLocationEntityRightGroups()
    {
        $identifiers = $this->getRequest()->query->get(self::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID);

        if (is_null($identifiers))
        {
            throw new ParameterNotDefinedException(self::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID);
        }

        if (!is_array($identifiers))
        {
            $identifiers = array($identifiers);
        }

        return $this->getRightsService()->findRightsLocationEntityRightGroupByIdentifiers($identifiers);
    }
}
