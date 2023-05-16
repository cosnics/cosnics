<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Feedback extends \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ENTRY_ID = 'entry_id';

    /**
     * @param string[] $extendedPropertyNames
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames([self::PROPERTY_ENTRY_ID]);
    }

    /**
     * @return int
     */
    public function getEntryId()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTRY_ID);
    }

    /**
     * @param int $entryId
     */
    public function setEntryId($entryId)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTRY_ID, $entryId);
    }
}
