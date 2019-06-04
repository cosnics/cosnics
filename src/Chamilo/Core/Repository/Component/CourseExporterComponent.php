<?php

namespace Chamilo\Core\Repository\Component;

use Chamilo\Application\Weblcms\Service\CourseService;
use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\Repository\Form\CourseExporterFormHandler;
use Chamilo\Core\Repository\Form\CourseExporterFormType;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Symfony\Component\Form\FormInterface;

/**
 * This component is a shortcut component that will only be accessible by URL. It will not be accessible in any way
 * through the normal interface. This component gives a shortcut for users to select a course and export all of it's
 * content objects as CPO. This component will be used for users that are migrating from one chamilo to another to have
 * some kind of filter of the contnet objects of a given course. Since this is a hidden component, shortcuts are used
 * that should in no way directly be used when creating similar functionality in the repository itself.
 *
 * @package Chamilo\Core\Repository\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseExporterComponent extends Manager
{

    /**
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    function run()
    {
        $form = $this->buildForm();
        $this->handleForm($form);

        return $this->getTwig()->render(
            'Chamilo\Core\Repository:CourseExporter.html.twig',
            ['HEADER' => $this->render_header(), 'FORM' => $form->createView(), 'FOOTER' => $this->render_footer()]
        );
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function buildForm()
    {
        $form = $this->getForm()->create(CourseExporterFormType::class, [], ['user' => $this->getUser()]);

        return $form;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return bool
     * @throws \Exception
     */
    protected function handleForm(FormInterface $form)
    {
        try
        {
            $formHandler = new CourseExporterFormHandler($this->getPublicationService());
            $formHandler->setUser($this->getUser());

            return $formHandler->handle($form, $this->getRequest());
        }
        catch(UserException $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            throw $ex;
        }
    }

    /**
     * @return CourseService
     */
    protected function getCourseService()
    {
        return $this->getService(CourseService::class);
    }

    /**
     * @return PublicationService
     */
    protected function getPublicationService()
    {
        return $this->getService(PublicationService::class);
    }

}