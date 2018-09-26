<?php

namespace Chamilo\Core\Queue\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Queue\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Queue\Storage\Repository\JobEntityRepository")
 * @ORM\Table(
 *     name="queue_job",
 *     indexes={
 *          @ORM\Index(name="qn_date", columns={"date"}),
 *          @ORM\Index(name="qn_status", columns={"status"})
 *     }
 * )
 */
class JobEntity
{
    const STATUS_CREATED = 1;
    const STATUS_SENT_TO_QUEUE = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_SUCCESS = 4;
    const STATUS_FAILED = 5;
    const STATUS_RETRY = 6;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=false, length=1024)
     */
    protected $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, length=2)
     */
    protected $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\JobEntity
     */
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\JobEntity
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\JobEntity
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

}