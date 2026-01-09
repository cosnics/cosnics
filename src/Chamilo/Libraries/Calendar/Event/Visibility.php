<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Libraries\Calendar\Event
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Visibility extends DataClass
{
    public const CONTEXT = 'Chamilo\Libraries\Calendar';

    public const PROPERTY_SOURCE = 'source';
    public const PROPERTY_USER_ID = 'user_id';

    private ?User $user;

    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        $extendedPropertyNames[] = self::PROPERTY_USER_ID;
        $extendedPropertyNames[] = self::PROPERTY_SOURCE;

        return parent::getDefaultPropertyNames($extendedPropertyNames);
    }

    public function getSource(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_SOURCE);
    }

    public function getUserId(): string
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    public function setSource(string $source): static
    {
        $this->setDefaultProperty(self::PROPERTY_SOURCE, $source);

        return $this;
    }

    public function setUserId(string $id): static
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $id);

        return $this;
    }
}
