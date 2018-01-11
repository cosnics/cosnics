<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.weblcms.tool.assignment.php.component
 *          Publication updater for assignments
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class PublicationUpdaterComponent extends Manager
{
    /**
     * Modified version of the default PublicationUpdater to allow for the feedback-functionality
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \HTML_QuickForm_Error
     * @throws \Exception
     */
    public function run()
    {
        $pid = $this->getRequest()->getFromPostOrUrl(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $pid);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(), $pid
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('ContentObjectPublication'), $pid
            );
        }

        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

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
            $is_course_admin,
            [],
            $this->getTranslator(),
            $this->getPublicationRepository()
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

            $show_details = $this->getRequest()->getFromUrl('details');

            $params = array();
            if ($show_details == 1)
            {
                $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] = $pid;
                $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                    \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW;
            }
            else
            {
                $filter = array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                );
            }

            $this->redirect($message, !$succes, $params, $filter);

            return null;
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $publication_form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return array|string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
    }
}
