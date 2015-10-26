<?php
use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Structure\Page;
require __DIR__ . '/../../Architecture/Bootstrap.php';

\Chamilo\Libraries\Architecture\Bootstrap :: launch();

// $data = array();

// $registrations = Configuration :: registrations();

// foreach ($registrations as $registration_type => $packages)
// {
// foreach ($packages as $blah)
// {
// foreach ($blah as $package)
// {
// $context = $package->get_context();
// $manager_class = $context . '\Manager';

// if (class_exists($manager_class))
// {
// $reflection = new ReflectionClass($manager_class);
// $constants = $reflection->getConstants();

// foreach ($constants as $constant => $value)
// {
// if (substr($constant, 0, 7) == 'ACTION_')
// {
// $component_path = Path :: getInstance()->namespaceToFullPath($context) . 'php/lib/manager/component/' .
// $value . '.class.php';
// if (! file_exists($component_path))
// {
// $data[] = array($context, $constant, $value);
// }
// }
// }
// }
// }
// }
// }

// $action_table = new SortableTableFromArray($data, 0, 600);
// $action_table->setColumnHeader(0, 'context');
// $action_table->setColumnHeader(1, 'constant');
// $action_table->setColumnHeader(2, 'component');

$data = array();

$registrations = Configuration :: registrations();

foreach ($registrations as $registration_type => $packages)
{
    foreach ($packages as $blah)
    {
        foreach ($blah as $package)
        {
            $context = $package->get_context();
            $manager_class = $context . '\Manager';

            if (class_exists($manager_class))
            {
                $reflection = new ReflectionClass($manager_class);
                $constants = $reflection->getConstants();

                $components_path = Path :: getInstance()->namespaceToFullPath($context) . 'php/lib/manager/component/';

                if (is_dir($components_path))
                {
                    $files = Filesystem :: get_directory_content($components_path, Filesystem :: LIST_FILES, false);

                    foreach ($files as $file)
                    {
                        $path_info = pathinfo($file);

                        if ($path_info['extension'] == 'php')
                        {
                            if (strpos($path_info['filename'], '.class') !== false)
                            {
                                $filename = str_replace('.class', '', $path_info['filename']);

                                if (! in_array($filename, $constants))
                                {
                                    $data[] = array($context, $path_info['basename']);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

$component_table = new SortableTableFromArray($data, 0, 600);
$component_table->setColumnHeader(0, 'context');
$component_table->setColumnHeader(1, 'component');

$page = Page :: getInstance();
$page->setViewMode(Page :: VIEW_MODE_HEADERLESS);

echo $page->getHeader()->toHtml();
echo $component_table->toHtml();
echo $page->getFooter()->toHtml();