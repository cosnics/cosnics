<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Reporting;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\ContentObject\PeerAssessment\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class UserResultReportingBlock extends ReportingBlock
{

    protected $user;

    protected $attempt;

    public function __construct($parent, $user, $attempt)
    {
        parent::__construct($parent, false);
        
        $this->user = $user;
        $this->attempt = $attempt;
    }

    public static function factory($parent, $user, $attempt)
    {
        $processor = $parent->get_parent()->get_root_content_object()->get_scale();
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($processor)->upperCamelize() .
             'UserResultReportingBlock';
        return new $class($parent, $user, $attempt);
    }

    public function count_data()
    {
        return null;
    }

    public function get_title()
    {
        $peer_assessment = $this->get_parent()->get_peer_assessment();
        return Translation::get('Score') . ' ' . ' ' . $this->user->get_lastname() . ' ' . $this->user->get_firstname() .
             ' ' . $this->attempt->get_title();
    }

    public function retrieve_data()
    {
        return $this->compose_data();
    }

    public function get_available_displaymodes()
    {
        return array(Html::VIEW_TABLE);
    }

    public function get_application()
    {
        return 'content_object_peer_assessment';
    }

    public function get_views()
    {
        return array(Html::VIEW_TABLE);
    }

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }
}
