<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Form\ItemForm;
use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Repository\ItemRepository;
use Chamilo\Core\Menu\Service\ItemService;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $this->check_allowed();
        
        $type = Request::get(Manager::PARAM_TYPE);
        if ($type)
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    null, 
                    Translation::get('Add' . ClassnameUtilities::getInstance()->getPackageNameFromNamespace($type))));
            
            $item = new $type();
            $item_form = ItemForm::factory(
                ItemForm::TYPE_CREATE, 
                $item, 
                $this->get_url(array(Manager::PARAM_TYPE => $type)));
            
            if ($item_form->validate())
            {
                $values = $item_form->exportValues();
                foreach ($item->get_default_property_names() as $property)
                {
                    $item->set_default_property($property, $values[$property]);
                }
                
                foreach ($item->get_additional_property_names() as $property)
                {
                    $item->set_additional_property($property, $values[$property]);
                }
                
                $item_titles = new ItemTitles();
                foreach ($values[ItemTitle::PROPERTY_TITLE] as $isocode => $title)
                {
                    if (! StringUtilities::getInstance()->isNullOrEmpty($title, true))
                    {
                        $item_title = new ItemTitle();
                        $item_title->set_title($title);
                        $item_title->set_isocode($isocode);
                        $item_titles->add($item_title);
                    }
                }
                
                $item->set_titles($item_titles);
                $item->set_display(\Chamilo\Core\Menu\Storage\DataClass\Item :: DISPLAY_BOTH);
                $item->set_hidden();
                $success = $item->create();
                
                if ($success)
                {
                    $message = Translation::get(
                        'ObjectCreated', 
                        array('OBJECT' => Translation::get('ManagerItem')), 
                        Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectNotCreated', 
                        array('OBJECT' => Translation::get('ManagerItem')), 
                        Utilities::COMMON_LIBRARIES);
                }
                
                $itemService = new ItemService(new ItemRepository());
                $itemService->resetCache();
                
                $this->redirect(
                    $message, 
                    ($success ? false : true), 
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE, 
                        Manager::PARAM_PARENT => $item->get_parent()));
            }
            else
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $item_form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
    }
}
