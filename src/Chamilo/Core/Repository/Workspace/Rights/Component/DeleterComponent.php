<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
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
        $rightsService = RightsService::getInstance();
        $workspace = $this->getCurrentWorkspace();

        if(!$rightsService->canManageWorkspace($this->getUser(), $workspace))
        {
            throw new NotAllowedException();
        }

        $entityRelationIdentifiers = $this->getRequest()->get(self::PARAM_ENTITY_RELATION_ID);
        
        try
        {
            if (empty($entityRelationIdentifiers))
            {
                throw new NoObjectSelectedException(Translation::get('WorkspaceEntityRelation'));
            }
            
            if (! is_array($entityRelationIdentifiers))
            {
                $entityRelationIdentifiers = array($entityRelationIdentifiers);
            }
            
            $entityRelationService = new EntityRelationService(new EntityRelationRepository());
            
            $rightsService = RightsService::getInstance();
            
            foreach ($entityRelationIdentifiers as $entityRelationIdentifier)
            {
                $entityRelation = $entityRelationService->getEntityRelationByIdentifier($entityRelationIdentifier);
                
                if ($rightsService->hasWorkspaceImplementationCreatorRights(
                    $this->get_user(), 
                    $this->getCurrentWorkspace()))
                {
                    if (! $entityRelationService->deleteEntityRelation($entityRelation))
                    {
                        throw new \Exception(
                            Translation::get(
                                'ObjectNotDeleted', 
                                array('OBJECT' => Translation::get('Workspace')), 
                                Utilities::COMMON_LIBRARIES));
                    }
                }
            }
            
            $success = true;
            $message = Translation::get(
                'ObjectDeleted', 
                array('OBJECT' => Translation::get('Workspace')), 
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
