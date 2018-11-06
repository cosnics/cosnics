<?php

namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;

/**
 * Interface ContentObjectPublicationModifierInterface
 *
 * @package Chamilo\Core\Repository\Publication\Service
 */
interface PublicationModifierInterface
{
    /**
     * @param integer $publicationIdentifier
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     * @throws \Exception
     */
    public function getContentObjectPublicationAttributes(int $publicationIdentifier);

    /**
     * @param $publicationIdentifier
     *
     * @return bool
     */
    public function deleteContentObjectPublication(int $publicationIdentifier);

    /**
     * @param \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes $publicationAttributes
     *
     * @return boolean
     */
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes);
}