<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Action\ContentObjectCopier;
use Chamilo\Core\Repository\Form\Type\CopyFormType;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\CategoryService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.repository_manager.component
 */
class CopierComponent extends Manager
{
    /**
     * @var int
     */
    protected $selectedCategoryId;

    /**
     * Runs this component and displays its output.
     *
     * @return string|null
     * @throws NoObjectSelectedException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws NotAllowedException
     */
    public function run()
    {
        $selectedContentObjectIds = (array) $this->getRequest()->getFromUrl(self::PARAM_CONTENT_OBJECT_ID);

        if (!$selectedContentObjectIds)
        {
            throw new NoObjectSelectedException(Translation::get('ContentObject'));
        }

        $selectedObjects = [];
        foreach ($selectedContentObjectIds as $selected_content_object_id)
        {
            $content_object = DataManager::retrieve_by_id(ContentObject::class_name(), $selected_content_object_id);
            if (!$content_object instanceof ContentObject)
            {
                throw new \InvalidArgumentException('No valid content object selected ' . $selected_content_object_id);
            }

            if (!$this->canCopyContentObject($content_object))
            {
                throw new NotAllowedException();
            }

            $selectedObjects[] = $content_object;
        }

        $form = $this->getForm()->create(CopyFormType::class, [], ['user' => $this->getUser()]);
        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->selectedCategoryId = $categoryId = $form->getData()[CopyFormType::ELEMENT_CATEGORY];
            $newCategoryName = $form->getData()[CopyFormType::ELEMENT_NEW_CATEGORY];

            $this->copyContentObject($selectedObjects, $categoryId, $newCategoryName);

            $parameters = array(
                self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS
            );

            if ($this->getWorkspace() instanceof PersonalWorkspace)
            {
                $parameters['parent_id'] = $this->selectedCategoryId;
            }

            $this->redirect($this->getTranslator()->trans('ObjectsCopied', [], Manager::context()), false, $parameters);

            return null;
        }

        return $this->getTwig()->render(
            Manager::context() . ':CopyContentObject.html.twig',
            [
                'HEADER' => $this->render_header(), 'FOOTER' => $this->render_footer(), 'FORM' => $form->createView(),
                'SELECTED_OBJECTS' => $this->getSelectedContentObjectsHtml($selectedObjects)
            ]
        );
    }

    /**
     * @param ContentObject[] $selectedContentObjects
     * @param int $categoryId
     * @param string|null $newCategoryName
     */
    protected function copyContentObject($selectedContentObjects, int $categoryId = 0, string $newCategoryName = null)
    {
        $target_user_id = $this->get_user_id();
        $messages = array();

        $userPersonalWorkspace = new PersonalWorkspace($this->getUser());

        if (!empty($newCategoryName))
        {
            $category = $this->getCategoryService()->createCategoryInWorkspace(
                $newCategoryName, $userPersonalWorkspace, $categoryId
            );

            $this->selectedCategoryId = $categoryId = $category->getId();
        }

        foreach ($selectedContentObjects as $content_object)
        {
            if ($this->canCopyContentObject($content_object))
            {
                $source_user_id = $content_object->get_owner_id();

                $copier = new ContentObjectCopier(
                    $this->get_user(),
                    array($content_object->get_id()),
                    $this->getWorkspace(),
                    $source_user_id,
                    $userPersonalWorkspace,
                    $target_user_id,
                    $categoryId,
                    false
                );

                $copier->run();

                $messages += $copier->get_messages_for_url();
            }
        }
//        Session::register(self::PARAM_MESSAGES, $messages);
    }


    public function get_additional_parameters($additionalParameters = array())
    {
        $additionalParameters[] = self::PARAM_CONTENT_OBJECT_ID;

        return parent::get_additional_parameters($additionalParameters);
    }

    /**
     * @param array $selectedContentObjects
     *
     * @return string|void
     */
    protected function getSelectedContentObjectsHtml($selectedContentObjects = array())
    {
        if (count($selectedContentObjects) == 0)
        {
            return;
        }

        $html[] = '<ul class="attachments_list">';

        foreach ($selectedContentObjects as $content_object)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname(
                ContentObject::get_content_object_type_namespace($content_object->get_type())
            );

            $html[] = '<li><img src="' . $content_object->get_icon_path(Theme::ICON_MINI) . '" alt="' .
                htmlentities(Translation::get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() .
                '</li>';
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService()
    {
        return $this->getService(CategoryService::class);
    }

    /**
     * @param ContentObject $content_object
     * @return bool
     */
    protected function canCopyContentObject(ContentObject $content_object): bool
    {
        return RightsService::getInstance()->canCopyContentObject(
            $this->get_user(),
            $content_object,
            $this->getWorkspace());
    }
}
