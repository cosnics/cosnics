<?php
namespace Chamilo\Libraries\Component;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Manager;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Form\Service\FormValidatorHtmlEditorRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class HtmlEditorInstanceComponent extends Manager
{
    protected FormValidatorHtmlEditorRenderer $formValidatorHtmlEditorRenderer;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator, UrlGenerator $urlGenerator,
        FormValidatorHtmlEditorRenderer $formValidatorHtmlEditorRenderer
    )
    {
        parent::__construct($request, $applicationHeaderRenderer, $defaultFooterRenderer, $translator, $urlGenerator);

        $this->formValidatorHtmlEditorRenderer = $formValidatorHtmlEditorRenderer;
    }

    /**
     * @throws \QuickformException
     */
    public function run(?User $currentUser = null): Response
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
        return $this->formValidatorHtmlEditorRenderer;
    }
}