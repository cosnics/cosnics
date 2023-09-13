<?php
namespace Chamilo\Core\Admin\Announcement\Storage\DataClass;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
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

    private ?ContentObject $contentObject;

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

    public function get_content_object(): ?ContentObject
    {
        if (!isset($this->contentObject))
        {
            $this->contentObject = DataManager::retrieve_by_id(
                ContentObject::class, $this->get_content_object_id()
            );
        }

        return $this->contentObject;
    }

    public function get_content_object_id(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    public function get_email_sent(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_EMAIL_SENT);
    }

    public function get_from_date(): ?int
    {
        return $this->getDefaultProperty(self::PROPERTY_FROM_DATE);
    }

    public function get_hidden(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_HIDDEN);
    }

    public function get_modification_date(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFICATION_DATE);
    }

    public function get_publication_date(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_DATE);
    }

    public function get_publisher_id(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLISHER_ID);
    }

    public function get_to_date(): ?int
    {
        return $this->getDefaultProperty(self::PROPERTY_TO_DATE);
    }

    public function is_forever(): bool
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    public function is_hidden(): bool
    {
        return $this->get_hidden() == 1;
    }

    public function set_content_object_id($id): void
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $id);
    }

    public function set_email_sent($email_sent): void
    {
        $this->setDefaultProperty(self::PROPERTY_EMAIL_SENT, $email_sent);
    }

    public function set_from_date($from_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_FROM_DATE, $from_date);
    }

    public function set_hidden($hidden): void
    {
        $this->setDefaultProperty(self::PROPERTY_HIDDEN, $hidden);
    }

    public function set_modification_date($modification_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_MODIFICATION_DATE, $modification_date);
    }

    public function set_publication_date($publication_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    public function set_publisher_id($publisher_id): void
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLISHER_ID, $publisher_id);
    }

    public function set_to_date($to_date): void
    {
        $this->setDefaultProperty(self::PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Toggles the visibility of this publication.
     */
    public function toggle_visibility(): void
    {
        $this->set_hidden(!$this->is_hidden());
    }

    public function was_email_sent(): bool
    {
        return $this->get_email_sent() == 1;
    }
}
