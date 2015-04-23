<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Form\RequestForm;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager
{

    private $calculator;

    public function run()
    {
        $this->calculator = new Calculator($this->get_user());
        if (! $this->calculator->request_allowed())
        {
            throw new NotAllowedException();
        }

        $request = new Request();
        $request->set_user_id($this->get_user_id());

        $form = new RequestForm($request, $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE)));

        if ($form->validate())
        {
            $values = $form->exportValues();

            $request->set_quota($values[Request :: PROPERTY_QUOTA] * pow(1024, 2));
            $request->set_motivation($values[Request :: PROPERTY_MOTIVATION]);
            $request->set_decision(Request :: DECISION_PENDING);
            $request->set_creation_date(time());

            $success = $request->create();

            $parameters = array();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_BROWSE;

            $this->redirect(
                Translation :: get(
                    $success ? 'ObjectCreated' : 'ObjectNotCreated',
                    array('OBJECT' => Translation :: get('Request')),
                    Utilities :: COMMON_LIBRARIES),
                ($success ? false : true),
                $parameters);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->get_action_bar()->as_html();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $allow_upgrade = PlatformSetting :: get('allow_upgrade', __NAMESPACE__);
        $maximum_user_disk_space = PlatformSetting :: get('maximum_user', __NAMESPACE__);

        if ($this->calculator->upgrade_allowed())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('UpgradeQuota'),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Upgrade'),
                    $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPGRADE))));
        }

        $action_bar->add_tool_action(
            new ToolbarItem(
                Translation :: get('BackToOverview'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Quota', 'Action/Browser'),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE))));

        return $action_bar;
    }
}
