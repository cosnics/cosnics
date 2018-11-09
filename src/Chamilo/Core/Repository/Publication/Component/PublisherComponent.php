<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Form\PublicationTargetForm;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationTargetProcessor;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Core\Repository\Publication\PublicationProcessor;
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

            var_dump($publicationResults);
        }
        else
        {
            $html = array();

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
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID);
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
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), $content_object_ids
        );

        $order_by = array();
        $order_by[] = new OrderBy(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID)
        );

        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_content_objects(
            ContentObject::class_name(), new DataClassRetrievesParameters($condition, null, null, $order_by)
        )->as_array();
    }
}
