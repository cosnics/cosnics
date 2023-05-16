<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service;

use Chamilo\Core\Repository\Publication\Domain\PublicationResult;
use Chamilo\Core\Repository\Publication\Domain\PublicationTarget;
use Chamilo\Core\Repository\Publication\Service\PublicationModifierInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Form\FormValidator;
use Exception;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationModifier implements PublicationModifierInterface
{

    protected PublicationService $publicationService;

    private Translator $translator;

    public function __construct(Translator $translator, PublicationService $publicationService)
    {
        $this->translator = $translator;
        $this->publicationService = $publicationService;
    }

    public function addContentObjectPublicationAttributesElementsToForm(FormValidator $formValidator)
    {
    }

    public function deleteContentObjectPublication(int $publicationIdentifier): bool
    {
        try
        {
            $this->getPublicationService()->deleteContentObjectPublicationsByTreeNodeDataId(
                $publicationIdentifier
            );

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function getContentObjectPublicationAttributes(int $publicationIdentifier): Attributes
    {
        return $this->getPublicationService()->getContentObjectPublicationAttributesForTreeNodeData(
            $publicationIdentifier
        );
    }

    public function getPublicationService(): PublicationService
    {
        return $this->publicationService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function publishContentObject(
        ContentObject $contentObject, PublicationTarget $publicationTarget, array $options = []
    ): PublicationResult
    {
        return new PublicationResult(
            PublicationResult::STATUS_FAILURE, $this->getTranslator()->trans(
            'PublicationImpossible', ['%CONTENT_OBJECT%' => $contentObject->get_title()],
            'Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository'
        )
        );
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    public function updateContentObjectPublicationContentObjectIdentifier(Attributes $publicationAttributes): bool
    {
        try
        {
            $this->getPublicationService()->updateContentObjectIdInTreeNodeData(
                $publicationAttributes->getId(), $publicationAttributes->get_content_object_id()
            );

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }
}