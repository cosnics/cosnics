<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Portfolio\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Feedback extends \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Feedback
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(array(self::PROPERTY_PUBLICATION_ID));
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'portfolio_feedback';
    }

    /**
     *
     * @param int $publication_id
     */
    public function set_publication_id($publication_id)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publication_id);
    }
}