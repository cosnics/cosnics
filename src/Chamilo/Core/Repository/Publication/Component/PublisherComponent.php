<?php
namespace Chamilo\Core\Repository\Publication\Component;

use Chamilo\Core\Repository\Publication\Form\PublicationTargetForm;
use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Core\Repository\Publication\PublicationProcessor;

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
            var_dump($publicationTargetForm->exportValues());
            exit;

            $publicationProcessor = new PublicationProcessor($this, $publicationTargetForm->exportValues());

            return $publicationProcessor->run();
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
}
