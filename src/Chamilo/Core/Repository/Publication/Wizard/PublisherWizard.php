<?php
namespace Chamilo\Core\Repository\Publication\Wizard;

use Chamilo\Core\Repository\Publication\Wizard\Pages\LocationSelectionPublisherWizardPage;
use Chamilo\Core\Repository\Publication\Wizard\Pages\PublisherWizardDisplay;
use Chamilo\Core\Repository\Publication\Wizard\Pages\PublisherWizardProcess;
use HTML_QuickForm_Controller;

/**
 *
 * @package core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublisherWizard extends HTML_QuickForm_Controller
{

    /**
     *
     * @var PublisherComponent
     */
    private $parent;

    /**
     *
     * @param PublisherComponent $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        parent :: __construct('PublisherWizard', true);
        $this->addPage(new LocationSelectionPublisherWizardPage('page_locations', $this->parent));
        
        $this->addAction('process', new PublisherWizardProcess($this->parent));
        $this->addAction('display', new PublisherWizardDisplay($this->parent));
    }
}
