<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Metadata\Service\InstanceService;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\Renderer\BasicTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: creator.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which gives the user the possibility to create a new content object in his repository.
 * When no type is passed to this component, the user will see a dropdown list in which a content object type can be
 * selected. Afterwards, the form to create the actual content object will be displayed.
 */
class CreatorComponent extends Manager implements TabsTypeSelectorSupport
{

    /**
     *
     * @var int
     */
    private $template_id;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! RightsService :: getInstance()->canAddContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $typeSelectorFactory = new TypeSelectorFactory($this->get_allowed_content_object_types(), $this->get_user_id());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $type_selector_renderer = new BasicTypeSelectorRenderer($this, $type_selector);

        $this->template_id = TypeSelector :: get_selection();

        if ($this->template_id)
        {
            $template_registration = \Chamilo\Core\Repository\Configuration :: registration_by_id($this->template_id);
            $template = $template_registration->get_template();
            $object = $template->get_content_object();

            $content_object_type_image = 'Logo/Template/' . $template_registration->get_name() . '/16';

            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS)),
                    Translation :: get(
                        'CreateContentType',
                        array(
                            'OBJECTTYPE' => strtolower(
                                Translation :: get(
                                    $template->translate('TypeName'),
                                    null,
                                    $template_registration->get_content_object_type())),
                            'ICON' => Theme :: getInstance()->getImage(
                                $content_object_type_image,
                                'png',
                                Translation :: get(
                                    $template->translate('TypeName'),
                                    null,
                                    $template_registration->get_content_object_type()),
                                null,
                                ToolbarItem :: DISPLAY_ICON,
                                false,
                                $template_registration->get_content_object_type())))));

            $object->set_owner_id($this->get_user_id());

            $category = FilterData :: get_instance($this->getWorkspace())->get_filter_property(
                FilterData :: FILTER_CATEGORY);
            $object->set_parent_id($category);

            $object->set_template_registration_id($this->template_id);

            $content_object_form = ContentObjectForm :: factory(
                ContentObjectForm :: TYPE_CREATE,
                $this->getWorkspace(),
                $object,
                'create_content_object',
                'post',
                $this->get_url(array(TypeSelector :: PARAM_SELECTION => $this->template_id)),
                null);

            if ($content_object_form->validate())
            {
                $values = $content_object_form->exportValues();
                $object = $content_object_form->create_content_object();

                if (! $object)
                {
                    $this->redirect(
                        Translation :: get(
                            'ObjectNotCreated',
                            array('OBJECT' => Translation :: get('ContentObject')),
                            Utilities :: COMMON_LIBRARIES),
                        true,
                        array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECTS, 'type' => $this->type));
                }
                else
                {
                    Event :: trigger(
                        'Activity',
                        Manager :: context(),
                        array(
                            Activity :: PROPERTY_TYPE => Activity :: ACTIVITY_CREATED,
                            Activity :: PROPERTY_USER_ID => $this->get_user_id(),
                            Activity :: PROPERTY_DATE => time(),
                            Activity :: PROPERTY_CONTENT_OBJECT_ID => $object->get_id(),
                            Activity :: PROPERTY_CONTENT => $object->get_title()));

                    $instanceService = new InstanceService();
                    $selectedTab = $instanceService->updateInstances(
                        $this->get_user(),
                        $object,
                        (array) $values[InstanceService :: PROPERTY_METADATA_ADD_SCHEMA]);

                    if ($selectedTab)
                    {
                        $parameters = array();
                        $parameters[Application :: PARAM_ACTION] = self :: ACTION_EDIT_CONTENT_OBJECTS;
                        $parameters[self :: PARAM_CONTENT_OBJECT_ID] = $object->get_id();
                        $parameters[DynamicTabsRenderer :: PARAM_SELECTED_TAB] = array(
                            self :: TABS_CONTENT_OBJECT => $selectedTab);

                        $this->simple_redirect($parameters);
                    }
                }

                if (is_array($object))
                {
                    $parent = $object[0]->get_parent_id();
                    $typeContext = $object[0]->package();
                }
                else
                {
                    $parent = $object->get_parent_id();
                    $typeContext = $object->package();
                }

                $parameters = array();
                $parameters[Application :: PARAM_ACTION] = self :: ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[self :: PARAM_CATEGORY_ID] = $parent;

                $this->redirect(
                    Translation :: get(
                        'ObjectCreated',
                        array('OBJECT' => Translation :: get('TypeName', null, $typeContext)),
                        Utilities :: COMMON_LIBRARIES),
                    false,
                    $parameters);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $content_object_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $type_selector_renderer->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_allowed_content_object_types()
    {
        $types = \Chamilo\Core\Repository\Storage\DataManager :: get_registered_types(true);

        foreach ($types as $index => $type)
        {
            $classnameUtilities = ClassnameUtilities :: getInstance();
            $namespace = $classnameUtilities->getNamespaceFromClassname($type);

            $context = $classnameUtilities->getNamespaceParent($namespace, 2);
            if (! \Chamilo\Configuration\Configuration :: get_instance()->isRegisteredAndActive($context))
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_creator');
    }
}
