<?php
namespace Chamilo\Application\Weblcms;

use Chamilo\Application\Weblcms\Form\ContentObjectPublicationForm;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class represents a publisher which shows the selected content objects and the publication form
 * 
 * @author Sven Vanpoucke
 * @package application.weblcms
 */
class ContentObjectPublisher
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
     * Initializes the publisher with use of the content object ids
     * 
     * @param $content_object_ids Array
     */
    public function initialize($content_object_ids)
    {
        $html = array();
        
        if (! is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }
        
        $items_to_publish = count($content_object_ids);
        $publications = array();
        
        if ($items_to_publish > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                $content_object_ids, 
                ContentObject :: get_table_name());
            $parameters = new DataClassRetrievesParameters($condition);
            
            $content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                ContentObject :: class_name(), 
                $parameters);
            
            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get(
                'SelectedContentObjects', 
                null, 
                Utilities :: COMMON_LIBRARIES) . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';
            
            while ($content_object = $content_objects->next_result())
            {
                $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                    ContentObject :: get_content_object_type_namespace($content_object->get_type()));
                
                $html[] = '<li><img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" alt="' .
                     htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' .
                     $content_object->get_title() . '</li>';
                
                $publication = new ContentObjectPublication();
                $publication->set_content_object_id($content_object->get_id());
                $publication->set_course_id($this->parent->get_course_id());
                $publication->set_tool($this->parent->get_tool_id());
                $publication->set_publisher_id($this->parent->get_user_id());
                $publication->set_publication_publisher($this->parent->get_user());
                $publications[] = $publication;
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }
        
        $this->publications = $publications;
        $this->content_object_html = $html;
        $course = $this->parent->get_course();
        $is_course_admin = $course->is_course_admin($this->parent->get_user());
        
        $form = new ContentObjectPublicationForm(
            ContentObjectPublicationForm :: TYPE_CREATE, 
            $publications, 
            $course, 
            $this->parent->get_url(), 
            $is_course_admin);
        
        $this->content_object_publication_form = $form;
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
     * Validates the publication form
     * 
     * @return boolean
     */
    public function ready_to_publish()
    {
        if (! $this->show_form)
        {
            return true;
        }
        
        return $this->content_object_publication_form->validate();
    }

    /**
     * Handles the submit of the publication form
     * 
     * @return boolean
     */
    public function publish()
    {
        if (! $this->show_form)
        {
            return $this->create_publications_without_form();
        }
        
        return $this->content_object_publication_form->handle_form_submit();
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
            
            $succes &= $publication->create();
        }
        
        return $succes;
    }

    /**
     * Returns the if the submit action is publish and build
     */
    public function is_publish_and_build_submit()
    {
        if (! $this->show_form)
        {
            return false;
        }
        
        $values = $this->content_object_publication_form->exportValues();
        return ! empty($values[ContentObjectPublicationForm :: PROPERTY_PUBLISH_AND_BUILD]);
    }

    public function is_publish_and_view_submit()
    {
        if (! $this->show_form)
        {
            return false;
        }
        $values = $this->content_object_publication_form->exportValues();
        return ! empty($values[ContentObjectPublicationForm :: PROPERTY_PUBLISH_AND_VIEW]);
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
