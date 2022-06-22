<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Metadata\Service\InstanceService;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\Renderer\BasicTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Service\ContentObjectSaver;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\GenericTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
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
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \ReflectionException
     */
    public function run()
    {
        if (!RightsService::getInstance()->canAddContentObjects($this->get_user(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $typeSelectorFactory = new TypeSelectorFactory($this->get_allowed_content_object_types(), $this->get_user_id());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $type_selector_renderer = new BasicTypeSelectorRenderer($this, $type_selector);

        $templateIdentifier = TypeSelector::get_selection();

        if ($templateIdentifier)
        {
            $template_registration =
                $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier($templateIdentifier);
            $template = $template_registration->get_template();
            //            $contentObject = $template->get_content_object();

            $contentObject = $this->getContentObjectSaver()->getContentObjectInstanceForTemplateAndUserIdentfier(
                $templateIdentifier, $this->get_user_id()
            );

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS)), Translation::get(
                    'CreateContentType', array(
                        'OBJECTTYPE' => strtolower(
                            Translation::get(
                                $template->translate('TypeName'), null, $contentObject->package()
                            )
                        )
                    )
                )
                )
            );

            $contentObject->set_owner_id($this->get_user_id());

            $category = FilterData::getInstance($this->getWorkspace())->get_filter_property(
                FilterData::FILTER_CATEGORY
            );
            $contentObject->set_parent_id($category);

            $contentObject->set_template_registration_id($templateIdentifier);

            $content_object_form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_CREATE, $this->getWorkspace(), $contentObject, 'create_content_object',
                FormValidator::FORM_METHOD_POST,
                $this->get_url(array(TypeSelector::PARAM_SELECTION => $templateIdentifier))
            );

            if ($content_object_form->validate())
            {
                $values = $content_object_form->exportValues();
                $contentObject = $content_object_form->create_content_object();

                if (!$contentObject)
                {
                    $this->redirectWithMessage(
                        Translation::get(
                            'ObjectNotCreated', array('OBJECT' => Translation::get('ContentObject')),
                            StringUtilities::LIBRARIES
                        ), true, array(self::PARAM_ACTION => self::ACTION_CREATE_CONTENT_OBJECTS, 'type' => $this->type)
                    );
                }
                else
                {
                    Event::trigger(
                        'Activity', Manager::context(), array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_CREATED,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $contentObject->get_id(),
                            Activity::PROPERTY_CONTENT => $contentObject->get_title()
                        )
                    );

                    $selectedTab = $this->getInstanceService()->updateInstances(
                        $this->get_user(), $contentObject,
                        (array) $values[InstanceService::PROPERTY_METADATA_ADD_SCHEMA]
                    );

                    if ($selectedTab)
                    {
                        $parameters = [];
                        $parameters[Application::PARAM_ACTION] = self::ACTION_EDIT_CONTENT_OBJECTS;
                        $parameters[self::PARAM_CONTENT_OBJECT_ID] = $contentObject->get_id();
                        $parameters[GenericTabsRenderer::PARAM_SELECTED_TAB] = array(
                            self::TABS_CONTENT_OBJECT => $selectedTab
                        );

                        $this->redirect($parameters);
                    }
                }

                if (is_array($contentObject))
                {
                    $parent = $contentObject[0]->get_parent_id();
                    $typeContext = $contentObject[0]->package();
                }
                else
                {
                    $parent = $contentObject->get_parent_id();
                    $typeContext = $contentObject->package();
                }

                $parameters = [];
                $parameters[Application::PARAM_ACTION] = self::ACTION_BROWSE_CONTENT_OBJECTS;
                $parameters[self::PARAM_CATEGORY_ID] = $parent;

                $this->redirectWithMessage(
                    Translation::get(
                        'ObjectCreated', array('OBJECT' => Translation::get('TypeName', null, $typeContext)),
                        StringUtilities::LIBRARIES
                    ), false, $parameters
                );
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $content_object_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $type_selector_renderer->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * @return \Chamilo\Core\Repository\Service\ContentObjectSaver
     */
    public function getContentObjectSaver()
    {
        return $this->getService(ContentObjectSaver::class);
    }

    /**
     * @return \Chamilo\Core\Metadata\Service\InstanceService
     */
    private function getInstanceService()
    {
        return $this->getService(InstanceService::class);
    }

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     */
    public function getTemplateRegistrationConsulter()
    {
        return $this->getService(TemplateRegistrationConsulter::class);
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     */
    public function get_allowed_content_object_types()
    {
        $types = DataManager::get_registered_types(true);

        foreach ($types as $index => $type)
        {
            $classnameUtilities = ClassnameUtilities::getInstance();
            $namespace = $classnameUtilities->getNamespaceFromClassname($type);

            $context = $classnameUtilities->getNamespaceParent($namespace, 2);
            if (!Configuration::getInstance()->isRegisteredAndActive($context))
            {
                unset($types[$index]);
            }
        }

        return $types;
    }
}
