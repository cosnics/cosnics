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

    /**
     *
     * @var string
     */
    private $hash;

    /**
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     *
     * @return string
     */
    public function hash()
    {
        if (! $this->getHash())
        {
            $this->setHash(md5(json_encode($this->getHashParts())));
        }

        return $this->getHash();
    }

    /**
     *
     * @return string[]
     */
    abstract public function getHashParts();
}