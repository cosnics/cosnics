<?php
namespace Chamilo\Configuration\Form;

use Chamilo\Configuration\Form\Form\ExecuteForm;
use Chamilo\Configuration\Form\Service\FormService;
use Chamilo\Configuration\Form\Storage\DataClass\Instance;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Configuration\Form
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Executer
{

    protected FormService $formService;

    protected StringUtilities $stringUtilities;

    protected Translator $translator;

    public function __construct(FormService $formService, Translator $translator, StringUtilities $stringUtilities)
    {
        $this->formService = $formService;
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
    }

    /**
     * @throws \QuickformException
     */
    public function run(Application $application, string $name, ?string $title = null)
    {
        $translator = $this->getTranslator();

        $title = $title ?: $translator->trans(
            $this->getStringUtilities()->createString($name)->upperCamelize()->toString(), $application::CONTEXT
        );

        $form = new ExecuteForm(
            $this->getForm($application, $name), $application->get_url(), $application->get_user(), $title
        );

        if ($form->validate())
        {
            $success = $form->update_values();
            $application->redirectWithMessage(
                $translator->trans($success ? 'DynamicFormExecuted' : 'DynamicFormNotExecuted', [], Manager::CONTEXT),
                !$success
            );
        }
        else
        {
            $html = [];

            $html[] = $application->render_header();
            $html[] = $form->render();
            $html[] = $application->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function getForm(Application $application, string $name): ?Instance
    {
        return $this->getFormService()->retrieveInstanceForContextAndName($application::CONTEXT, $name);
    }

    public function getFormService(): FormService
    {
        return $this->formService;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
