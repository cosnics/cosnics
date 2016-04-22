<?php
namespace Chamilo\Core\Admin\Announcement;

use Chamilo\Core\Admin\Announcement\Form\PublicationForm;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

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

    public function __construct($parent, $content_object_ids)
    {
        $this->parent = $parent;
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

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-heading">';
            $html[] = '<h3 class="panel-title">' . Translation :: get(
                'SelectedContentObjects',
                null,
                Utilities :: COMMON_LIBRARIES) . '</h3>';
            $html[] = '</div>';
            $html[] = '<div class="panel-body">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $namespace = ContentObject :: get_content_object_type_namespace($content_object->get_type());

                if (RightsService :: getInstance()->canUseContentObject($this->parent->get_user(), $content_object))
                {
                    $html[] = '<li><img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" alt="' .
                         htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' .
                         $content_object->get_title() . '</li>';

                    $publication = new Publication();
                    $publication->set_content_object_id($content_object->get_id());
                    $publication->set_publisher_id($this->parent->get_user_id());
                    $publication->set_publisher($this->parent->get_user());
                    $publications[] = $publication;
                }
                else
                {
                    $html[] = '<li><img src="' . $content_object->get_icon_path(Theme :: ICON_MINI) . '" alt="' .
                         htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' .
                         $content_object->get_title() . '<span style="color: red; font-style: italic;">' .
                         Translation :: get('NotAllowed') . '</span>' . '</li>';
                }
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $this->publications = $publications;
        $this->content_object_html = $html;

        $form = new PublicationForm(PublicationForm :: TYPE_CREATE, $publications, $this->parent->get_url());

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
        return $this->content_object_publication_form->validate();
    }

    /**
     * Handles the submit of the publication form
     *
     * @return boolean
     */
    public function publish()
    {
        return $this->content_object_publication_form->handle_form_submit();
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
