<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RubricResultTargetUser
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\RubricResultRepository")
 *
 * @ORM\Table(
 *      name="repository_rubric_result_target_user",
 *      indexes={
 *      }
 * )
 */
class RubricResultTargetUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var RubricResult
     *
     * @ORM\Column(name="rubric_result_id", type="guid")
     */
    protected $rubricResultGUID;

    /**
     * @var int
     *
     * @ORM\Column(name="target_user_id", type="integer")
     */
    protected $targetUserId;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return RubricResultTargetUser
     */
    public function setId(int $id): RubricResultTargetUser
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getRubricResultGUID(): ?string
    {
        return $this->rubricResultGUID;
    }

    /**
     * @param string $rubricResultGUID
     *
     * @return RubricResultTargetUser
     */
    public function setRubricResultGUID(string $rubricResultGUID): RubricResultTargetUser
    {
        $this->rubricResultGUID = $rubricResultGUID;

        return $this;
    }

    /**
     * @return int
     */
    public function getTargetUserId(): ?int
    {
        return $this->targetUserId;
    }

    /**
     * @param int $targetUserId
     *
     * @return RubricResultTargetUser
     */
    public function setTargetUserId(int $targetUserId): RubricResultTargetUser
    {
        $this->targetUserId = $targetUserId;

        return $this;
    }
}
