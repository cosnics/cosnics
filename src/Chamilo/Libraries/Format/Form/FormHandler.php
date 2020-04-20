<?php
namespace Chamilo\Libraries\Format\Form;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package Chamilo\Libraries\Format\Form
 *
 * @author pjbro
 */
abstract class FormHandler
{

    /**
     * @var DataClass
     */
    protected $originalModel;

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     */
    public function handle(FormInterface $form, Request $request): bool
    {
        if (!$request->isMethod('POST'))
        {
            return false;
        }

        $formData = $form->getData();
        $this->originalModel = is_object($formData) ? clone $formData : $formData;

        $form->handleRequest($request);

        if (!$form->isValid())
        {
            $this->rollBackModel($form);

            return false;
        }

        return true;
    }

    abstract protected function rollBackModel(FormInterface $form);
}