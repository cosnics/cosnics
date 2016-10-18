<?php
namespace Chamilo\Application\Survey\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * A survey publication
 */
class Publication extends DataClass
{

    // DataClass properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';

    /**
     *
     * @var \repository\ContentObject
     */
    private $content_object;

    /**
     *
     * @var \user\User
     */
    private $publisher;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_CONTENT_OBJECT_ID,
                self :: PROPERTY_PUBLISHER_ID,
                self :: PROPERTY_PUBLISHED,
                self :: PROPERTY_MODIFIED,
                self :: PROPERTY_TITLE,
                self :: PROPERTY_FROM_DATE,
                self :: PROPERTY_TO_DATE));
    }

    /**
     *
     * @return int
     */
    public function getContentObjectId()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     *
     * @return \repository\ContentObject
     */
    public function getContentObject()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $this->getContentObjectId());
        }

        return $this->content_object;
    }

    /**
     *
     * @param int $content_object_id
     */
    public function setContentObjectId($contentObjectId)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $contentObjectId);
    }

    /**
     *
     * @return int
     */
    public function getPublisherId()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

    /**
     *
     * @return \user\User
     */
    public function getPublisher()
    {
        if (! isset($this->publisher))
        {
            $this->publisher = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $this->getPublisherId());
        }

        return $this->publisher;
    }

    /**
     *
     * @param int $publisher_id
     */
    public function setPublisherId($publisherId)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisherId);
    }

    /**
     *
     * @return int
     */
    public function getPublished()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     *
     * @param int $published
     */
    public function setPublished($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    /**
     *
     * @return int
     */
    public function getModified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    /**
     *
     * @param int $modified
     */
    public function setModified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    /**
     * Returns the title of this Publication.
     *
     * @return the title.
     */
    function getTitle()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Sets the title of this Publication.
     *
     * @param title
     */
    function setTitle($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    /**
     * Returns the from_date of this Publication.
     *
     * @return the from_date.
     */
    function getFromDate()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this Publication.
     *
     * @param from_date
     */
    function setFromDate($fromDate)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $fromDate);
    }

    /**
     * Returns the to_date of this Publication.
     *
     * @return the to_date.
     */
    function getToDate()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this Publication.
     *
     * @param to_date
     */
    function setToDate($toDate)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $toDate);
    }

    function isPublicationPeriod()
    {
        $fromDate = $this->getFromDate();
        $toDate = $this->getToDate();
        if ($fromDate == 0 && $toDate == 0)
        {
            return true;
        }

        $time = time();

        if ($time < $fromDate || $time > $toDate)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
