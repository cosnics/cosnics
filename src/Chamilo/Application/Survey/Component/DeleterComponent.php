<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Repository\PublicationRepository;
use Chamilo\Application\Survey\Service\PublicationService;
use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Application\Survey\Component
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
        $publicationIdentifiers = $this->getRequest()->get(self::PARAM_PUBLICATION_ID);
        
        try
        {
            if (empty($publicationIdentifiers))
            {
                throw new NoObjectSelectedException(Translation::get('Publication'));
            }
            
            if (! is_array($publicationIdentifiers))
            {
                $publicationIdentifiers = array($publicationIdentifiers);
            }
            
            $publicationService = new PublicationService(new PublicationRepository());
            $rightsService = RightsService::getInstance();
            
            foreach ($publicationIdentifiers as $publicationIdentifier)
            {
                $publication = $publicationService->getPublicationByIdentifier($publicationIdentifier);
                
                if ($rightsService->hasPublicationCreatorRights($this->get_user(), $publication))
                {
                    if (! $publicationService->deletePublication($publication))
                    {
                        throw new \Exception(
                            Translation::get(
                                'ObjectNotDeleted', 
                                array('OBJECT' => Translation::get('Publication')), 
                                Utilities::COMMON_LIBRARIES));
                    }
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('Publication')), 
                Utilities::COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }
        
        $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
    }
}