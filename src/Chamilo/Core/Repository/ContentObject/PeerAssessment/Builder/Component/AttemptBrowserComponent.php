<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class AttemptBrowserComponent extends Manager
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
        $attempts = $this->get_attempts($publication_id);

        // show an error message if no attempts are defined
        if (count($attempts) === 0)
        {
            $this->redirect('', 0, array(self :: PARAM_ACTION => self :: ACTION_EDIT_ATTEMPT));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->render_action_bar();
            $html[] = '<div class="context_info alert alert-warning">' . Translation :: get('AttemptInfoMessage') . '</div>';
            $html[] = $this->render_attempts($attempts);
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    private function render_attempts(array $attempts)
    {
        // TODO date locale doesn't work
        $html = array();

        $image = Theme :: getInstance()->getCommonImagePath('Action/Period');

        // loop through all the attempts and render them
        foreach ($attempts as $a)
        {

            $url = $this->get_url(
                array(self :: PARAM_ACTION => self :: ACTION_EDIT_ATTEMPT, self :: PARAM_ATTEMPT => $a->get_id()));
            $title = '<a href="' . $url . '">' . $a->get_title() . '</a>';
            $description = $a->get_description();
            $info = sprintf(
                Translation :: get('AttemptInfoDate'),
                date('d/m/Y', $a->get_start_date()),
                date('d/m/Y', $a->get_end_date()));
            $actions = $this->render_toolbar($a);
            $level = $level == 1 ? 2 : 1;

            $hidden = $a->get_hidden();

            $html[] = \Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager :: render_list_item(
                $title,
                $description,
                $info,
                $actions,
                $level,
                $hidden,
                $image);
        }

        return implode(PHP_EOL, $html);
    }

    private function render_toolbar($attempt)
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit'),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_EDIT_ATTEMPT,
                        self :: PARAM_ATTEMPT => $attempt->get_id())),
                ToolbarItem :: DISPLAY_ICON));

        if ($attempt->get_hidden())
        {
            $label = Translation :: get('Invisible');
            $image = 'Action/VisibleNa';
        }
        else
        {
            $label = Translation :: get('Visible');
            $image = 'Action/Visible';
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_DELETE_ATTEMPT,
                        self :: PARAM_ATTEMPT => $attempt->get_id())),
                ToolbarItem :: DISPLAY_ICON,
                true));

        return $toolbar->as_html();
    }

    public function render_action_bar()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $buttonToolbar = $this->buttonToolbarRenderer->getButtonToolBar();
        $commonActions = new ButtonGroup();
        $commonActions->addButton(
            new Button(
                Translation :: get('CreateAttempt'),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ATTEMPT))));

        $buttonToolbar->addButtonGroup($commonActions);

        return $this->buttonToolbarRenderer->render();
    }
}
