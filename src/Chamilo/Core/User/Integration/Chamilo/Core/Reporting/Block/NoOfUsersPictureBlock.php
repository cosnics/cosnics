<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

class NoOfUsersPictureBlock extends Block
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $users = DataManager::retrieves(
            User::class,
            new DataClassRetrievesParameters());
        $picturetext = Translation::get('Picture');
        $nopicturetext = Translation::get('NoPicture');
        $picture[$picturetext] = 0;
        $picture[$nopicturetext] = 0;
        
        foreach($users as $user)
        {
            if ($user->get_picture_uri())
            {
                $picture[$picturetext] ++;
            }
            else
            {
                $picture[$nopicturetext] ++;
            }
        }
        
        $reporting_data->set_categories(array(Translation::get('Picture'), Translation::get('NoPicture')));
        $reporting_data->set_rows(array(Translation::get('Count')));
        
        $reporting_data->add_data_category_row(
            Translation::get('Picture'), 
            Translation::get('Count'), 
            $picture[$picturetext]);
        $reporting_data->add_data_category_row(
            Translation::get('NoPicture'), 
            Translation::get('Count'), 
            $picture[$nopicturetext]);
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(
            Html::VIEW_TABLE, 
            Html::VIEW_STACKED_AREA, 
            Html::VIEW_STACKED_BAR, 
            Html::VIEW_RADAR, 
            Html::VIEW_POLAR, 
            Html::VIEW_3D_PIE, 
            Html::VIEW_PIE, 
            Html::VIEW_RING, 
            Html::VIEW_BAR, 
            Html::VIEW_LINE, 
            Html::VIEW_AREA, 
            Html::VIEW_CSV, 
            Html::VIEW_XLSX, 
            Html::VIEW_XML);
    }
}
