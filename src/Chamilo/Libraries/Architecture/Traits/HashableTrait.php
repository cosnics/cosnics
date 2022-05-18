<?php
namespace Chamilo\Libraries\Architecture\Traits;

/**
 *
 * @package Chamilo\Libraries\Architecture\Traits
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait HashableTrait
{

    private ?string $hash = null;

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     *
     * @return string[]
     */
    abstract public function getHashParts(): array;

    public function hash(): string
    {
        if (!$this->getHash())
        {
            $this->setHash(md5(json_encode($this->getHashParts())));
        }

        return $this->getHash();
    }
}