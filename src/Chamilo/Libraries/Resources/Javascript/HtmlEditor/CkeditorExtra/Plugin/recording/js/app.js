//webkitURL is deprecated but nevertheless
URL = window.URL || window.webkitURL;

var gumStream; 						//stream from getUserMedia()
var recorder; 						//WebAudioRecorder object
var input; 							//MediaStreamAudioSourceNode  we'll be recording
var encodingType; 					//holds selected encoding for resulting audio (file)
var encodeAfterRecord = true;       // when to encode
var blob;

// shim for AudioContext when it's not avb. 
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext;                   //new audio context to help us record

//Parameters
function getURLParameter (parameter) {
    let url = new URL(window.location);
    return url.searchParams.get(parameter);
}
//console.log(getURLParameter(window.location));

//add events and translations to those 2 buttons
let recordButton = document.getElementById("recordButton");
recordButton.addEventListener("click", startRecording);
recordButton.innerText = getURLParameter('record');

let stopButton = document.getElementById("stopButton");
stopButton.addEventListener("click", stopRecording);
stopButton.innerText = getURLParameter('stop');

let choiceText = document.getElementById("choice");
choiceText.innerText = getURLParameter('recordings');

function startRecording() {
    console.log("startRecording() called");

    /*
        Simple constraints object, for more advanced features see
        https://addpipe.com/blog/audio-constraints-getusermedia/
    */

    var constraints = {audio: true, video: false};

    /*
    	We're using the standard promise based getUserMedia() 
    	https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
	*/

    navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        //__log("getUserMedia() success, stream created, initializing WebAudioRecorder...");

        /*
            create an audio context after getUserMedia is called
            sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
            the sampleRate defaults to the one set in your OS for your playback device

        */
        audioContext = new AudioContext();

        //update the format
        //document.getElementById("formats").innerHTML = "Format: 2 channel " + encodingTypeSelect.options[encodingTypeSelect.selectedIndex].value + " @ " + audioContext.sampleRate / 1000 + "kHz";
        //document.getElementById("formats").innerHTML = "Format: 2 channel " + "mp3" + " @ " + audioContext.sampleRate / 1000 + "kHz";

        //assign to gumStream for later use
        gumStream = stream;

        /* use the stream */
        input = audioContext.createMediaStreamSource(stream);

        //stop the input from playing back through the speakers
        //input.connect(audioContext.destination)

        //get the encoding
        //encodingType = encodingTypeSelect.options[encodingTypeSelect.selectedIndex].value;
        //wav, mp3 or ogg
        encodingType = "mp3";

        //disable the encoding selector
        //encodingTypeSelect.disabled = true;

        recorder = new WebAudioRecorder(input, {
            workerDir: "../js/", // must end with slash
            encoding: encodingType,
            numChannels: 2, //2 is the default, mp3 encoding supports only 2
            onEncoderLoading: function (recorder, encoding) {
                // show "loading encoder..." display
                //__log("Loading " + encoding + " encoder...");
            },
            onEncoderLoaded: function (recorder, encoding) {
                // hide "loading encoder..." display
                //__log(encoding + " encoder loaded");
            }
        });

        recorder.onComplete = function (recorder, blob) {
            //__log("Encoding complete");
            createDownloadLink(blob, recorder.encoding);
            //encodingTypeSelect.disabled = false;
        };

        recorder.setOptions({
            timeLimit: 120,
            encodeAfterRecord: encodeAfterRecord,
            ogg: {quality: 0.5},
            mp3: {bitRate: 160}
        });

        //start the recording process
        recorder.startRecording();

        //__log("Recording started");

    }).catch(function (err) {
        //enable the record button if getUSerMedia() fails
        recordButton.disabled = false;
        stopButton.disabled = true;

    });

    //disable the record button
    recordButton.disabled = true;
    stopButton.disabled = false;

    //Blinking
    document.getElementById("icon").setAttribute("class", "blink");
}

function stopRecording() {
    console.log("stopRecording() called");

    //stop microphone access
    gumStream.getAudioTracks()[0].stop();

    //disable the stop button
    stopButton.disabled = true;
    recordButton.disabled = false;

    //tell the recorder to finish the recording (stop recording + encode the recorded audio)
    recorder.finishRecording();

    //__log('Recording stopped');

    //Blinking
    document.getElementById("icon").removeAttribute("class");
}



function postBlob(blobData, linkDownload) {
    linkDownload = prompt("Save to repo", linkDownload.toString()) + '.mp3';

    if (linkDownload !== 'null.mp3') {
        let serverHost = window.location.href.toString().split(window.location.host)[0] + window.location.host;

        let jsonResponse;

        var ajaxUri = serverHost + '/index.php';
        var parameters = {
            'application': 'Chamilo\\Core\\Repository\\Ajax',
            'go': 'html_editor_file_upload',
            'upload': blobData
        };

        var fd = new FormData();
        fd.append('application', 'Chamilo\\Core\\Repository\\Ajax');
        fd.append('go', 'html_editor_file_upload');
        fd.append('upload', blobData, linkDownload);

        $.ajax({
            type: "POST",
            url: ajaxUri,
            data: fd,
            async: false,
            processData: false,
            contentType: false
        }).success(function (json) {
            if (json.uploaded === 1) {
                jsonResponse = json;
            }
        }).error(function (error) {
            console.log('error');
        });

        //Disable save button
        //document.getElementsByName(linkDownload).disabled = true;

        //Example: src/Chamilo/Core/Repository/Processor/Ckeditor/Processor.php
        window.opener.CKEDITOR.tools.callFunction(parseInt(getURLParameter('CKEditorFuncNum')), window.location.href.toString(), jsonResponse['co-id'], jsonResponse['security-code'].toString(),'audio');
        window.close();
    }
}


function createDownloadLink(blob, encoding) {
    this.blob = blob;

    var self = this;

    var url = URL.createObjectURL(blob);
    var au = document.createElement('audio');
    var li = document.createElement('li');
    var link = document.createElement('a');

    //add controls to the <audio> element
    au.controls = true;
    au.src = url;

    //link the a element to the blob
    link.href = url;
    link.download = 'rec-' + window.location.hostname + '-' + new Date().toISOString() + '.' + encoding;
    link.id = link.download;
    link.innerHTML = link.download;

    //save to repo button
    var button = document.createElement('button');
    button.innerHTML = getURLParameter('insert');
    button.id = 'insert';
    button.addEventListener('click', function () {
        postBlob(blob, link.download)
    }, false);

    //download button
    var buttondownl = document.createElement('button');
    buttondownl.innerHTML = getURLParameter('download');
    buttondownl.id = 'download';
    buttondownl.addEventListener('click', function () {
        link.click()
    }, false);

    //add the new audio and a elements to the li element
    li.appendChild(au);
    //li.appendChild(link);
    li.appendChild(button);
    li.appendChild(buttondownl);

    //add the li element to the ordered list
    recordingsList.appendChild(li);
}


//helper function
function __log(e, data) {
    log.innerHTML += "\n" + e + " " + (data || '');
}