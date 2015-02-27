<?php
namespace Chamilo\Libraries\Utilities\Various;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;

require_once __DIR__ . '/../../Architecture/Bootstrap.php';
\Chamilo\Libraries\Architecture\Bootstrap :: getInstance()->setup();

// $files = Filesystem :: get_directory_content(Path :: getInstance()->getBasePath());

$files = array(
    'E:\Apache\chamilo/application\alexia\php\package\package.info',
    'E:\Apache\chamilo/application\assessment\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\assessment\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\application\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\application\right\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\context\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\rights\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\role\entitlement\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\role\entity\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\role\php\package\package.info',
    'E:\Apache\chamilo/application\atlantis\user_group\php\package\package.info',
    'E:\Apache\chamilo/application\cache\php\package\package.info',
    'E:\Apache\chamilo/application\cas_user\account\php\package\package.info',
    'E:\Apache\chamilo/application\cas_user\php\package\package.info',
    'E:\Apache\chamilo/application\cas_user\rights\php\package\package.info',
    'E:\Apache\chamilo/application\cas_user\service\php\package\package.info',
    'E:\Apache\chamilo/application\cda\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\data_source\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\instance\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\advice\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\advice\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\career\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\career\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\cas\implementation\doctrine\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\cas\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\course\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\course\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\course_results\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\course_results\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\employment\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\employment\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\enrollment\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\enrollment\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\exemption\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\exemption\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\faculty\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\faculty\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\faculty_info\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\faculty_info\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\group\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\group\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\group_user\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\group_user\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\person\implementation\chamilo\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\person\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\photo\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\photo\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\profile\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\profile\implementation\chamilo\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\profile\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\student_materials\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\student_materials\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\student_year\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\student_year\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\teaching_assignment\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\teaching_assignment\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training_info\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training_info\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training_results\implementation\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\module\training_results\php\package\package.info',
    'E:\Apache\chamilo/application\discovery\php\package\package.info',
    'E:\Apache\chamilo/application\distribute\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_apple\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_helpdesk\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\atlantis\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\bamaflex\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\cas\data\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\cas\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\cas\storage\php\package\package.info',
    'E:\Apache\chamilo/application\ehb_sync\php\package\package.info',
    'E:\Apache\chamilo/application\elude\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\elude\php\package\package.info',
    'E:\Apache\chamilo/application\forum\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\forum\php\package\package.info',
    'E:\Apache\chamilo/application\gutenberg\php\package\package.info',
    'E:\Apache\chamilo/application\handbook\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\handbook\php\package\package.info',
    'E:\Apache\chamilo/application\internship_organizer\php\package\package.info',
    'E:\Apache\chamilo/application\laika\php\package\package.info',
    'E:\Apache\chamilo/application\laika\rights\php\package\package.info',
    'E:\Apache\chamilo/application\linker\php\package\package.info',
    'E:\Apache\chamilo/application\package\php\package\package.info',
    'E:\Apache\chamilo/application\peer_assessment\php\package\package.info',
    'E:\Apache\chamilo/application\personal_calendar\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\personal_calendar\php\package\package.info',
    'E:\Apache\chamilo/application\personal_messenger\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\personal_messenger\php\package\package.info',
    'E:\Apache\chamilo/application\photo_gallery\php\package\package.info',
    'E:\Apache\chamilo/application\phrases\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\phrases\php\package\package.info',
    'E:\Apache\chamilo/application\portfolio\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\portfolio\php\package\package.info',
    'E:\Apache\chamilo/application\profiler\php\package\package.info',
    'E:\Apache\chamilo/application\reservations\integrationcore\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\reservations\php\package\package.info',
    'E:\Apache\chamilo/application\search_portal\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\search_portal\php\package\package.info',
    'E:\Apache\chamilo/application\survey\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\course\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\course_type\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\integration\core\reporting\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\request\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\request\rights\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\announcement\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\appointment\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\assessment\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\assignment\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\blog\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\calendar\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\chat\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_copier\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_deleter\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_exporter\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_group\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_importer\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_sections\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_settings\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\course_truncater\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\description\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\document\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\ects\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\ehb_photo\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\ephorus\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\forum\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\geolocation\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\glossary\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\home\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\learning_path\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\link\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\note\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\peer_assessment\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\perception\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\reporting\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\rights\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\search\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\streaming_video\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\user\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\video_conferencing\php\package\package.info',
    'E:\Apache\chamilo/application\weblcms\tool\wiki\php\package\package.info',
    'E:\Apache\chamilo/application\wiki\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\category_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\dynamic_form_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\email_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\bitbucket\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\box\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\cmis\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\dailymotion\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\dropbox\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\eol\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\europeana\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\fedora\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\flickr\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\google_docs\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\hq23\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\matterhorn\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\mediamosa\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\photobucket\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\picasa\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\qwiki\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\scribd\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\slideshare\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\soundcloud\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\vimeo\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\wikimedia\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\wikipedia\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\implementation\youtube\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\external_repository_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\feedback_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\invitation_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\new_feedback_manager\php\package\package.info',
    'E:\Apache\chamilo/core/\rights\editor\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\reporting_viewer\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\rights_editor_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\validation_manager\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\video_conferencing_manager\implementation\bbb\php\package\package.info',
    'E:\Apache\chamilo/common\extensions\video_conferencing_manager\php\package\package.info',
    'E:\Apache\chamilo/common\libraries\php\package\package.info',
    'E:\Apache\chamilo/core\admin\announcement\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/core\admin\announcement\php\package\package.info',
    'E:\Apache\chamilo/core\admin\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/core\admin\integration\core\reporting\php\package\package.info',
    'E:\Apache\chamilo/core\admin\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/core\admin\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/core\admin\language\php\package\package.info',
    'E:\Apache\chamilo/core\admin\php\package\package.info',
    'E:\Apache\chamilo/core\context_linker\php\package\package.info',
    'E:\Apache\chamilo/core\help\php\package\package.info',
    'E:\Apache\chamilo/core\home\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/core\home\php\package\package.info',
    'E:\Apache\chamilo/core\install\php\package\package.info',
    'E:\Apache\chamilo/core\lynx\manager\php\package\package.info',
    'E:\Apache\chamilo/core\lynx\php\package\package.info',
    'E:\Apache\chamilo/core\lynx\remote\php\package\package.info',
    'E:\Apache\chamilo/core\lynx\source\php\package\package.info',
    'E:\Apache\chamilo/core\menu\php\package\package.info',
    'E:\Apache\chamilo/core\metadata\php\package\package.info',
    'E:\Apache\chamilo/core\migration\php\package\package.info',
    'E:\Apache\chamilo/group\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/group\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/group\php\package\package.info',
    'E:\Apache\chamilo/reporting\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/reporting\php\package\package.info',
    'E:\Apache\chamilo/reporting\viewer\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\adaptive_assessment\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\adaptive_assessment_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\announcement\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_match_numeric_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_match_text_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_matching_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_matrix_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_multiple_choice_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_open_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_rating_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assessment_select_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\assignment\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\bbb_meeting\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\blog\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\blog_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\bookmark\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\calendar_event\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\cmis\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\comic_book\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\competence\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\criteria\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\dailymotion\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\description\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\document\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\encyclopedia_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\event\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\external_calendar\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\feedback\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\file\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\fill_in_blanks_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\forum\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\forum_topic\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\glossary\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\glossary_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\handbook\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\handbook_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\handbook_topic\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\hotpotatoes\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\hotspot_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\indicator\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\introduction\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\learning_path\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\learning_path_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\life_page\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\link\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\link\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\match_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\matterhorn\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\mediamosa\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\note\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\ordering_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\package\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\page\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\peer_assessment\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\personal_message\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\physical_location\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\portfolio\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\portfolio_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\profile\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\qwiki\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\rss_feed\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\rss_feed\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\scorm_item\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\slideshare\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\soundcloud\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\story\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_description\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_matching_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_matrix_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_multiple_choice_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_open_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_page\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_rating_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\survey_select_question\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\system_announcement\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\task\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\template\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\twitter_search\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\vimeo\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\webpage\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\wiki\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\wiki_page\php\package\package.info',
    'E:\Apache\chamilo/repository\content_object\youtube\php\package\package.info',
    'E:\Apache\chamilo/repository\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/repository\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/repository\php\package\package.info',
    'E:\Apache\chamilo/repository\quota\php\package\package.info',
    'E:\Apache\chamilo/repository\quota\rights\php\package\package.info',
    'E:\Apache\chamilo/repository\viewer\php\package\package.info',
    'E:\Apache\chamilo/rights\php\package\package.info',
    'E:\Apache\chamilo/core\tracking\php\package\package.info',
    'E:\Apache\chamilo/user\integration\core\home\php\package\package.info',
    'E:\Apache\chamilo/user\integration\core\reporting\php\package\package.info',
    'E:\Apache\chamilo/user\integration\core\tracking\php\package\package.info',
    'E:\Apache\chamilo/user\integration\core\webservice\php\package\package.info',
    'E:\Apache\chamilo/user\php\package\package.info',
    'E:\Apache\chamilo/webservice\php\package\package.info');

foreach ($files as $file)
{
    // $file_properties = FileProperties :: from_path($file);

    // if ($file_properties->get_extension() == 'info' && $file_properties->get_name() == 'package' &&
    // strpos($file_properties->get_path(), '.hg') === false)
    // {
    $packages[] = str_replace(Path :: getInstance()->getBasePath(), '', str_replace('\php\package\package.info', '', $file));
    // }
}

sort($packages);

$rows = array();

foreach ($packages as $key => $package)
{
    $row = array();
    $row[] = $key + 1 . '.';
    $row[] = $package;
    $row[] = '<img src="' . Theme :: getInstance()->getImagePath($package) . 'Logo/16.png" />';

    $rows[] = $row;
}

$table = new SortableTableFromArray($rows, 0, 500, 'packages_list');
$header = $table->getHeader();
$table->set_header(0, '#');
$table->set_header(1, 'Package');
$table->set_header(2, ' ');

Display :: small_header();
echo $table->as_html();
Display :: small_footer();