<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Form\PublicationTargetForm;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationResultsRenderer;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Publication\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class PublisherComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     *
     * @throws \QuickformException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws NoObjectSelectedException
     * @throws NotAllowedException
     */
    public function run()
    {
        $publicationTargetForm = new PublicationTargetForm(
            $this, $this->get_url(
            [
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $this->getRequest()->getFromRequestOrQuery(
                    \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID
                )
            ]
        )
        );

        if ($publicationTargetForm->validate())
        {
            $exportValues = $publicationTargetForm->exportValues();
            $publicationResults = $this->getPublicationTargetProcessor()->processSelectedTargetsFromValues(
                $this->getSelectedContentObjects(), $exportValues[self::WIZARD_TARGET]
            );

            $html[] = $this->renderHeader();
            $html[] = $this->getPublicationResultsRenderer()->renderPublicationResults($publicationResults);
        }
        else
        {
            $html = [];

            $html[] = $this->renderHeader();
            $html[] = $publicationTargetForm->render();
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @see \Chamilo\Libraries\Architecture\Application\Application::getAdditionalParameters()
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getPublicationResultsRenderer(): PublicationResultsRenderer
    {
        return $this->getService(PublicationResultsRenderer::class);
    }

    public function getPublicationTargetProcessor(): PublicationTargetProcessor
    {
        return $this->getService(PublicationTargetProcessor::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Storage\DataClass\ContentObject>
     */
    public function getSelectedContentObjects(): ArrayCollection
    {
        $content_object_ids = $this->getRequest()->query->get(
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID
        );

        if (!is_array($content_object_ids))
        {
            $content_object_ids = [$content_object_ids];
        }

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $content_object_ids
        );

        $orderBy = OrderBy::generate(ContentObject::class, ContentObject::PROPERTY_ID);

        return DataManager::retrieve_content_objects(
            ContentObject::class, new DataClassRetrievesParameters($condition, null, null, $orderBy)
        );
    }
}
