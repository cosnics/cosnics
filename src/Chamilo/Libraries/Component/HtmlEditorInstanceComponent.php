<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Libraries\Manager;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HtmlEditorInstanceComponent extends Manager
{
    /**
     * @throws \QuickformException
     */
    public function run(): Response
    {
        $request = $this->getRequest();
        $name = $request->request->get('name');
        $label = $request->request->get('label');
        $options = $request->request->get('options');
        $attributes = $request->request->get('attributes');

        $options = str_replace('\"', '"', $options);
        $options = json_decode($options, true);

        $attributes = str_replace('\"', '"', $attributes);
        $attributes = json_decode($attributes, true);

        return new Response(
            $this->getFormValidatorHtmlEditorRenderer()->renderHtmlEditor($name, $label, $options, $attributes)
        );
    }

    public function getFormValidatorHtmlEditorRenderer(): FormValidatorHtmlEditorRenderer
    {
        return $this->getService(FormValidatorHtmlEditorRenderer::class);
    }
}