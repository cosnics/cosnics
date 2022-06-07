<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Preview;

use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Feedback;
use Ramsey\Uuid\Uuid;

/**
 * A dummy Feedback class which allows the preview to emulate the Feedback functionality
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DummyFeedback extends Feedback
{
    // Properties
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';

    /**
     *
     * @see \libraries\storage\DataClass::create()
     */
    public function create(): bool
    {
        $this->set_id(Uuid::uuid4());

        return PreviewStorage::getInstance()->create_feedback($this);
    }

    /**
     *
     * @see \libraries\storage\DataClass::delete()
     */
    public function delete(): bool
    {
        return PreviewStorage::getInstance()->delete_feedback($this);
    }

    /**
     *
     * @return int
     */
    public function get_content_object_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_CONTENT_OBJECT_ID));
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_portfolio_preview_feedback';
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_content_object_id($content_object_id)
    {
        $this->setDefaultProperty(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     *
     * @see \libraries\storage\DataClass::update()
     */
    public function update(): bool
    {
        return PreviewStorage::getInstance()->update_feedback($this);
    }
}