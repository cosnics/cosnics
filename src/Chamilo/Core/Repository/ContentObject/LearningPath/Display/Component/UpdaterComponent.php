<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends BaseHtmlTreeComponent
{

    /**
     * Executes this component
     */
    public function build()
    {
        $this->validateSelectedLearningPathChild();

        if ($this->canEditCurrentTreeNode())
        {
            $content_object = $this->getCurrentContentObject();

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT,
                new PersonalWorkspace($this->get_user()),
                $content_object,
                'edit',
                'post',
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        self::PARAM_CHILD_ID => $this->getCurrentLearningPathChildId()
                    )
                )
            );

            if ($form->validate())
            {
                $succes = $form->update_content_object();

                if ($succes)
                {
                    Event::trigger(
                        'Activity',
                        \Chamilo\Core\Repository\Manager::context(),
                        array(
                            Activity::PROPERTY_TYPE => Activity::ACTIVITY_UPDATED,
                            Activity::PROPERTY_USER_ID => $this->get_user_id(),
                            Activity::PROPERTY_DATE => time(),
                            Activity::PROPERTY_CONTENT_OBJECT_ID => $content_object->get_id(),
                            Activity::PROPERTY_CONTENT => $content_object->get_title()
                        )
                    );
                }

                if ($succes && $form->is_version())
                {
                    try
                    {
                        $learningPathChildService = $this->getLearningPathChildService();
                        $learningPathChildService->updateContentObjectInLearningPathChild(
                            $this->getCurrentTreeNode(), $content_object->get_latest_version()
                        );
                    }
                    catch (\Exception $ex)
                    {
                        $succes = false;
                    }
                }

                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        array('OBJECT' => Translation::get('ContentObject')),
                        Utilities::COMMON_LIBRARIES
                    )
                );

                $params = array();
                $params[self::PARAM_ACTION] = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

                $this->redirect($message, (!$succes), $params, array(self::PARAM_CONTENT_OBJECT_ID));
            }
            else
            {
                if ($this->getCurrentTreeNode()->isRootNode())
                {
                    $title = Translation::get('ChangeIntroduction');
                }
                else
                {
                    $title = Translation::get(
                        'EditContentObject',
                        array('CONTENT_OBJECT' => $this->getCurrentContentObject()->get_title())
                    );
                }

                $trail = BreadcrumbTrail::getInstance();
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(), $title
                    )
                );

                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_CHILD_ID);
    }

    /**
     * @return string
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();
        $html[] = $this->renderRepoDragPanel();

        return implode(PHP_EOL, $html);
    }
}
