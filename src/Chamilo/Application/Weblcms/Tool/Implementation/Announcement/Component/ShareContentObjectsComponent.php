<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Announcement\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;

class ShareContentObjectsComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $publication_id = Request :: get(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve(
            ContentObjectPublication :: class_name(),
            $publication_id);

        if ($publication->get_content_object()->get_owner_id() != $this->get_user_id())
        {
            throw new NotAllowedException();
        }

        $factory = new ApplicationFactory(
            $this->getRequest(),
            \Chamilo\Application\Weblcms\Tool\Action\Manager :: context(),
            $this->get_user(),
            $this);
        return $factory->run();
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID);
    }
}
