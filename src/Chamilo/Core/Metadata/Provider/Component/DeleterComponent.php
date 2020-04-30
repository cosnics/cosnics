<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Core\Metadata\Storage\DataClass\ProviderLink;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

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
        
        $providerLinkIds = $this->getRequest()->get(self::PARAM_PROVIDER_LINK_ID);
        
        try
        {
            if (empty($providerLinkIds))
            {
                throw new NoObjectSelectedException(Translation::get('ProviderLink'));
            }
            
            if (! is_array($providerLinkIds))
            {
                $providerLinkIds = array($providerLinkIds);
            }
            
            foreach ($providerLinkIds as $providerLinkId)
            {
                $providerLink = DataManager::retrieve_by_id(ProviderLink::class, $providerLinkId);
                
                if (! $providerLink->delete())
                {
                    throw new Exception(
                        Translation::get(
                            'ObjectNotDeleted', 
                            array('OBJECT' => Translation::get('ProviderLink')), 
                            Utilities::COMMON_LIBRARIES));
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('ProviderLink')), 
                Utilities::COMMON_LIBRARIES);
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}