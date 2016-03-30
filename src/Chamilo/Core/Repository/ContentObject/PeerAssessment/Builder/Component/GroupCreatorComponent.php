<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Form\PeerAssessmentGroupForm;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;

class GroupCreatorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->is_allowed(self :: EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $publication_id = $this->get_publication_id();

        $form = new PeerAssessmentGroupForm($this, null, $this->get_url());

        if ($form->validate())
        {
            $group = $this->get_group();
            $group->validate_parameters($form->exportValues());
            $group->set_publication_id($publication_id);
            $group->save();

            $form->set_group_id($group->get_id());

            $form->update_group_memberships();

            $enroll_errors = $form->get_enroll_errors();

            $message = ! is_null($enroll_errors) ? $enroll_errors : null;

            // redirect back to the group overview page
            $this->redirect($message, false, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS));
        }

        $html = array();

        $html[] = parent :: render_header();
        $html[] = $this->render_action_bar();
        $html[] = $form->toHtml();
        $html[] = parent :: render_footer();

        return implode(PHP_EOL, $html);
    }

    protected function render_action_bar()
    {
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        parent :: add_additional_breadcrumbs($breadcrumbtrail);

        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_GROUPS)),
                Translation :: get('GroupManagement')));
    }
}
