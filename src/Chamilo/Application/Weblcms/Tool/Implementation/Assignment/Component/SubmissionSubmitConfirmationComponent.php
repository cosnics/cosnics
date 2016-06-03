<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component\SubmissionSubmit\SubmissionSubmitWizardComponent;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Platform\Translation;

/**
 * Shows a big confirmation message to the user when his submission has been succesfull.
 * This component is separate so
 * nothing wrong can happen when a user clicks on the refresh button of the browser
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class SubmissionSubmitConfirmationComponent extends SubmissionSubmitWizardComponent
{

    public function run()
    {
        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->renderConfirmationMessage(
            Translation::getInstance()->getTranslation(
                'SubmissionSubmitConfirmation',
                array(
                    'ASSIGNMENT_TITLE' => $this->getPublication()->get_content_object()->get_title(),
                    'USER_EMAIL' => $this->getUser()->get_email()),
                Manager::context()));

        $parameters = $this->get_parameters();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_BROWSE_SUBMISSIONS;

        $redirect = new Redirect($parameters);

        $button = new Button(
            Translation::getInstance()->getTranslation('ReturnToSubmissionsBrowser', null, Manager::context()),
            null,
            $redirect->getUrl(),
            Button::DISPLAY_ICON_AND_LABEL,
            false,
            'btn-primary');

        $buttonRenderer = new ButtonRenderer($button);

        $html[] = '<div style="text-align:center">' . $buttonRenderer->render() . '</div>';
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the additional parameters
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_PUBLICATION_ID, self::PARAM_SUBMITTER_TYPE, self::PARAM_TARGET_ID);
    }

    // /**
    // * Add an additional breadcrumb to the trail.
    // *
    // * @param $breadcrumbTrail BreadcrumbTrail
    // */
    // public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbTrail)
    // {
    // parent::add_additional_breadcrumbs($breadcrumbTrail);
    //
    // $breadcrumbTrail->add(
    // new Breadcrumb(
    // $this->get_url(
    // array(
    // \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SUBMIT_SUBMISSION
    // )
    // ),
    // Translation::getInstance()->getTranslation('SubmissionSubmitComponent', null, Manager::context())
    // )
    // );
    // }

    /**
     * Returns the selected step index
     *
     * @return bool
     */
    protected function getSelectedStepIndex()
    {
        $indexModifier = $this->allowGroupSubmissions() ? 1 : 0;
        return 1 + $indexModifier;
    }
}