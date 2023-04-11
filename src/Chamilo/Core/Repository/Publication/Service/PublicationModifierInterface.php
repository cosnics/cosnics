<?php
namespace Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * @package Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface PublicationModifierInterface
{
    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator);

    public function deleteContentObjectPublication(int $publicationIdentifier): bool;

    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes;

    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult;
    
    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool;
}