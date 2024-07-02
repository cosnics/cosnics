<?php
namespace Chamilo\Core\Repository\Publication\Publisher\Component;

use Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport;
use Chamilo\Core\Repository\Publication\Publisher\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Architecture\Traits\ViewerTrait;
use Chamilo\Core\Repository\Viewer\ViewerInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeaderRenderer;
use Chamilo\Libraries\Storage\Parameters\RetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use RuntimeException;

/**
 * Publisher component that executes the repo viewer, calls the publication form and executes the publisher handler
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublisherComponent extends Manager implements ViewerInterface, BreadcrumbLessComponentInterface
{
    use ViewerTrait;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->getParentApplication() instanceof PublisherSupport)
        {
            throw new RuntimeException(
                'To use the publisher application the parent application must implement the ' .
                '"Chamilo\Core\Repository\Publication\Publisher\Interfaces\PublisherSupport" interface'
            );
        }

        if (!$this->isAnyObjectSelectedInViewer())
        {
            $applicationConfiguration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);

            $applicationConfiguration->set(\Chamilo\Core\Repository\Viewer\Manager::SETTING_BREADCRUMBS_DISABLED, true);

            return $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::CONTEXT, $applicationConfiguration
            )->run();
        }

        $selectedContentObjects = $this->getSelectedContentObjects();
        $form = $this->getPublicationForm($selectedContentObjects);

        if ($form instanceof FormValidator)
        {
            if ($form->validate())
            {
                $this->getPublicationHandler()->publish($selectedContentObjects);
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $this->getPublicationHandler()->publish($selectedContentObjects);
        }
    }

    /**
     * Returns a list of selected content objects
     */
    protected function getSelectedContentObjects()
    {
        $selectedContentObjectIds = $this->getObjectsSelectedInviewer();

        if (!empty($selectedContentObjectIds) && !is_array($selectedContentObjectIds))
        {
            $selectedContentObjectIds = [$selectedContentObjectIds];
        }

        if (count($selectedContentObjectIds) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                $selectedContentObjectIds
            );
            $parameters = new RetrievesParameters(condition: $condition);

            return DataManager::retrieve_active_content_objects(
                ContentObject::class, $parameters
            );
        }

        return [];
    }

    /**
     * Helper functionality
     *
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = [])
    {
        return Translation::getInstance()->get($variable, $parameters, Manager::CONTEXT);
    }

    /**
     * Returns the title for the first step wizard
     *
     * @return string
     */
    protected function getWizardFirstStepTitle()
    {
        $action = $this->getRequest()->getFromRequestOrQuery(\Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION);
        switch ($action)
        {
            case \Chamilo\Core\Repository\Viewer\Manager::ACTION_CREATOR :
                return $this->getTranslation('FirstStepCreate');
            case \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER :
                return $this->getTranslation('FirstStepBrowseInWorkspaces');
            case \Chamilo\Core\Repository\Viewer\Manager::ACTION_IMPORTER :
                return $this->getTranslation('FirstStepImport');
            default :
                return $this->getTranslation('FirstStepBrowseInWorkspaces');
        }
    }

    /**
     * Overwrite render header to add the wizard
     *
     * @return string
     */
    public function render_header(string $pageTitle = ''): string
    {
        $html = [];
        $html[] = parent::render_header($pageTitle);

        $wizardHeader = new WizardHeader();
        $wizardHeader->setStepTitles(
            [$this->getWizardFirstStepTitle(), $this->getTranslation('SecondStepPublish')]
        );

        $selectedStepIndex = $this->isAnyObjectSelected() ? 1 : 0;
        $wizardHeader->setSelectedStepIndex($selectedStepIndex);

        $wizardHeaderRenderer = new WizardHeaderRenderer($wizardHeader);

        $html[] = $wizardHeaderRenderer->render();

        return implode(PHP_EOL, $html);
    }

}
