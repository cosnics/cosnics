<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Configuration\Service\PackageContextSequencer;
use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Resource\StylesheetGenerator;

/**
 * @package Chamilo\Libraries\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetComponent extends Manager implements NoAuthenticationSupport, NoVisitTraceComponentInterface
{
    const PARAM_THEME = 'theme';
    const PARAM_TYPE = 'type';

    const TYPE_COMMON = 1;
    const TYPE_VENDOR = 2;

    public function run()
    {
        $platformPackageBundles = PlatformPackageBundles::getInstance();
        $packages = $platformPackageBundles->get_packages();

        var_dump(array_keys($packages));

        var_dump($this->getService(PackageContextSequencer::class)->sequencePackageContexts(array_keys($packages)));
        exit;

        //        $sequencer = new Sequencer(array_keys($packages));
        //        $sortedPackages = $sequencer->run();
        //        var_dump(array_keys($packages), $sortedPackages);
        //        exit;

        switch ($this->getType())
        {
            case self::TYPE_COMMON:
                $this->getStyleSheetGenerator()->runCommon($this->getRequest()->query->get(self::PARAM_THEME));
                break;
            case self::TYPE_VENDOR:
                $this->getStyleSheetGenerator()->runVendor();
                break;
        }
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Resource\StylesheetGenerator
     */
    public function getStyleSheetGenerator()
    {
        return $this->getService(StylesheetGenerator::class);
    }

    public function getType()
    {
        return $this->getRequest()->query->get(self::PARAM_TYPE);
    }
}