<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Form\TermsConditionsForm;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

class TermsConditionsEditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        // TODO check right to see/edit this!!!
        $user = null;
        // todo: get user

        $form = new TermsConditionsForm($user, $this->get_url(), TermsConditionsForm :: TYPE_EDIT);

        if ($form->validate())
        {
            $success = $form->edit_terms_conditions();
            if ($success == 1)
            {
                $redirect = new Redirect();
                $redirect->toUrl();
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail = new BreadCrumbTrail(false);
        $breadcrumbtrail->add_help('terms_conditions');
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
