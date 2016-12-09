<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $relationInstanceIds = $this->getRequest()->get(self :: PARAM_RELATION_INSTANCE_ID);

        try
        {
            if (empty($relationInstanceIds))
            {
                throw new NoObjectSelectedException(Translation :: get('RelationInstance'));
            }

            if (! is_array($relationInstanceIds))
            {
                $relationInstanceIds = array($relationInstanceIds);
            }

            foreach ($relationInstanceIds as $relationInstanceId)
            {
                $relationInstance = DataManager :: retrieve_by_id(RelationInstance :: class_name(), $relationInstanceId);

                if (! $relationInstance->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted',
                            array('OBJECT' => Translation :: get('RelationInstance')),
                            Utilities :: COMMON_LIBRARIES));
                }
            }

            $success = true;
            $message = Translation :: get(
                'ObjectDeleted',
                array('OBJECT' => Translation :: get('RelationInstance')),
                Utilities :: COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }
}