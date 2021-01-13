<?php

namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Common\Import\Factory\ImportFactories;
use Chamilo\Core\Repository\Common\Import\Factory\ImportFactoryInterface;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Core\Repository\Common\Import
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectImportService
{
    const PARAM_IMPORT_TYPE = 'import_type';

    /**
     *
     * @var \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    private $form;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var integer[]
     */
    private $contentObjectIds;

    /**
     * @var ImportFactoryInterface
     */
    protected $importFactory;

    /**
     *
     * @param ImportFactoryInterface $importFactory
     * @param string $type
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct(
        ImportFactoryInterface $importFactory, $type, WorkspaceInterface $workspace, Application $application
    )
    {
        $this->type = $type;
        $this->workspace = $workspace;
        $this->application = $application;
        $this->importFactory = $importFactory;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function setWorkspace(WorkspaceInterface $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Form\ContentObjectImportForm
     * @throws \Exception
     */
    public function getForm()
    {
        if (!isset($this->form))
        {
            $importFormParameters = new ImportFormParameters(
                $this->getType(),
                $this->getWorkspace(),
                $this->getApplication(),
                $this->getApplication()->get_url(array(self::PARAM_IMPORT_TYPE => $this->getType()))
            );

            //$this->form = ContentObjectImportForm::factory($importFormParameters);
            $this->form = $this->importFactory->getImportForm($importFormParameters);
        }

        return $this->form;
    }

    /**
     *
     * @return integer[]
     */
    public function getContentObjectIds()
    {
        return $this->contentObjectIds;
    }

    public function hasFinished()
    {
        if ($this->getForm()->validate())
        {
//            $formProcessorFactory = FormProcessorFactory::getInstance();
//            $formProcessor = $formProcessorFactory->getFormProcessor(
//                $this->getType(),
//                $this->getApplication()->getUser()->getId(),
//                $this->getWorkspace(),
//                $this->getForm()->exportValues(),
//                $this->getApplication()->getRequest()
//            );

            $formProcessor = $this->importFactory->getImportFormProcessor(
                $this->getApplication()->getUser()->getId(),
                $this->getWorkspace(),
                $this->getForm()->exportValues(),
                $this->getApplication()->getRequest()
            );

            $importParameters = $formProcessor->getImportParameters();

//            $controller = ContentObjectImportController::factory($importParameters);
            $controller = $this->importFactory->getImportController($importParameters);
            $this->contentObjectIds = $controller->run();

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @return string
     */
    public function renderForm()
    {
        return $this->getForm()->toHtml();
    }
}
