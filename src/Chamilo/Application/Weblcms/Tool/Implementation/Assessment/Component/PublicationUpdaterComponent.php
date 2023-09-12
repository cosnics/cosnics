<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class PublicationUpdaterComponent extends Manager
{

    /**
     * Modified version of the default PublicationUpdater to allow for the feedback-functionality
     */
    public function run()
    {
        $pid = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID) ?
            $this->getRequest()->query->get(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
            ) : $this->getRequest()->request->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $pid);

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $pid
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

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get('ToolPublicationUpdaterComponent', ['TITLE' => $content_object->get_title()])
                )
            );

            $course = $this->get_course();
            $is_course_admin = $course->is_course_admin($this->get_user());

            $publication_form = new PublicationForm(
                $this->getUser(), PublicationForm::TYPE_UPDATE, [$publication], $course, $this->get_url(),
                $is_course_admin
            );

            if ($publication_form->validate() || $content_object->getType() == 'introduction')
            {
                $succes = $publication_form->handle_form_submit();

                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'), ['OBJECT' => Translation::get('Publication')],
                        StringUtilities::LIBRARIES
                    ), ENT_COMPAT | ENT_HTML401, 'UTF-8'
                );

                $show_details = $this->getRequest()->query->get('details');
                $tool = $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Manager::PARAM_TOOL);

                $params = [];
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
                    $params[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] =
                        $this->getRequest()->query->get(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                        );
                }

                if (!isset($show_details) && $tool != 'learning_path')
                {
                    $filter = [
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    ];
                }

                $this->redirectWithMessage($message, !$succes, $params, $filter);
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $publication_form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $this->redirectWithMessage(
                Translation::get('NotAllowed'), true, [], [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                ]
            );
        }
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
