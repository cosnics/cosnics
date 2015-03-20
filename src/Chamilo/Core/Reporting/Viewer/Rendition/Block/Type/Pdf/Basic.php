<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Pdf;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Pdf;

/**
 *
 * @author Andras Zolnay
 * @package reporting.viewer
 */
class Basic extends Pdf
{

    public function render()
    {
        $pdf_mc_table = $this->get_context()->get_pdf_mc_table();

        $title_font = array('Arial', 'B', 11);
        if (method_exists($this->get_block(), 'get_title_font'))
        {
            $title_font = $this->get_block()->get_title_font();
        }
        $pdf_mc_table->SetFont($title_font[0], $title_font[1], $title_font[2]);

        $pdf_mc_table->MultiCell(
            0,
            3,
            $this->transcode_string($this->get_block()->get_title(), false, 'UTF-8', 'CP1252'),
            0);
        $pdf_mc_table->Ln();

        $reporting_data = $this->get_block()->get_data();

        // If all styles are NULL, creates them automatically
        $reporting_data->setStyleAutomatically();

        $table_font = array('Arial', '', 9);
        if (method_exists($this->get_block(), 'get_table_font'))
        {
            $table_font = $this->get_block()->get_table_font();
        }
        $pdf_mc_table->SetFont($table_font[0], $table_font[1], $table_font[2]);

        // Column Style
        $column_type = array();
        if ($reporting_data->is_categories_visible())
        {
            $column_type[] = $reporting_data->get_category_style();
        }
        foreach ($reporting_data->get_rows() as $row_index => $row_name)
        {
            $column_type[] = $reporting_data->get_row_style($row_name);
        }
        $pdf_mc_table->SetColumnType($column_type);

        // Heading
        if ($reporting_data->is_row_heading_visible())
        {
            $heading_data = array();
            if ($reporting_data->is_categories_visible())
            {
                $heading_data[] = '';
            }
            foreach ($reporting_data->get_rows() as $row_index => $row_name)
            {
                $heading_data[] = $row_name;
            }

            $pdf_mc_table->SetHeading($heading_data);
            $pdf_mc_table->Heading();
        }
        else
        {
            $pdf_mc_table->SetHeading(null);
        }

        // Data
        $pdf_mc_table->SetFillColor(255, 255, 255);
        $pdf_mc_table->SetTextColor(0);
        foreach ($reporting_data->get_categories() as $category_index => $category_name)
        {
            $row_data = array();
            if ($reporting_data->is_categories_visible())
            {
                $row_data[] = $category_name;
            }

            foreach ($reporting_data->get_rows() as $row_index => $row_name)
            {
                $reporting_data_data = $reporting_data->get_data();
                $row_data[] = $this->transcode_string(
                    $reporting_data_data[$category_index][$row_index],
                    false,
                    'UTF-8',
                    'CP1252');
            }

            $split_row_data = $this->split_row_data($pdf_mc_table, $row_data);
            foreach ($split_row_data as $new_row_data)
            {
                $pdf_mc_table->Row($new_row_data);
            }
        }

        $pdf_mc_table->Ln(20);
    }

    /**
     * @brief Creates new rows such that none of the new rows is higher than one page.
     *
     * @return array of array.
     */
    private function split_row_data($pdf_mc_table, $row_data)
    {
        // Split each row item into array of items where each item fits on a page.
        $split_row_items = array();
        $max_row_item_count = 0;
        foreach ($row_data as $column_index => $row_item)
        {
            $split_item = $this->split_row_item($pdf_mc_table, $column_index, $row_item);
            $split_row_items[] = $split_item;
            $max_row_item_count = max($max_row_item_count, count($split_item));
        }

        // Create new rows.
        $rows = array();
        for ($i = 0; $i < $max_row_item_count; $i ++)
        {
            $row_data = array();

            foreach ($split_row_items as $split_item)
            {
                if ($i < count($split_item))
                {
                    $row_data[] = $split_item[$i];
                }
                else
                {
                    $row_data[] = '';
                }
            }

            $rows[] = $row_data;
        }

        return $rows;
    }

