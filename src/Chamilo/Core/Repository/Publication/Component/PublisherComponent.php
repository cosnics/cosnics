<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Form\PublicationTargetForm;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\PublicationProcessor;
use Chamilo\Core\Repository\Publication\Service\PublicationResultsRenderer;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Publication\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class PublisherComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $publicationTargetForm = new PublicationTargetForm(
            $this, $this->get_url(
            array(
                \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID => $this->getRequest()->get(
                    \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID
                )
            )
        )
        );

        if ($publicationTargetForm->validate())
        {
            $exportValues = $publicationTargetForm->exportValues();
            $publicationResults = $this->getPublicationTargetProcessor()->processSelectedTargetsFromValues(
                $this->getContainer(), $this->getSelectedContentObjects(), $exportValues[self::WIZARD_TARGET]
            );

            $html[] = $this->render_header();
            $html[] = $this->getPublicationResultsRenderer()->renderPublicationResults($publicationResults);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $publicationTargetForm->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::add_additional_breadcrumbs()
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repository_publisher');
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationResultsRenderer
     */
    public function getPublicationResultsRenderer()
    {
        return $this->getService(PublicationResultsRenderer::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationTargetProcessor
     */
    public function getPublicationTargetProcessor()
    {
        return $this->getService(PublicationTargetProcessor::class);
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]
     */
    public function getSelectedContentObjects()
    {
        $content_object_ids = $this->getRequest()->query->get(
            \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID
        );

        if (!is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $content_object_ids
        );

        $order_by = [];
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID)
        );

        return DataManager::retrieve_content_objects(
            ContentObject::class, new DataClassRetrievesParameters($condition, null, null, $order_by)
        );
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::get_additional_parameters()
     */
    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = \Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID;

        return parent::get_additional_parameters($additionalParameters);
    }
}
