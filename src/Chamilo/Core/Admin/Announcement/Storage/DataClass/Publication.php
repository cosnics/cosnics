<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Core\Admin\Announcement\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Publication extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    public const PROPERTY_EMAIL_SENT = 'email_sent';
    public const PROPERTY_FROM_DATE = 'from_date';
    public const PROPERTY_HIDDEN = 'hidden';
    public const PROPERTY_MODIFICATION_DATE = 'modified';
    public const PROPERTY_PUBLICATION_DATE = 'published';
    public const PROPERTY_PUBLISHER_ID = 'publisher_id';
    public const PROPERTY_TO_DATE = 'to_date';

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $content_object;

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_CONTENT_OBJECT_ID,
                self::PROPERTY_FROM_DATE,
                self::PROPERTY_TO_DATE,
                self::PROPERTY_HIDDEN,
                self::PROPERTY_PUBLISHER_ID,
                self::PROPERTY_PUBLICATION_DATE,
                self::PROPERTY_MODIFICATION_DATE,
                self::PROPERTY_EMAIL_SENT
            ]
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'admin_announcement_publication';
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_content_object()
    {
        if (!isset($this->content_object))
        {
            $this->content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $this->get_content_object_id()
            );
        }

        return $this->content_object;
    }

    /**
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_email_sent()
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL_SENT);
    }

    public function get_from_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_FROM_DATE);
    }

    public function get_hidden()
    {
        return $this->getDefaultProperty(self::PROPERTY_HIDDEN);
    }

    public function get_modification_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFICATION_DATE);
    }

    /**
     * Gets the date on which this publication was made
     *
     * @return int
     */
    public function get_publication_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_DATE);
    }

    public function get_publisher_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER_ID);
    }

    public function get_to_date()
    {
        return $this->getDefaultProperty(self::PROPERTY_TO_DATE);
    }

    /**
     * @return bool
     */
    public function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    public function is_hidden()
    {
        return $this->get_hidden();
    }

    public function set_content_object_id($id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $id);
    }

    public function set_email_sent($email_sent)
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL_SENT, $email_sent);
    }

    public function set_from_date($from_date)
    {
        $this->setDefaultProperty(self::PROPERTY_FROM_DATE, $from_date);
    }

    public function set_hidden($hidden)
    {
        $this->setDefaultProperty(self::PROPERTY_HIDDEN, $hidden);
    }

    public function set_modification_date($modification_date)
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFICATION_DATE, $modification_date);
    }

    public function set_publication_date($publication_date)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    public function set_publisher_id($publisher_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    public function set_to_date($to_date)
    {
        $this->setDefaultProperty(self::PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Toggles the visibility of this publication.
     */
    public function toggle_visibility()
    {
        $this->set_hidden(!$this->is_hidden());
    }

    public function was_email_sent()
    {
        return $this->get_email_sent();
    }
}