    /**
     * @brief Splits the given item in the row such that none of the new items is higher than one page.
     *
     * @return array of strings.
     */
    private function split_row_item($pdf_mc_table, $column_index, $row_item)
    {
        $character_per_surface_unit = .085;

        $column_surface = $pdf_mc_table->GetWidth($column_index) * $pdf_mc_table->GetAbsoluteHeight(1.);
        $max_page_length = (int) ($column_surface * $character_per_surface_unit);

        if ($max_page_length <= 0)
        {
            throw new \Exception('Invalid PDF export maximum page length');
        }

        $column_item_length = strlen($row_item);

        $split_row_item = array();

        if ($column_item_length <= $max_page_length)
        {
            $split_row_item[] = $row_item;
        }
        else
        {
            $start = 0;
            while ($start < $column_item_length)
            {
                $current_page_string = substr($row_item, $start, $max_page_length);
                if (! $current_page_string)
                {
                    throw new \Exception('Invalid page start index.');
                }

                $next_page_string = substr($row_item, $start + $max_page_length, $max_page_length);

                if (! $next_page_string)
                {
                    $current_page_length = $max_page_length;
                    $start_increment = $max_page_length;
                }
                else
                {
                    $current_matches = array();
                    preg_match('/(\s+)(\S*)$/', $current_page_string, $current_matches, PREG_OFFSET_CAPTURE);
                    $next_matches = array();
                    preg_match('/^(\S*)(\s+)/', $next_page_string, $next_matches, PREG_OFFSET_CAPTURE);

                    if (! empty($current_matches[2][0]) && ! empty($next_matches[1][0]))
                    { // Page break in middle of a word
                        $current_page_length = $current_matches[1][1];
                        $start_increment = $current_matches[2][1];
                    }
                    else
                        if (! empty($current_matches[2][0]))
                        { // Page break directly after word
                            $current_page_length = $max_page_length;
                            $start_increment = $max_page_length + strlen($next_matches[2][0]);
                        }
                        else
                            if (! empty($next_matches[1][0]))
                            { // Page break directly before first word of next page.
                                $current_page_length = $current_matches[1][1];
                                $start_increment = $max_page_length;
                            }
                            else
                            { // Page break in middle of whitespaces
                                $current_page_length = $current_matches[1][1];
                                $start_increment = $max_page_length + strlen($next_matches[2][0]);
                            }
                }

                $split_row_item[] = substr($current_page_string, 0, $current_page_length);
                $start += $start_increment;
            }
        }

        return $split_row_item;
    }

    /**
     * \brief Simple HTML to plain text converter:
     * Features:
     * - ordered and unordered lists
     * - normalizes new lines
     * - encoding
     *
     * @param $should_create_single_line If true, all white spaces are replaced by ' '
     * @param $input_encoding Passed to iconv as input character set
     * @param $output_encoding Passed to iconv as output character set and also to html_entity_decode.
     */
    private function transcode_string($text, $should_create_single_line = false, $input_encoding = 'UTF-8',
        $output_encoding = 'UTF-8')
    {
        $text = iconv($input_encoding, $output_encoding, $text);

        $decode_flags = ENT_QUOTES | ENT_HTML401;

        // Remove all tags except lists.
        $text = strip_tags($text, '<ol><ul><li><br><br/><img>');

        // Handle line break
        $text = preg_replace('/<br\/>/i', "\n", $text);
        $text = preg_replace('/<br \/>/i', "\n", $text);

        // Convert back html escape sring.
        $text = html_entity_decode($text, $decode_flags, $output_encoding);

        // Replace 2 or more subsequent line breaks with two ones.
        $text = preg_replace('/\n(\s*\n)+/', "\n\n", $text);

        // Remove leading and trailing white spaces.
        $text = trim($text);

        // Handle ordered list
        $text = preg_replace_callback("/<ol>(.*)<\/ol>/isU", array('self', 'replace_ordered_list'), $text);

        // Handle unordered list
        $text = preg_replace_callback(
            "/<ul(\s+class=\".*\")?(\s+style=\".*\")?\s*>(.*)<\/ul>/isU",
            array('self', 'replace_unordered_list'),
            $text);

        // Replace dot character chr(149) with its encoded equivalent
        $text = str_replace(chr(149), html_entity_decode('&bull;', $decode_flags, $output_encoding), $text);

        // Replace non-breaking space with ' '.
        $text = str_replace(html_entity_decode('&nbsp;', $decode_flags, $output_encoding), ' ', $text);

        // Replace all whitespaces with ' '.
        if ($should_create_single_line)
        {
            $text = preg_replace('/[ \n\r\t]+/', ' ', $text);
        }

        // Extract image filename
        $text = preg_replace('/<img\s+src="(.+)".*\/>/isU', '${1}', $text);

        return $text;
    }

    private function replace_ordered_list($enumaration_match)
    {
        preg_match_all("/<li(\s+style=\".*\")?\s*>(.*)<\/li>/isU", $enumaration_match[1], $list_matches);
        $result = array();
        foreach ($list_matches[2] as $index => $list_item)
        {
            $result[] = '  ' . ($index + 1) . '. ' . trim($list_item);
        }

        return implode($result, "\n");
    }

    private function replace_unordered_list($unordered_list_match)
    {
        preg_match_all(
            "/<li(\s+class=\".*\")?(\s+style=\".*\")?\s*>(.*)<\/li>/isU",
            $unordered_list_match[3],
            $list_matches);
        $result = array();
        foreach ($list_matches[3] as $list_item)
        {
            $result[] = '  ' . chr(149) . ' ' . trim($list_item);
        }

        return implode($result, "\n");
    }
}
