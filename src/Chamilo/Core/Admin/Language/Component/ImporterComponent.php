<?php
namespace Chamilo\Core\Admin\Language\Component;

use Chamilo\Core\Admin\Language\Manager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ImporterComponent extends Manager
{
    const PARAM_SOURCE_FILE = 'source_file';

    private static $repository_map = array(
        'chamilo-adaptive-assessment-dev' => 'core\repository\content_object\adaptive_assessment',
        'chamilo-adaptive-assessment-item-dev' => 'core\repository\content_object\adaptive_assessment_item',
        'chamilo-admin-dev' => 'core\admin',
        'chamilo-announcement-dev' => 'core\repository\content_object\announcement',
        'chamilo-app-alexia-dev' => 'application\alexia',
        'chamilo-app-assessment-dev' => 'application\assessment',
        'chamilo-app-cache-dev' => 'application\cache',
        'chamilo-app-cas-user-dev' => 'application\cas_user',
        'chamilo-app-cda-dev' => 'application\cda',
        'chamilo-app-context-linker-dev' => 'core\context_linker',
        'chamilo-app-distribute-dev' => 'application\distribute',
        'chamilo-app-elude-dev' => 'application\elude',
        'chamilo-app-forum-dev' => 'application\forum',
        'chamilo-app-gutenberg-dev' => 'application\gutenberg',
        'chamilo-app-handbook-dev' => 'application\handbook',
        'chamilo-app-internship-organizer-dev' => 'application\internship_organizer',
        'chamilo-app-laika-dev' => 'application\laika',
        'chamilo-app-linker-dev' => 'application\linker',
        'chamilo-app-metadata-dev' => 'core\metadata',
        'chamilo-app-package-dev' => 'application\package',
        'chamilo-app-peer-assessment-dev' => 'application\peer_assessment',
        'chamilo-app-personal-calendar-dev' => 'application\personal_calendar',
        'chamilo-app-personal-messenger-2-dev' => 'application\personal_messenger',
        'chamilo-app-photo-gallery-dev' => 'application\photo_gallery',
        'chamilo-app-phrases-dev' => 'application\phrases',
        'chamilo-app-portfolio-dev' => 'application\portfolio',
        'chamilo-app-profiler-dev' => 'application\profiler',
        'chamilo-app-reservations-dev' => 'application\reservations',
        'chamilo-app-search-portal-dev' => 'application\search_portal',
        'chamilo-app-survey-dev' => 'application\survey',
        'chamilo-app-weblcms-announcement-dev' => 'application\weblcms\tool\announcement',
        'chamilo-app-weblcms-assessment-dev' => 'application\weblcms\tool\assessment',
        'chamilo-app-weblcms-assignment-dev' => 'application\weblcms\tool\assignment',
        'chamilo-app-weblcms-blog-dev' => 'application\weblcms\tool\blog',
        'chamilo-app-weblcms-calendar-dev' => 'application\weblcms\tool\calendar',
        'chamilo-app-weblcms-chat-dev' => 'application\weblcms\tool\chat',
        'chamilo-app-weblcms-course-copier-dev' => 'application\weblcms\tool\course_copier',
        'chamilo-app-weblcms-course-deleter-dev' => 'application\weblcms\tool\course_deleter',
        'chamilo-app-weblcms-course-exporter-dev' => 'application\weblcms\tool\course_exporter',
        'chamilo-app-weblcms-course-group-dev' => 'application\weblcms\tool\course_group',
        'chamilo-app-weblcms-course-importer-dev' => 'application\weblcms\tool\course_importer',
        'chamilo-app-weblcms-course-sections-dev' => 'application\weblcms\tool\course_sections',
        'chamilo-app-weblcms-course-settings-dev' => 'application\weblcms\tool\course_settings',
        'chamilo-app-weblcms-course-truncater-dev' => 'application\weblcms\tool\course_truncater',
        'chamilo-app-weblcms-description-dev' => 'application\weblcms\tool\description',
        'chamilo-app-weblcms-dev' => 'application\weblcms',
        'chamilo-app-weblcms-document-dev' => 'application\weblcms\tool\document',
        'chamilo-app-weblcms-ephorus-dev' => 'application\weblcms\tool\ephorus',
        'chamilo-app-weblcms-forum-dev' => 'application\weblcms\tool\forum',
        'chamilo-app-weblcms-geolocation-dev' => 'application\weblcms\tool\geolocation',
        'chamilo-app-weblcms-glossary-dev' => 'application\weblcms\tool\glossary',
        'chamilo-app-weblcms-home-dev' => 'application\weblcms\tool\home',
        'chamilo-app-weblcms-learning-path-dev' => 'application\weblcms\tool\learning_path',
        'chamilo-app-weblcms-link-dev' => 'application\weblcms\tool\link',
        'chamilo-app-weblcms-maintenance-dev' => 'application\weblcms\tool\maintenance',
        'chamilo-app-weblcms-note-dev' => 'application\weblcms\tool\note',
        'chamilo-app-weblcms-reporting-dev' => 'application\weblcms\tool\reporting',
        'chamilo-app-weblcms-rights-dev' => 'application\weblcms\tool\rights',
        'chamilo-app-weblcms-search-dev' => 'application\weblcms\tool\search',
        'chamilo-app-weblcms-streaming-video-dev' => 'application\weblcms\tool\streaming_video',
        'chamilo-app-weblcms-user-dev' => 'application\weblcms\tool\user',
        'chamilo-app-weblcms-video-conferencing-dev' => 'application\weblcms\tool\video_conferencing',
        'chamilo-app-weblcms-wiki-dev' => 'application\weblcms\tool\wiki',
        'chamilo-app-wiki-dev' => 'application\wiki',
        'chamilo-assessment-dev' => 'core\repository\content_object\assessment',
        'chamilo-assessment-matching-question-dev' => 'core\repository\content_object\assessment_matching_question',
        'chamilo-assessment-match-numeric-question-dev' => 'core\repository\content_object\assessment_match_numeric_question',
        'chamilo-assessment-match-text-question-dev' => 'core\repository\content_object\assessment_match_text_question',
        'chamilo-assessment-matrix-question-dev' => 'core\repository\content_object\assessment_matrix_question',
        'chamilo-assessment-multiple-choice-question-dev' => 'core\repository\content_object\assessment_multiple_choice_question',
        'chamilo-assessment-open-question-dev' => 'core\repository\content_object\assessment_open_question',
        'chamilo-assessment-rating-question-dev' => 'core\repository\content_object\assessment_rating_question',
        'chamilo-assessment-select-question-dev' => 'core\repository\content_object\assessment_select_question',
        'chamilo-assignment-dev' => 'core\repository\content_object\assignment',
        'chamilo-bbb-meeting-dev' => 'core\repository\content_object\bbb_meeting',
        'chamilo-blog-dev' => 'core\repository\content_object\blog',
        'chamilo-blog-item-dev' => 'core\repository\content_object\blog_item',
        'chamilo-bookmark-dev' => 'core\repository\content_object\bookmark',
        'chamilo-calendar-event-dev' => 'core\repository\content_object\calendar_event',
        'chamilo-cmis-dev' => 'core\repository\content_object\cmis',
        'chamilo-comic-book-dev' => 'core\repository\content_object\comic_book',
        'chamilo-competence-dev' => 'core\repository\content_object\competence',
        'chamilo-criteria-dev' => 'core\repository\content_object\criteria',
        'chamilo-dailymotion-dev' => 'core\repository\content_object\dailymotion',
        'chamilo-description-dev' => 'core\repository\content_object\description',
        'chamilo-dev' => 'common\libraries',
        'chamilo-document-dev' => 'core\repository\content_object\document',
        'chamilo-encyclopedia-item-dev' => 'core\repository\content_object\encyclopedia_item',
        'chamilo-external-calendar-dev' => 'core\repository\content_object\external_calendar',
        'chamilo-ext-repo-bitbucket-dev' => 'common\extensions\external_repository_manager\implementation\bitbucket',
        'chamilo-ext-repo-box-dev' => 'common\extensions\external_repository_manager\implementation\box',
        'chamilo-ext-repo-cmis-dev' => 'common\extensions\external_repository_manager\implementation\cmis',
        'chamilo-ext-repo-dailymotion-dev' => 'common\extensions\external_repository_manager\implementation\dailymotion',
        'chamilo-ext-repo-dropbox-dev' => 'common\extensions\external_repository_manager\implementation\dropbox',
        'chamilo-ext-repo-eol-dev' => 'common\extensions\external_repository_manager\implementation\eol',
        'chamilo-ext-repo-fedora-dev' => 'common\extensions\external_repository_manager\implementation\fedora',
        'chamilo-ext-repo-flickr-dev' => 'common\extensions\external_repository_manager\implementation\flickr',
        'chamilo-ext-repo-google-docs-dev' => 'common\extensions\external_repository_manager\implementation\google_docs',
        'chamilo-ext-repo-mediamosa-dev' => 'common\extensions\external_repository_manager\implementation\mediamosa',
        'chamilo-ext-repo-photobucket-dev' => 'common\extensions\external_repository_manager\implementation\photobucket',
        'chamilo-ext-repo-picasa-dev' => 'common\extensions\external_repository_manager\implementation\picasa',
        'chamilo-ext-repo-qwiki-dev' => 'common\extensions\external_repository_manager\implementation\qwiki',
        'chamilo-ext-repo-scribd-dev' => 'common\extensions\external_repository_manager\implementation\scribd',
        'chamilo-ext-repo-slideshare-dev' => 'common\extensions\external_repository_manager\implementation\slideshare',
        'chamilo-ext-repo-soundcloud-dev' => 'common\extensions\external_repository_manager\implementation\soundcloud',
        'chamilo-ext-repo-vimeo-dev' => 'common\extensions\external_repository_manager\implementation\vimeo',
        'chamilo-ext-repo-wikimedia-dev' => 'common\extensions\external_repository_manager\implementation\wikimedia',
        'chamilo-ext-repo-wikipedia-dev' => 'common\extensions\external_repository_manager\implementation\wikipedia',
        'chamilo-ext-repo-youtube-dev' => 'common\extensions\external_repository_manager\implementation\youtube',
        'chamilo-feedback-dev' => 'core\repository\content_object\feedback',
        'chamilo-fill-in-blanks-question-dev' => 'core\repository\content_object\fill_in_blanks_question',
        'chamilo-forum-dev' => 'core\repository\content_object\forum',
        'chamilo-forum-post-dev' => 'core\repository\content_object\forum_post_template',
        'chamilo-forum-topic-dev' => 'core\repository\content_object\forum_topic',
        'chamilo-glossary-dev' => 'core\repository\content_object\glossary',
        'chamilo-glossary-item-dev' => 'core\repository\content_object\glossary_item',
        'chamilo-group-dev' => 'group',
        'chamilo-handbook-dev' => 'core\repository\content_object\handbook',
        'chamilo-handbook-item-dev' => 'core\repository\content_object\handbook_item',
        'chamilo-handbook-topic-dev' => 'core\repository\content_object\handbook_topic',
        'chamilo-help-dev' => 'core\help',
        'chamilo-home-dev' => 'core\home',
        'chamilo-hotpotatoes-dev' => 'core\repository\content_object\hotpotatoes',
        'chamilo-hotspot-question-dev' => 'core\repository\content_object\hotspot_question',
        'chamilo-indicator-dev' => 'core\repository\content_object\indicator',
        'chamilo-install-dev' => 'core\install',
        'chamilo-introduction-dev' => 'core\repository\content_object\introduction',
        'chamilo-learning-path-dev' => 'core\repository\content_object\learning_path',
        'chamilo-learning-path-item-dev' => 'core\repository\content_object\learning_path_item',
        'chamilo-life_page-dev' => 'core\repository\content_object\life_page',
        'chamilo-link-dev' => 'core\repository\content_object\link',
        'chamilo-lynx-dev' => 'core\lynx',
        'chamilo-match-question-dev' => 'core\repository\content_object\match_question',
        'chamilo-media_production-dev' => 'core\repository\content_object\media_production',
        'chamilo-mediamosa-dev' => 'core\repository\content_object\mediamosa',
        'chamilo-menu-dev' => 'core\menu',
        'chamilo-migration-dev' => 'core\migration',
        'chamilo-note-dev' => 'core\repository\content_object\note',
        'chamilo-ordering-question-dev' => 'core\repository\content_object\ordering_question',
        'chamilo-package-dev' => 'core\repository\content_object\package',
        'chamilo-peer-assessment-dev' => 'core\repository\content_object\peer_assessment',
        'chamilo-personal-message-dev' => 'core\repository\content_object\personal_message',
        'chamilo-physical-location-dev' => 'core\repository\content_object\physical_location',
        'chamilo-portfolio-dev' => 'core\repository\content_object\portfolio',
        'chamilo-portfolio-item-dev' => 'core\repository\content_object\portfolio_item',
        'chamilo-profile-dev' => 'core\repository\content_object\profile',
        'chamilo-qwiki-dev' => 'core\repository\content_object\qwiki',
        'chamilo-reporting-dev' => 'reporting',
        'chamilo-repository-dev' => 'core\repository',
        'chamilo-rights-dev' => 'core\rights',
        'chamilo-rss-feed-dev' => 'core\repository\content_object\rss_feed',
        'chamilo-scorm-item-dev' => 'core\repository\content_object\scorm_item',
        'chamilo-slideshare-dev' => 'core\repository\content_object\slideshare',
        'chamilo-soundcloud-dev' => 'core\repository\content_object\soundcloud',
        'chamilo-story-dev' => 'core\repository\content_object\story',
        'chamilo-survey-description-dev' => 'core\repository\content_object\survey_description',
        'chamilo-survey-dev' => 'core\repository\content_object\survey',
        'chamilo-survey-matching-question-dev' => 'core\repository\content_object\survey_matching_question',
        'chamilo-survey-matrix-question-dev' => 'core\repository\content_object\survey_matrix_question',
        'chamilo-survey-multiple-choice-question-dev' => 'core\repository\content_object\survey_multiple_choice_question',
        'chamilo-survey-open-question-dev' => 'core\repository\content_object\survey_open_question',
        'chamilo-survey-page-dev' => 'core\repository\content_object\survey_page',
        'chamilo-survey-rating-question-dev' => 'core\repository\content_object\survey_rating_question',
        'chamilo-survey-select-question-dev' => 'core\repository\content_object\survey_select_question',
        'chamilo-system-announcement-dev' => 'core\repository\content_object\system_announcement',
        'chamilo-task-dev' => 'core\repository\content_object\task',
        'chamilo-template-dev' => 'core\repository\content_object\template',
        'chamilo-tracking-dev' => 'core\tracking',
        'chamilo-twitter-search-dev' => 'core\repository\content_object\twitter_search',
        'chamilo-user-dev' => 'user',
        'chamilo-vid-conf-bbb-dev' => 'common\extensions\video_conferencing_manager\implementation\bbb',
        'chamilo-vimeo-dev' => 'core\repository\content_object\vimeo',
        'chamilo-wiki-dev' => 'core\repository\content_object\wiki',
        'chamilo-wiki-page-dev' => 'core\repository\content_object\wiki_page',
        'chamilo-youtube-dev' => 'core\repository\content_object\youtube');

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $form = new FormValidator('translations', 'post', $this->get_url());
        $form->addElement('file', self::PARAM_SOURCE_FILE, Translation::get('SourceFile'));
        $form->addElement(
            'style_submit_button',
            'import_button',
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES),
            array('id' => 'import_button'),
            null,
            'import');

        if ($form->validate())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->process();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Reads a line from a csv file an converts it to an array
     *
     * @param resource $file_handle
     *
     * @return string[]
     */
    private function read_csv($file_handle)
    {
        return fgetcsv($file_handle, 0, ';', '"');
    }

    private function parse_file()
    {
        $file = $_FILES[self::PARAM_SOURCE_FILE];
        $file_path = $file['tmp_name'];

        $file_handle = fopen($file_path, 'r');

        $information_row = $this->read_csv($file_handle);
        $languages_row = $this->read_csv($file_handle);

        $languages = array_slice($languages_row, 4);

        $translations = array();

        while (($csv_data = $this->read_csv($file_handle)) !== false)
        {
            foreach (array_slice($csv_data, 4) as $key => $value)
            {
                $translations[$csv_data[0]][$languages[$key]][$csv_data[1]] = $value;
            }
        }

        return $translations;
    }

    private function process()
    {
        $translations = $this->parse_file();
        $convert_package_names = $this->is_repository_based(array_keys($translations));

        $data = array();

        foreach ($translations as $package => $languages)
        {
            if ($convert_package_names)
            {
                $package = self::$repository_map[$package];

                if (! $package)
                {
                    continue;
                }
            }

            foreach ($languages as $language => $variables)
            {
                $language_path = Path::getInstance()->namespaceToFullPath($package) . 'resources/i18n/' . $language .
                     '.i18n';

                if (file_exists($language_path))
                {
                    $existing_translations = parse_ini_file($language_path);
                }
                else
                {
                    $existing_translations = array();
                }

                $differences = array_diff(array_keys($existing_translations), array_keys($variables));

                foreach ($differences as $difference)
                {
                    $variables[$difference] = $existing_translations[$difference];
                }

                $non_empty_local_translations = 0;

                foreach ($variables as $variable => $value)
                {
                    if ($value == '' && $existing_translations[$variable] != '')
                    {
                        $variables[$variable] = $existing_translations[$variable];
                        $non_empty_local_translations ++;
                    }
                }

                $row = array();
                $row[] = $package;
                $row[] = Theme::getInstance()->getImage(
                    'language/' . $language,
                    'png',
                    $language,
                    null,
                    ToolbarItem::DISPLAY_ICON,
                    false,
                    __NAMESPACE__);
                $row[] = count($existing_translations);
                $row[] = count($variables);
                $row[] = count($differences);
                $row[] = $non_empty_local_translations;

                $data[] = $row;

                $translation_content = array();

                $translation_content[] = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;';
                $translation_content[] = '; Package = ' . $package;
                $translation_content[] = '; Language = ' . $language;
                $translation_content[] = ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;';
                $translation_content[] = '';

                foreach ($variables as $variable => $value)
                {
                    $translation_content[] = $variable . ' = "' . str_replace('"', '\"', $value) . '"';
                }

                Filesystem::write_to_file($language_path, implode(PHP_EOL, $translation_content));
            }
        }

        $headers = array();
        $headers[] = new SortableStaticTableColumn(Translation::get('Package'));
        $headers[] = new SortableStaticTableColumn(
            Theme::getInstance()->getImage(
                'Action/Language',
                'png',
                Translation::get('Language'),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                __NAMESPACE__));
        $headers[] = new SortableStaticTableColumn(
            Theme::getInstance()->getImage(
                'Action/Local',
                'png',
                Translation::get('LocalVariableCount'),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                __NAMESPACE__));

        $headers[] = new SortableStaticTableColumn(
            Theme::getInstance()->getImage(
                'Action/Import',
                'png',
                Translation::get('ImportVariableCount'),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                __NAMESPACE__));

        $headers[] = new SortableStaticTableColumn(
            Theme::getInstance()->getImage(
                'Action/Extra',
                'png',
                Translation::get('LocalExtraCount'),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                __NAMESPACE__));

        $headers[] = new SortableStaticTableColumn(
            Theme::getInstance()->getImage(
                'Action/Empty',
                'png',
                Translation::get('LocalNonEmptyCount'),
                null,
                ToolbarItem::DISPLAY_ICON,
                false,
                __NAMESPACE__));

        $table = new SortableTableFromArray(
            $data,
            $headers,
            $this->get_parameters(),
            0,
            20,
            SORT_ASC,
            'language_import_' . time());

        return $table->toHtml();
    }

    public function is_repository_based($packages)
    {
        foreach ($packages as $package)
        {
            if (strpos($package, '-') !== false)
            {
                return true;
            }
        }

        return false;
    }
}
