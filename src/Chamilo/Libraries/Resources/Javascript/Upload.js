/**
 * JavaScript library to deal with file uploads
 * @author    Yannick Warnier <yannick.warnier@chamilo.org>
 */
/**
 * Upload class. Used to pack functions into one practical object.
 * Call like this: var myUpload = new upload(5);
 */

function upload(latency) {
    /**
     * Starts the timer
     * Call like this:
     * @param    string    Name of the DOM element we need to update
     * @param    string    Loading image to display
     * @return    true
     */
    function start(domid, img, text, formid) {
        __progress_bar_domid = domid;
        __progress_bar_img = img;
        __progress_bar_text = text;
        __progress_bar_interval = setTimeout(__display_progress_bar, latency);
        //__display_progress_bar()
        __upload_form_domid = formid;
    }

    /**
     * Displays the progress bar in the given DOM element
     */
    function __display_progress_bar() {
        var my_html = '<span style="font-style:italic;">' + __progress_bar_text + '</span><br/>' + __progress_bar_img +
            '';
        document.getElementById(__progress_bar_domid).innerHTML = my_html;
        if (__upload_form_domid != '') {
            document.getElementById(__upload_form_domid).style.display = 'none';
        }
    }

    function stop() {
        //clearTimeout(__progress_bar_interval);
        document.getElementById(__progress_bar_domid).innerHTML = null;
        document.getElementById(__progress_bar_domid).style.display = 'none';
    }

    this.start = start;
    this.stop = stop;
    var __progress_bar_domid = '';
    var __progress_bar_img = getPath('WEB_PATH') + '/configuration/resources/images/' + getTheme() +
        '/action_progress_bar.gif';
    var __progress_bar_text = getTranslation('Uploading', 'libraries');
    var __upload_form_domid = '';
}
