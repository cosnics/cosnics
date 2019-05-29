<?php

namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @package Chamilo\Core\Repository\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseExporterFormHandler extends \Chamilo\Libraries\Format\Form\FormHandler
{
    /**
     * @var \Chamilo\Application\Weblcms\Service\PublicationService
     */
    protected $publicationService;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    protected $user;

    /**
     * CourseExporterFormHandler constructor.
     *
     * @param \Chamilo\Application\Weblcms\Service\PublicationService $publicationService
     */
    public function __construct(\Chamilo\Application\Weblcms\Service\PublicationService $publicationService)
    {
        $this->publicationService = $publicationService;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setUser(\Chamilo\Core\User\Storage\DataClass\User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        if(!$this->user instanceof User)
        {
            throw new \RuntimeException('The user has not been set so the form can not be handled');
        }

        if (!parent::handle($form, $request))
        {
            return false;
        }

        $data = $form->getData();
        $course = $data['course'];

        $contentObjectIds = [];

        $publications = $this->publicationService->getPublicationsByCourse($course);
        foreach ($publications as $publication)
        {
            $contentObjectIds[] = $publication->get_content_object_id();
        }

        $contentObjectIds = array_unique($contentObjectIds);

        $exportParameters = new ExportParameters(
            new PersonalWorkspace($this->user),
            $this->user->getId(),
            ContentObjectExport::FORMAT_CPO,
            $contentObjectIds
        );

        $exporter = ContentObjectExportController::factory($exportParameters);

        $path = $exporter->run();
        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        $response->send();

        exit;

        return true;
    }

    protected function rollBackModel(\Symfony\Component\Form\FormInterface $form)
    {
        // TODO: Implement rollBackModel() method.
    }
}