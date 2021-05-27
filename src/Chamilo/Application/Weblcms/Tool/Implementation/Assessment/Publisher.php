<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Form\PublicationForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Modified version of the default Publisher to allow for the feedback-functionality
 *
 * @author Hans De Bisschop
 */
class Publisher
{

    /**
     * The component on which this publisher will run
     *
     * @var Application
     */
    private $parent;

    /**
     * The publications for the form
     *
     * @var Array<ContentObjectPublication>
     */
    private $publications;

    /**
     * The html that displays the list of available content objects
     */
    private $content_object_html;

    /**
     * Show the form or publish directly
     *
     * @var boolean
     */
    private $show_form;

    public function __construct($parent, $content_object_ids, $show_form = true)
    {
        $this->parent = $parent;
        $this->show_form = $show_form;

        $this->initialize($content_object_ids);
    }

    /**
     * Creates the publications without the publications form
     *
     * @return boolean
     */
    public function create_publications_without_form()
    {
        $publications = $this->publications;
        $succes = true;

        foreach ($publications as $publication)
        {
            $publication->set_category_id(0);
            $publication->set_from_date(0);
            $publication->set_to_date(0);
            $publication->set_publication_date(time());
            $publication->set_modified_date(time());
            $publication->set_hidden(0);
            $publication->set_show_on_homepage(0);

            if ($publication->create())
            {
                $assessment_publication = new Publication();
                $assessment_publication->set_publication_id($publication->get_id());
                $assessment_publication->set_show_score(1);
                $assessment_publication->set_show_correction(1);
                $assessment_publication->set_show_answer_feedback(Configuration::ANSWER_FEEDBACK_TYPE_ALL);
                $assessment_publication->set_feedback_location(Configuration::FEEDBACK_LOCATION_TYPE_BOTH);
                $succes &= $assessment_publication->create();
            }
            else
            {
                $succes &= false;
            }
        }

        return $succes;
    }

    /**
     * Returns the publications for the content objects
     *
     * @var Array<ContentObjectPublication>
     */
    public function get_publications()
    {
        return $this->publications;
    }

    /**
     * Initializes the publisher with use of the content object ids
     *
     * @param $content_object_ids Array
     */
    public function initialize($content_object_ids)
    {
        $html = [];

        if (!is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }

        $items_to_publish = count($content_object_ids);
        $publications = [];

        if ($items_to_publish > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                $content_object_ids, ContentObject::get_table_name()
            );
            $parameters = new DataClassRetrievesParameters($condition);

            $content_objects = DataManager::retrieve_active_content_objects(
                ContentObject::class, $parameters
            );

            $html[] = '<div class="panel panel-default">';

            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">';
            $html[] = Translation::get('SelectedContentObjects', null, Utilities::COMMON_LIBRARIES);
            $html[] = '</h3>';
            $html[] = '</div>';

            $html[] = '<ul class="list-group">';

            foreach($content_objects as $content_object)
            {
                $namespace = ContentObject::get_content_object_type_namespace($content_object->get_type());
                $glyph = $content_object->getGlyph(IdentGlyph::SIZE_MINI);

                if (RightsService::getInstance()->canUseContentObject($this->parent->get_user(), $content_object))
                {
                    $html[] = '<li class="list-group-item">' . $glyph->render() . ' ' . $content_object->get_title() .
                        '</li>';

                    $publication = new ContentObjectPublication();
                    $publication->set_content_object_id($content_object->get_id());
                    $publication->set_course_id($this->parent->get_course_id());
                    $publication->set_tool($this->parent->get_tool_id());
                    $publication->set_publisher_id($this->parent->get_user_id());
                    $publication->set_publication_publisher($this->parent->get_user());
                    $publications[] = $publication;
                }
                else
                {
                    $html[] = '<li class="list-group-item">' . $glyph->render() . ' ' . $content_object->get_title() .
                        '<em class="text-danger">' . Translation::get('NotAllowed') . '</em>' . '</li>';
                }
            }

            $html[] = '</ul>';
            $html[] = '</div>';
        }

        $this->publications = $publications;
        $this->content_object_html = $html;
        $course = $this->parent->get_course();
        $is_course_admin = $course->is_course_admin($this->parent->get_user());

        $form = new PublicationForm(
            $this->parent->get_user(), PublicationForm::TYPE_CREATE, $publications, $course, $this->parent->get_url(),
            $is_course_admin
        );

        $this->content_object_publication_form = $form;
    }

    /**
     * Returns the if the submit action is publish and build
     */
    public function is_publish_and_build_submit()
    {
        if (!$this->show_form)
        {
            return false;
        }

        $values = $this->content_object_publication_form->exportValues();

        return !empty($values[PublicationForm::PROPERTY_PUBLISH_AND_BUILD]);
    }

    /**
     * Handles the submit of the publication form
     *
     * @return boolean
     */
    public function publish()
    {
        if (!$this->show_form)
        {
            return $this->create_publications_without_form();
        }

        return $this->content_object_publication_form->handle_form_submit();
    }

    /**
     * Validates the publication form
     *
     * @return boolean
     */
    public function ready_to_publish()
    {
        if (!$this->show_form)
        {
            return true;
        }

        return $this->content_object_publication_form->validate();
    }

    /**
     * Renders the form with the content object listings
     *
     * @return String
     */
    public function toHtml()
    {
        $html = $this->content_object_html;
        $html[] = $this->content_object_publication_form->toHtml();
        $html[] = '<div style="clear: both;"></div>';

        return implode(PHP_EOL, $html);
    }
}
