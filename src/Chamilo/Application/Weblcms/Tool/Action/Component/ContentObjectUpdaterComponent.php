<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Service\ServiceFactory;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: edit.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
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

        if(empty($pid))
        {
            throw new NoObjectSelectedException($contentObjectPublicationTranslation);
        }

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $pid
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException($contentObjectPublicationTranslation, $pid);
        }

        $repositoryRightsService = \Chamilo\Core\Repository\Workspace\Service\RightsService::getInstance();
        $weblcmsRightsService = ServiceFactory::getInstance()->getRightsService();

        $canEditContentObject = $repositoryRightsService->canEditContentObject(
            $this->get_user(),
            $publication->get_content_object()
        );
        $canEditPublicationContentObject = $weblcmsRightsService->canUserEditPublication(
            $this->get_user(),
            $publication,
            $this->get_course()
        );

        $is_admin_introduction = $this->get_course()->is_course_admin($this->get_user()) &&
            $publication->get_content_object()->get_type() == Introduction::class_name();

        if ($canEditContentObject || $canEditPublicationContentObject || $is_admin_introduction)
        {
            $content_object = $publication->get_content_object();

            if ($content_object->get_type() == Introduction::class_name())
            {
                $publication->ignore_display_order();
            }

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get('ToolContentObjectUpdateComponent', array('TITLE' => $content_object->get_title()))
                )
            );

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT,
                new PersonalWorkspace($this->get_user()),
                $content_object,
                'edit',
                'post',
                $this->get_url(array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $pid))
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
                    $filter = array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    );
                }
                else
                {
                    $filter = array();
                }

                $this->redirect(Translation::get('ContentObjectUpdated'), false, $params, $filter);
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
            $this->redirect(
                Translation::get("NotAllowed"),
                true,
                array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null)
            );
        }
    }
}
