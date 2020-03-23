<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Component;

use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Rights\Form\RightsForm;
use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\EntityRelationService;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Rights\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        $workspace = $this->getCurrentWorkspace();
        
        $form = new RightsForm($this->get_url());
        
        if ($form->validate())
        {
            $values = $form->exportValues();
            
            try
            {
                $entityRelationService = new EntityRelationService(new EntityRelationRepository());
                $rightsService = RightsService::getInstance();
                
                $right = $rightsService->getAggregatedRight(
                    (int) $values[RightsForm::PROPERTY_VIEW], 
                    (int) $values[RightsForm::PROPERTY_USE], 
                    (int) $values[RightsForm::PROPERTY_COPY], 
                    (int) $values[RightsForm::PROPERTY_MANAGE]);
                
                $success = $entityRelationService->setEntityRelations(
                    $workspace, 
                    $values[RightsForm::PROPERTY_ACCESS], 
                    $right);
                
                $translation = $success ? 'RightsSet' : 'RightsNotSet';
                $message = Translation::get($translation);
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $parameters = $filters = array();
            
            if ($values['submit'] == Translation::get('SaveAndAddNew', null, Manager::context()))
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_CREATE;
            }
            else
            {
                $filters[] = self::PARAM_ACTION;
                $parameters[\Chamilo\Core\Repository\Workspace\Manager::PARAM_ACTION] = $this->getRequest()->get(
                    \Chamilo\Core\Repository\Workspace\Manager::PARAM_BROWSER_SOURCE);
            }
            
            $this->redirect($message, ! $success, $parameters, $filters);
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}