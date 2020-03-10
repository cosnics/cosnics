<?php

namespace Chamilo\Core\Queue\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Queue\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 *  @ORM\Entity(repositoryClass="Chamilo\Core\Queue\Storage\Repository\JobEntityRepository")
 *  @ORM\Table(
 *     name="queue_job_parameter",
 *     indexes={
 *          @ORM\Index(name="qjp_name", columns={"name"}),
 *          @ORM\Index(name="qjp_value", columns={"value"}),
 *          @ORM\Index(name="qjp_name_value", columns={"name", "value"})
 *     }
 * )
 */
class JobParameter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var Job
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\Core\Queue\Storage\Entity\Job", inversedBy="parameters")
     * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
     */
    protected $job;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=20)
     */
    protected $value;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    /**
     * @param \Chamilo\Core\Queue\Storage\Entity\Job $job
     *
     * @return JobParameter
     */
    public function setJob(Job $job): JobParameter
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return JobParameter
     */
    public function setName(string $name): JobParameter
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return JobParameter
     */
    public function setValue(string $value): JobParameter
    {
        $this->value = $value;

        return $this;
    }

}