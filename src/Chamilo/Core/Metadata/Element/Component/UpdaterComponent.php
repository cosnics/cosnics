<?php
namespace Chamilo\Core\Metadata\Element\Component;

use Chamilo\Core\Metadata\Element\Form\ElementForm;
use Chamilo\Core\Metadata\Element\Manager;
use Chamilo\Core\Metadata\Element\Storage\DataManager;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 * 
 * @package core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
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
        
        $element_id = Request::get(self::PARAM_ELEMENT_ID);
        $this->set_parameter(self::PARAM_ELEMENT_ID, $element_id);
        
        $element = DataManager::retrieve_by_id(Element::class_name(), $element_id);
        
        $form = new ElementForm($this->get_url(), $element);
        
        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();
                
                $element->set_name($values[Element::PROPERTY_NAME]);
                $element->set_display_name($values[Element::PROPERTY_DISPLAY_NAME]);
                $element->set_value_type($values[Element::PROPERTY_VALUE_TYPE]);
                
                $success = $element->update();
                
                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';
                
                $message = Translation::get(
                    $translation, 
                    array('OBJECT' => Translation::get('Element')), 
                    Utilities::COMMON_LIBRARIES);
            }
            catch (\Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }
            
            $this->redirect(
                $message, 
                ! $success, 
                array(
                    self::PARAM_ACTION => self::ACTION_BROWSE, 
                    \Chamilo\Core\Metadata\Schema\Manager::PARAM_SCHEMA_ID => $element->get_schema_id()));
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

    /**
     * Adds additional breadcrumbs
     * 
     * @param \libraries\format\BreadcrumbTrail $breadcrumb_trail
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_BROWSE), array(self::PARAM_ELEMENT_ID)), 
                Translation::get('BrowserComponent')));
    }
}