<?php
namespace Chamilo\Libraries\Protocol\Security\Architecture\Interface;

/**
 * A class implements the <code>Hashable</code> interface to indicate that it can be hashed to a unique MD5 string
 *
 * @package Chamilo\Libraries\Protocol\Security\Architecture\Interface
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface HashableInterface
{
    public function getHash(): ?string;

    /**
     *
     * @return string[]
     */
    public function getHashParts(): array;

    public function hash(): string;

    public function setHash(string $hash);
}
