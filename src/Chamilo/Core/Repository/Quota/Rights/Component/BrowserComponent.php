<?php
namespace Chamilo\Core\Repository\Quota\Rights\Component;

use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;

class BrowserComponent extends Manager implements TableSupport
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getRightsService()->canUserSetRightsForQuotaRequests($this->getUser()))
        {
            throw new NotAllowedException();
        }

        $table = new EntityTable($this, $this->getTranslator(), $this->getRightsService());

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_BROWSE), $table->render());
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /*
     * (non-PHPdoc) @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        // TODO Auto-generated method stub
    }
}
