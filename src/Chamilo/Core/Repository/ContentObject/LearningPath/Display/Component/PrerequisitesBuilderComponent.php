<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\PrerequisitesBuilderForm;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: prerequisites_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.learning_path.component
 */
class PrerequisitesBuilderComponent extends TabComponent
{

    public function build()
    {
        $this->validateAndFixCurrentStep();
        
        if (! $this->canEditComplexContentObjectPathNode($this->get_current_node()))
        {
            throw new NotAllowedException();
        }
        
        $complex_content_object_item = $this->get_current_complex_content_object_item();
        
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail::getInstance();
        
        $trail->add(new Breadcrumb($this->get_url(), Translation::get('BuildPrerequisites')));
        
        if (! $complex_content_object_item instanceof ComplexContentObjectItem)
        {
            $html = array();
            
            $html[] = $this->render_header($trail);
            $html[] = $this->display_error_message(
                Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES));
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
        
        $form = new PrerequisitesBuilderForm($this);
        
        if ($form->validate())
        {
            $succes = $form->build_prerequisites();
            $message = $succes ? 'PrerequisitesBuild' : 'PrerequisitesNotBuild';
            
            $parameters = array();
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
            
            $this->redirect(Translation::get($message), ! $succes, $parameters, array(self::PARAM_CONTENT_OBJECT_ID));
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

    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP, self::PARAM_FULL_SCREEN, self::PARAM_CONTENT_OBJECT_ID);
    }
}
