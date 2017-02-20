<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationUpdaterComponent extends Manager
{

    /**
     * Modified version of the default PublicationUpdater to allow for the feedback-functionality
     */
    public function run()
    {
        $pid = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) ? Request::get(
            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
        ) : $_POST[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID];

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $pid);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $pid
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('ContentObjectPublication'), $pid
            );
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication))
        {

            $content_object = $publication->get_content_object();

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get('ToolPublicationUpdaterComponent', array('TITLE' => $content_object->get_title()))
                )
            );

            $course = $this->get_course();
            $is_course_admin = $course->is_course_admin($this->get_user());

            $publication_form = new PublicationForm(
                $this->getUser(),
                PublicationForm::TYPE_UPDATE,
                array($publication),
                $course,
                $this->get_url(),
                $is_course_admin
            );

            if ($publication_form->validate() || $content_object->get_type() == 'introduction')
            {
                $succes = $publication_form->handle_form_submit();

                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        array('OBJECT' => Translation::get('Publication')),
                        Utilities::COMMON_LIBRARIES
                    ),
                    ENT_COMPAT | ENT_HTML401,
                    'UTF-8'
                );

                $show_details = Request::get('details');
                $tool = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);

                $params = array();
                if ($show_details == 1)
                {
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $pid;
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                        \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
                }

                // TODO: What does this code do? Is this still valid?
                if ($tool == 'learning_path')
                {
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = null;
                    $params['display_action'] = 'view';
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = Request::get(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    );
                }

                if (!isset($show_details) && $tool != 'learning_path')
                {
                    $filter = array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    );
                }

                $this->redirect($message, !$succes, $params, $filter);
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $publication_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $this->redirect(
                Translation::get("NotAllowed"),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                )
            );
        }
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
