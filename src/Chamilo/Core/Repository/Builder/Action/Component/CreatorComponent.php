<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_builder.component
 */
class CreatorComponent extends Manager implements \Chamilo\Core\Repository\Viewer\ViewerInterface, DelegateComponent
{

    /**
     *
     * @var string[]
     */
    private $type_selection;

    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        $type_selection = $this->get_type_selection();
        $content_object = $this->get_parent_content_object();

        if ($type_selection)
        {
            $template_registration = \Chamilo\Core\Repository\Configuration::registration_by_id($type_selection);
            $template = $template_registration->get_template();

            $html[] = '<h4>';
            $html[] = Translation::get(
                'AddOrCreateNewTo',
                array(
                    'NEW_TYPE' => $template->translate('TypeName'),
                    'PARENT_TYPE' => Translation::get(
                        'TypeName',
                        null,
                        ClassnameUtilities::getInstance()->getNamespaceFromClassname($content_object->get_type())),
                    'TITLE' => $content_object->get_title()),
                \Chamilo\Core\Repository\Manager::context());
            $html[] = '</h4><br />';
        }
        else
        {
            $title[] = Translation::get(
                'AddOrCreateNewTo',
                array(
                    'NEW_TYPE' => Translation::get('Items'),
                    'PARENT_TYPE' => Translation::get(
                        'TypeName',
                        null,
                        ClassnameUtilities::getInstance()->getNamespaceFromClassname($content_object->get_type())),
                    'TITLE' => $content_object->get_title()),
                \Chamilo\Core\Repository\Manager::context());
        }

        return implode(PHP_EOL, $html);
    }

    public function run()
    {
        $this->get_complex_content_object_breadcrumbs();

        $this->type_selection = TypeSelector::get_selection();

        $exclude = $this->retrieve_used_items($this->get_root_content_object()->get_id());
        $exclude[] = $this->get_root_content_object()->get_id();

        if (! \Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_TABS_DISABLED, true);

            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                $applicationConfiguration);

            $component->set_parameter(
                \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                $this->get_parent()->get_complex_content_object_item_id());

            $component->set_parameter(TypeSelector::PARAM_SELECTION, $this->type_selection);

            $component->set_excluded_objects($exclude);

            return $component->run();
        }
        else
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects($this->getUser());

            if (! is_array($objects))
            {
                $objects = array($objects);
            }

            foreach ($objects as $content_object_id)
            {
                $type = \Chamilo\Core\Repository\Storage\DataManager::determineDataClassType(
                    ContentObject::class_name(),
                    $content_object_id);

                if (method_exists($this->get_parent(), 'get_helper_object'))
                {
                    $helper_object = $this->get_parent()->get_helper_object($type);
                    if ($helper_object)
                    {
                        $this->create_helper_object($helper_object, $content_object_id);
                        $content_object_id = $helper_object->get_id();
                    }
                }

                // gets the type of the helper object
                $type = \Chamilo\Core\Repository\Storage\DataManager::determineDataClassType(
                    ContentObject::class_name(),
                    $content_object_id);

                $this->create_complex_content_object_item($type, $content_object_id);
            }

            $this->redirect(
                Translation::get(
                    'ObjectAdded',
                    array('OBJECT' => Translation::get('ContentObject')),
                    Utilities::COMMON_LIBRARIES),
                false,
                array(
                    \Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager::ACTION_BROWSE,
                    \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()->get_complex_content_object_item_id()));
        }
    }

    private function retrieve_used_items($parent)
    {
        $items = array();

        $complex_content_object_items = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::class_name(),
                    \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::PROPERTY_PARENT),
                new StaticConditionVariable($parent),
                \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->is_complex())
            {
                $items[] = $complex_content_object_item->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($complex_content_object_item->get_ref()));
            }
        }

        return $items;
    }

    public function create_helper_object($helper_object, $content_object_id)
    {
        $helper_object->set_title($helper_object->get_type_name());
        $helper_object->set_description($helper_object->get_type_name());
        $helper_object->set_owner_id($this->get_user_id());
        $helper_object->set_reference($content_object_id);
        $helper_object->set_parent_id(0);
        $helper_object->create();
    }

    public function create_complex_content_object_item($type, $content_object_id)
    {
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem::factory(
            $type);
        $complex_content_object_item->set_ref($content_object_id);
        $parent_id = $this->get_parent_content_object_id();
        $complex_content_object_item->set_parent($parent_id);
        $complex_content_object_item->set_display_order(
            \Chamilo\Core\Repository\Storage\DataManager::select_next_display_order($parent_id));
        $complex_content_object_item->set_user_id($this->get_user_id());
        $complex_content_object_item->create();
    }

    public function get_allowed_content_object_types()
    {
        return $this->get_root_content_object()->get_allowed_types();
    }

    public function is_shared_object_browser()
    {
        return (Request::get(\Chamilo\Core\Repository\Viewer\Component\BrowserComponent::SHARED_BROWSER) == 1);
    }

    public function get_type_selection()
    {
        if (! isset($this->type_selection))
        {
            $this->type_selection = TypeSelector::get_selection();
        }

        return $this->type_selection;
    }
}
