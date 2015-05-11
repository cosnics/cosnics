<?php
namespace Chamilo\Application\Survey\Storage\DataClass;

use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class Publication extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * Publication properties
     */
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';

    private $publication_cache = null;

    public function create()
    {
        $succes = parent :: create();

        $rights = Rights :: get_available_rights_for_publications();
        foreach ($rights as $right)
        {
            if ($right != Rights :: PARTICIPATE_RIGHT)
            {
                Rights :: get_instance()->set_publication_user_right($right, $this->get_publisher(), $this->get_id());
            }
        }
        return $succes;
    }

    public function update()
    {
        $succes = parent :: update();
        return $succes;
    }

    public function delete()
    {
        $location = Rights :: get_instance()->get_publication_location($this->get_id());

        if (! $location->delete())
        {
            return false;
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_SURVEY_PUBLICATION_ID),
            new StaticConditionVariable($this->get_id()));
        $participants = DataManager :: retrieves(
            Participant :: CLASS_NAME,
            new DataClassRetrievesParameters($condition));
        while ($participant = $participants->next_result())
        {
            $participant->delete();
        }

        $succes = parent :: delete();
        return $succes;
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_CONTENT_OBJECT_ID,
                self :: PROPERTY_FROM_DATE,
                self :: PROPERTY_TO_DATE,
                self :: PROPERTY_PUBLISHER,
                self :: PROPERTY_PUBLISHED,
                self :: PROPERTY_TITLE));
    }

    function get_data_manager()
    {
        return DataManager :: get_instance();
    }

    /**
     * Returns the content_object_id of this Publication.
     *
     * @return the content_object_id.
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this Publication.
     *
     * @param content_object_id
     */
    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the title of this Publication.
     *
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Sets the title of this Publication.
     *
     * @param title
     */
    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Returns the from_date of this Publication.
     *
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this Publication.
     *
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this Publication.
     *
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this Publication.
     *
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the publisher of this Publication.
     *
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this Publication.
     *
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this Publication.
     *
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this Publication.
     *
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function is_publication_period()
    {
        $from_date = $this->get_from_date();
        $to_date = $this->get_to_date();
        if ($from_date == 0 && $to_date == 0)
        {
            return true;
        }

        $time = time();

        if ($time < $from_date || $time > $to_date)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function get_publication_object()
    {
        if (! $this->publication_cache)
        {
            $this->publication_cache = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                Survey :: class_name(),
                $this->get_content_object_id());
        }
        return $this->publication_cache;
    }
}

?>