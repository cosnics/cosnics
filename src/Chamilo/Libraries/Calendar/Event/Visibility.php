<?php
namespace Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
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

    public function getUser(): ?User
    {
        if (isset($this->user))
        {
            $this->user = DataManager::retrieve_by_id(User::class, (string) $this->getUserId());
        }

        return $this->user;
    }

    public function getUserId(): int
    {
        return $this->getDefaultProperty(self::PROPERTY_USER_ID);
    }

    /**
     * @throws \Exception
     */
    public function setSource(string $source)
    {
        $this->setDefaultProperty(self::PROPERTY_SOURCE, $source);
    }

    /**
     * @throws \Exception
     */
    public function setUserId(int $id)
    {
        $this->setDefaultProperty(self::PROPERTY_USER_ID, $id);
    }
}
