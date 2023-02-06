<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use Chamilo\Core\User\Storage\DataClass\User;
use JMS\Serializer\Annotation\Type;

/**
 * Class GradeBookUserJSONModel
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookUserJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $lastName;

    /**
     * GradeBookUserJSONModel constructor.
     *
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(int $id, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @param User $user
     *
     * @return GradeBookUserJSONModel
     */
    public static function fromUser(User $user): GradeBookUserJSONModel
    {
        return new self($user->getId(), $user->get_firstname(), $user->get_lastname());
    }
}
