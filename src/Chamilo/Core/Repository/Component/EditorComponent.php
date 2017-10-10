<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Service\InstanceService;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to edit an existing content object.
 */
class EditorComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request::get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $id);

        if ($id)
        {
            $object = DataManager::retrieve_by_id(ContentObject::class_name(), $id);

            if (! $object)
            {
                return $this->display_error_page(
                    Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES));
            }

            $template_registration = $object->get_template_registration();
            $template = $template_registration->get_template();

            $content_object_type_image = 'Logo/Template/' . $template_registration->get_name() . '/16';

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS)),
                    Translation::get('EditContentObject', array('CONTENT_OBJECT' => $object->get_title()))));

            if (! RightsService::getInstance()->canEditContentObject($this->get_user(), $object, $this->getWorkspace()))
            {
                throw new NotAllowedException();
            }
            elseif (! $object->is_latest_version())
            {
                $parameters = array();
                $parameters[Application::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[FilterData::FILTER_CATEGORY] = $object->get_parent_id();

                $this->redirect(Translation::get('EditNotAllowed'), true, $parameters);
            }

            if (! \Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager::is_content_object_editable(
                $object->get_id()))
            {
                $parameters = array();
                $parameters[Application::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[FilterData::FILTER_CATEGORY] = $object->get_parent_id();
                $this->redirect(Translation::get('UpdateNotAllowed'), false, $parameters);
            }

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT,
                $this->getWorkspace(),
                $object,
                'edit',
                'post',
                $this->get_url(array(self::PARAM_CONTENT_OBJECT_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_content_object();

                $parameters = array();
                $parameters[Application::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[FilterData::FILTER_CATEGORY] = $object->get_parent_id();

                if ($success)
                {
                    $values = $form->exportValues();

                    $entity = DataClassEntityFactory::getInstance()->getEntityFromDataClass($object);
                    $entityService = new EntityService();
                    $entityService->updateEntitySchemaValues(
                        $this->get_user(),
                        new RelationService(),
                        new ElementService(),
                        $entity,
                        $values[EntityService::PROPERTY_METADATA_SCHEMA]);

                    Event::trigger(
                        'Activity',
                        Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $object->get_id(),
                            Activity::PROPERTY_CONTENT => $object->get_title()));

                    $instanceService = new InstanceService();
                    $selectedTab = $instanceService->updateInstances(
                        $this->get_user(),
                        $object,
                        (array) $values[InstanceService::PROPERTY_METADATA_ADD_SCHEMA]);

                    if ($selectedTab)
                    {
                        $parameters[Application::PARAM_ACTION] = self::ACTION_EDIT_CONTENT_OBJECTS;
                        $parameters[self::PARAM_CONTENT_OBJECT_ID] = $object->get_id();
                        $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = array(
                            self::TABS_CONTENT_OBJECT => $selectedTab);

                        $this->simple_redirect($parameters);
                    }
                    else
                    {
                        $selectedTabs = $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB];
                        unset($selectedTabs[self::TABS_CONTENT_OBJECT]);
                        $parameters[DynamicTabsRenderer::PARAM_SELECTED_TAB] = $selectedTabs;
                    }
                }

                $this->redirect(
                    Translation::get(
                        $success == ContentObjectForm::RESULT_SUCCESS ? 'ObjectUpdated' : 'ObjectNotUpdated',
                        array('OBJECT' => Translation::get('ContentObject')),
                        Utilities::COMMON_LIBRARIES),
                    ($success == ContentObjectForm::RESULT_SUCCESS ? false : true),
                    $parameters);
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
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected',
                        array('OBJECT' => Translation::get('ContentObject')),
                        Utilities::COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_editor');
    }
}
