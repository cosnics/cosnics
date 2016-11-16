<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Component;

use Chamilo\Core\Metadata\Relation\Instance\Form\RelationInstanceForm;
use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Core\Metadata\Relation\Instance\Service\RelationInstanceService;
use Chamilo\Core\Metadata\Storage\DataClass\RelationInstance;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $relationInstance = new RelationInstance();
        
        $form = new RelationInstanceForm(
            $relationInstance, 
            $this->getSourceEntities(), 
            $this->getRelations(), 
            $this->getTargetEntities(), 
            $this->get_url());
        
        if ($form->validate())
        {
            $submittedValues = $form->exportValues();
            $relationInstanceService = new RelationInstanceService();
            $success = $relationInstanceService->createRelationInstancesFromSubmittedValues(
                $this->get_user(), 
                $submittedValues);
            
            $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';
            
            $message = Translation::get(
                $translation, 
                array('OBJECT' => Translation::get('RelationInstance')), 
                Utilities::COMMON_LIBRARIES);
            
            $this->redirect($message, ! $success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
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
