<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common\Import;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common\ImportImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 * @package Chamilo\Core\Repository\ContentObject\Hotpotatoes\Common\Export
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CpoImportImplementation extends ImportImplementation
{
    public function import()
    {
        /** @var \Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes $hotpotatoes */
        $hotpotatoes = ContentObjectImport::launch($this);

        $temporaryHotpotatoesBasePath = $this->get_controller()->getTemporaryDirectory() . 'hotpotatoes';
        $relativePathInCPO = basename(rtrim(dirname($hotpotatoes->get_path()), '/'));
        $temporaryHotpotatoesPath = $temporaryHotpotatoesBasePath . DIRECTORY_SEPARATOR . $relativePathInCPO;

        $user = $this->get_controller()->get_parameters()->get_user();

        $hotpotatoesWebBasePath = Path::getInstance()->getPublicStoragePath(
                'Chamilo\Core\Repository\ContentObject\Hotpotatoes'
            ) . $user. '/';

        $relativePathFolder = dirname($hotpotatoes->get_path());
        $hotpotatoesWebPath = Filesystem::create_unique_name($hotpotatoesWebBasePath . $relativePathFolder);

        $newRelativePathFolder = str_replace($hotpotatoesWebBasePath, '', $hotpotatoesWebPath);

        if($newRelativePathFolder != $relativePathFolder)
        {
            $hotpotatoes->set_path(str_replace($relativePathFolder, $newRelativePathFolder, $hotpotatoes->get_path()));
            $hotpotatoes->update();
        }

        Filesystem::recurse_move($temporaryHotpotatoesPath, $hotpotatoesWebPath);

        return $hotpotatoes;
    }

    public function post_import()
    {

    }
}
