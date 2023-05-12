<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.component
 */
class ContentObjectUpdaterComponent extends Manager implements DelegateComponent
{
    public function run()
    {
        $pid = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) ? Request::get(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
        ) : Request::post(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
        );

        $contentObjectPublicationTranslation = Translation::getInstance()->getTranslation(
            'ContentObjectPublication', null, 'Chamilo\Application\Weblcms'
        );

        if (empty($pid))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pid
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $pid);
        }

        $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

        $canEditContentObject = $this->getRightsService()->canEditContentObject(
            $this->get_user(), $publication->get_content_object()
        );
        $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
            $this->get_user(), $publication, $this->get_course()
        );

        $is_admin_introduction = $this->get_course()->is_course_admin($this->get_user()) &&
            $publication->get_content_object()->getType() == Introduction::class;

        if ($canEditContentObject || $canEditPublicationContentObject || $is_admin_introduction)
        {
            $content_object = $publication->get_content_object();

            if ($content_object->getType() == Introduction::class)
            {
                $publication->ignore_display_order();
            }

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get('ToolContentObjectUpdateComponent', ['TITLE' => $content_object->get_title()])
                )
            );

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $content_object, 'edit',
                FormValidator::FORM_METHOD_POST,
                $this->get_url([\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $pid])
            );

            if ($form->validate())
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $publication->set_content_object_id($content_object->get_latest_version_id());
                    $publication->update();
                }

                $tool = $this->get_tool_id();

                if ($tool == 'learning_path')
                {
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = null;
                    $params['display_action'] = 'view';
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = Request::get(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    );
                }

                if ($tool != 'learning_path')
                {
                    $filter = [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    ];
                }
                else
                {
                    $filter = [];
                }

                $this->redirectWithMessage(Translation::get('ContentObjectUpdated'), false, $params, $filter);
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $this->redirectWithMessage(
                Translation::get('NotAllowed'), true,
                [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null]
            );
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    protected function getRightsService(): RightsService
    {
        return $this->getService(RightsService::class);
    }
}
