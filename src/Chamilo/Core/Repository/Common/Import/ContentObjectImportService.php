<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * @package Chamilo\Core\Repository\Common\Import
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectImportService
{
    public const PARAM_IMPORT_TYPE = 'import_type';

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * @var int
     */
    private $contentObjectIds;

    /**
     * @var \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    private $form;

    /**
     * @var string
     */
    private $type;

    private Workspace $workspace;

    /**
     * @param string $type
     */
    public function __construct($type, Workspace $workspace, Application $application)
    {
        $this->type = $type;
        $this->workspace = $workspace;
        $this->application = $application;
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return int
     */
    public function getContentObjectIds()
    {
        return $this->contentObjectIds;
    }

    /**
     * @return \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    public function getForm()
    {
        if (!isset($this->form))
        {
            $importFormParameters = new ImportFormParameters(
                $this->getType(), $this->getWorkspace(), $this->getApplication(),
                $this->getApplication()->get_url([self::PARAM_IMPORT_TYPE => $this->getType()])
            );

            $this->form = ContentObjectImportForm::factory($importFormParameters);
        }

        return $this->form;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    public function hasFinished()
    {
        if ($this->getForm()->validate())
        {
            $formProcessorFactory = FormProcessorFactory::getInstance();
            $formProcessor = $formProcessorFactory->getFormProcessor(
                $this->getType(), $this->getApplication()->getUser()->getId(), $this->getWorkspace(),
                $this->getForm()->exportValues(), $this->getApplication()->getRequest()
            );

            $importParameters = $formProcessor->getImportParameters();

            $controller = ContentObjectImportController::factory($importParameters);
            $this->contentObjectIds = $controller->run();

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        return $this->getForm()->toHtml();
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function setWorkspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }
}