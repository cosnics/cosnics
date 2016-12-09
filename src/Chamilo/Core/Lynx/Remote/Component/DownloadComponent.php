<?php
namespace Chamilo\Core\Lynx\Remote\Component;

use Chamilo\Core\Lynx\Remote\DataClass\Package;
use Chamilo\Core\Lynx\Remote\DataManager;
use Chamilo\Core\Lynx\Remote\Manager;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

class DownloadComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        try
        {
            $package = DataManager::retrieve_by_id(
                Package::class_name(), 
                (int) Request::get(self::PARAM_PACKAGE_ID));
        }
        catch (\Exception $exception)
        {
            $this->redirect($exception->getMessage(), true, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        
        if ($package->is_downloadable())
        {
            $source_filename = $package->get_source_filename();
            $file_properties = FileProperties::from_url($source_filename);
            
            if ($file_properties->get_path())
            {
                $temp_path = Path::getInstance()->getTemporaryPath(self::context()) . 'download/' . $package->get_id();
                
                if (! file_exists($temp_path) && Filesystem::create_dir(dirname($temp_path)))
                {
                    if (copy($source_filename, $temp_path))
                    {
                        $file_compression = Filecompression::factory();
                        $path = $file_compression->extract_file($temp_path, false);
                        
                        if ($path)
                        {
                            Filesystem::remove($temp_path);
                            if (Filesystem::recurse_move(
                                $path, 
                                Path::getInstance()->namespaceToFullPath($package->get_context())))
                            {
                                $this->redirect(
                                    Translation::get('PackageDownloaded'), 
                                    false, 
                                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
                            }
                            else
                            {
                                $this->redirect(
                                    Translation::get('PackageNotMoved'), 
                                    true, 
                                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
                            }
                        }
                        else
                        {
                            $this->redirect(
                                Translation::get('ZipExtractFailed'), 
                                true, 
                                array(self::PARAM_ACTION => self::ACTION_BROWSE));
                        }
                    }
                    else
                    {
                        $this->redirect(
                            Translation::get(
                                'PackageNotDownloadable', 
                                array('TYPE' => Translation::get('SourceFileNotCopied'))), 
                            true, 
                            array(self::PARAM_ACTION => self::ACTION_BROWSE));
                    }
                }
            }
            else
            {
                $this->redirect(
                    Translation::get('PackageNotDownloadable', array('TYPE' => $source_filename)), 
                    true, 
                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
            }
        }
        else
        {
            $this->redirect(
                Translation::get('PackageNotDownloadable', array('TYPE' => $package->get_name())), 
                true, 
                array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
    }
}
