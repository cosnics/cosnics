<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Component
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

        $providerLinkIds = Request :: get(self :: PARAM_PROVIDER_LINK_ID);

        try
        {
            if (empty($providerLinkIds))
            {
                throw new NoObjectSelectedException(Translation :: get('ProviderLink'));
            }

            if (! is_array($providerLinkIds))
            {
                $providerLinkIds = array($providerLinkIds);
            }

            foreach ($providerLinkIds as $providerLinkId)
            {
                $providerLink = DataManager :: retrieve_by_id(ProviderLink :: class_name(), $providerLinkId);

                if (! $providerLink->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted',
                            array('OBJECT' => Translation :: get('ProviderLink')),
                            Utilities :: COMMON_LIBRARIES));
                }
            }

            $success = true;
            $message = Translation :: get(
                'ObjectDeleted',
                array('OBJECT' => Translation :: get('ProviderLink')),
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