<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    public function run()
    {
        $entityRelation = $this->getCurrentEntityRelation();
        
        if (! $entityRelation instanceof WorkspaceEntityRelation)
        {
            throw new ObjectNotExistException(Translation::get('WorkspaceEntityRelation'));
        }
        
        $form = new RightsForm(
            $this->get_url(array(self::PARAM_ENTITY_RELATION_ID => $this->getCurrentEntityRelation()->getId())), 
            $entityRelation, 
            RightsForm::MODE_UPDATE);
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                
                $rightsService = RightsService::getInstance();
                
                $right = $rightsService->getAggregatedRight(
                    (int) $values[RightsForm::PROPERTY_VIEW], 
                    (int) $values[RightsForm::PROPERTY_USE], 
                    (int) $values[RightsForm::PROPERTY_COPY], 
                    (int) $values[RightsForm::PROPERTY_MANAGE]);
                
                $success = $this->getEntityRelationService()->updateEntityRelation(
                    $entityRelation, 
                    $this->getCurrentWorkspace()->getId(), 
                    $entityRelation->get_entity_type(), 
                    $entityRelation->get_entity_id(), 
                    $right);
                
                $translation = $success ? 'RightsUpdated' : 'RightsNotUpdated';
                $message = Translation::get($translation);
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $this->redirectWithMessage($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityRelationService
     */
    private function getEntityRelationService()
    {
        if (! isset($this->entityRelationService))
        {
            $this->entityRelationService = new EntityRelationService(new EntityRelationRepository());
        }
        
        return $this->entityRelationService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function getCurrentEntityRelation()
    {
        return $this->getEntityRelationService()->getEntityRelationByIdentifier(
            $this->getCurrentEntityRelationIdentifier());
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\mixed
     */
    private function getCurrentEntityRelationIdentifier()
    {
        return $this->getRequest()->query->get(self::PARAM_ENTITY_RELATION_ID);
    }
}