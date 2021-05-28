<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Connector;
use Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type\Feeder;

class FeederForm extends ConfigurationForm
{

    /**
     *
     * @var ContentObjectPublicationService
     */
    protected $contentObjectPublicationService;

    /**
     *
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param boolean $hasStaticTitle
     */
    public function __construct(Block $block, $hasStaticTitle)
    {
        $this->contentObjectPublicationService = new ContentObjectPublicationService(
            new ContentObjectPublicationRepository(new PublicationRepository()));
        
        parent::__construct($block, $hasStaticTitle);
    }

    /**
     *
     * @see \Chamilo\Core\Home\Form\ConfigurationForm::addSettings()
     */
    public function addSettings()
    {
        $connector = new Connector();
        
        $this->addElement(
            'select', 
            Feeder::CONFIGURATION_OBJECT_ID, 
            Translation::get('UseObject'), 
            $connector->get_rss_feed_objects(), 
            array('class' => 'form-control'));
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        $defaults = [];
        
        $contentObjectPublication = $this->contentObjectPublicationService->getFirstContentObjectPublicationForElement(
            $this->getBlock());
        
        if ($contentObjectPublication)
        {
            $defaults[Feeder::CONFIGURATION_OBJECT_ID] = $contentObjectPublication->get_content_object_id();
        }
        
        parent::setDefaults($defaults, $filter);
    }
}