<i18n>
{
    "en": {
        "choose-file": "Choose file",
        "choose-type": "Choose type",
        "correct-mistakes": "Please correct any errors and",
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-Unknown": "An unknown error occurred. Your changes have possibly not been saved. You can try again later.",
        "file-with": "File with",
        "go-to-gradebook": "Go to gradebook",
        "import": "Import",
        "import-complete": "Import complete",
        "import-preview": "Preview",
        "import-results-overview": "You can find an overview of the results from the CSV file below. Click the button below to import. Only results that are valid will be imported.",
        "import-steps": "Import steps",
        "import-successful": "The results have been successfully imported.",
        "no-results-some-students": "Careful! For some subscribed students no matching results have been found. See below.<br>You can still make manual adjustments.",
        "question-upload": "What kind of file do you want to upload?",
        "reupload-results": "reupload",
        "select-file": "Select a file...",
        "type-scores": "1 or more score columns",
        "type-scores-comments": "1 score column and 1 feedback column",
        "upload": "Upload",
        "user-not-in-course": "Student is not subscribed to this course",
        "without-results": "No results"
    },
    "nl": {
        "choose-file": "Kies bestand",
        "choose-type": "Kies type",
        "correct-mistakes": "Gelieve de fout(en) te verbeteren en de resultaten",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Er deed zich een onbekende fout voor. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "file-with": "Bestand met",
        "go-to-gradebook": "Ga naar puntenboekje",
        "import": "Importeer",
        "import-complete": "Importeren voltooid",
        "import-preview": "Voorbeeldweergave",
        "import-results-overview": "Hieronder vind je een overzicht van de resultaten uit het CSV-bestand. Klik op de knop hieronder om te importeren. Enkel de geldige resultaten zullen worden geïmporteerd.",
        "import-steps": "Import-stappen",
        "import-successful": "De resultaten werden succesvol geïmporteerd.",
        "no-results-some-students": "Let op! Voor sommige ingeschreven studenten werden geen resultaten gevonden. Zie hieronder.<br>Gelieve deze handmatig aan te passen.",
        "question-upload": "Wat voor bestand wil je opladen?",
        "reupload-results": "opnieuw op te laden",
        "select-file": "Kies een bestand...",
        "type-scores": "1 of meerdere scorekolommen",
        "type-scores-comments": "1 scorekolom en 1 feedbackkolom",
        "upload": "Upload",
        "user-not-in-course": "Student maakt geen deel uit van deze cursus",
        "without-results": "Zonder resultaat"
    }
}
</i18n>

<template>
    <div id="app">
        <div id="gradebook-import">
            <ol role="navigation" class="nav nav-tabs mod-steps" :aria-label="$t('import-steps')">
                <li class="nav-item u-cursor-pointer" :aria-current="chooseTypeActive ? 'step' : null" :class="{'active': chooseTypeActive, 'done': importType}"><a class="nav-link u-block" @click="reload"><span class="step u-inline-block">1</span>{{ $t('choose-type') }}</a></li>
                <li class="nav-item" :aria-current="chooseFileActive ? 'step' : null" :class="{'active': chooseFileActive, 'done': imported || resultsLoaded}"><a class="nav-link u-block"><span class="step u-inline-block">2</span>{{ $t('choose-file') }}</a></li>
                <li class="nav-item" :aria-current="previewActive ? 'step' : null" :class="{'active': previewActive, 'done': resultsLoaded}"><a class="nav-link u-block"><span class="step u-inline-block">3</span>{{ $t('import-preview') }}</a></li>
                <li class="nav-item" :aria-current="importCompleteActive ? 'step' : null" :class="{'active': importCompleteActive}"><a class="nav-link u-block"><span class="step u-inline-block">4</span>{{ $t('import-complete') }}</a></li>
            </ol>
            <div v-if="chooseTypeActive">
                <p class="gradebook-import-type u-font-medium">{{ $t('question-upload') }}</p>
                <div class="u-flex u-gap-small-2x">
                    <button class="btn btn-light fs-13" @click="importType = 'scores'">{{ $t('type-scores') }}</button>
                    <button class="btn btn-default fs-13" @click="importType = 'scores_comments'">{{ $t('type-scores-comments') }}</button>
                </div>
            </div>
            <div v-if="chooseFileActive && !hasError">
                <div class="gradebook-import-file u-font-medium">{{ $t('file-with') }} {{ importType === 'scores' ? $t('type-scores') : $t('type-scores-comments') }}</div>
                <csv-import-info :import-type="importType" />
                <input type="file" name="file" id="file" class="inputfile" ref="inputfile" @change="filename=$event.target.value.split('\\').pop()">
                <div class="u-flex">
                    <label for="file" class="btn btn-default lbl-input-file u-font-normal" :class="{'mod-selected': !!filename}" :title="$t('select-file')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg> <span>{{ filename || $t('select-file') }}</span></label>
                    <button v-if="filename" id="uploadbutton" type="button" value="Upload" @click="uploadCSV" class="btn btn-primary" :disabled="hasError || !importType">{{ $t('upload') }}</button>
                </div>
            </div>
            <div v-if="chooseFileActive && hasError" class="import-errors alert alert-danger">
                <span class="error-filename u-inline-block u-font-medium" v-if="has500Error">{{ filename }}:</span>
                <div class="errors" :class="{'mb-20': has500Error}" v-html="error"></div>
                <div class="u-font-medium" v-if="has500Error">{{ $t('correct-mistakes') }} <a href="#" @click.stop="reload">{{ $t('reupload-results') }}</a>.</div>
            </div>
            <template v-if="previewActive && !hasError">
                <div class="csv-import-info u-flex u-align-items-start">
                    <p>{{ $t('import-results-overview') }}</p>
                    <div><button :title="$t('import')" class="btn btn-primary" @click="uploadResults"><span aria-hidden="true" class="glyphicon glyphicon-arrow-right"></span> {{ $t('import') }}</button></div>
                </div>
                <imports-table :fields="fields" :results="results" :max-scores="maxScores" />
            </template>
            <div v-if="previewActive && hasError" class="import-errors alert alert-danger">
                <div class="errors" v-html="error"></div>
            </div>
            <template v-if="importCompleteActive" >
                <div class="alert alert-info mod-import-completed">
                    <p>{{ $t('import-successful') }}</p>
                    <p v-if="missingUsers.length" v-html="$t('no-results-some-students')"></p>
                    <p><a :href="apiConfig.gradeBookRootURL" class="u-font-medium"><i class="fa fa-arrow-right" aria-hidden="true"></i> {{ $t('go-to-gradebook') }}</a></p>
                </div>
                <p v-if="missingUsers.length" class="gradebook-import-missing-users u-font-medium">{{ $t('without-results') }}:</p>
                <missing-users-table v-if="missingUsers.length" :missing-users="missingUsers"></missing-users-table>
            </template>
        </div>
        <div v-if="debugServerResponse" id="server-response"></div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import ImportsTable from './components/ImportsTable.vue';
import MissingUsersTable from './components/MissingUsersTable.vue';
import CsvImportInfo from './components/CSVImportInfo.vue';
import {CSVImportField, CSVImportResult, CSVImportTotals} from './domain/GradeBook';
import axios from 'axios';
import {logResponse} from './domain/Log';

interface APIConfig {
    processCsvURL: string;
    importCsvURL: string;
    readonly csrfToken: string;
}

const TIMEOUT_SEC = 30;

@Component({
    components: { ImportsTable, MissingUsersTable, CsvImportInfo }
})
export default class ImporterApp extends Vue {
    private importType: string|null = null;
    private filename = '';
    private hasError = false;
    private has500Error = false;
    private error = '';
    private imported = false;
    private missingUsers: any[] = [];
    private results: CSVImportResult[] = [];
    private fields: CSVImportField[] = [];
    private maxScores: CSVImportTotals = {};

    @Prop({type: Object, default: () => null}) readonly apiConfig!: APIConfig;
    @Prop({type: Number, required: true}) readonly gradebookDataId!: number;
    @Prop({type: Number, required: true}) readonly currentVersion!: number;
    @Prop({type: Boolean, default: false}) readonly debugServerResponse!: boolean;

    get chooseTypeActive() {
        return !this.importType;
    }

    get chooseFileActive() {
        return this.importType && !(this.imported || this.resultsLoaded);
    }

    get previewActive() {
        return !this.imported && this.resultsLoaded;
    }

    get importCompleteActive() {
        return this.imported;
    }

    get inputFile() {
        return this.$refs['inputfile'] as HTMLInputElement;
    }

    get resultsLoaded() {
        return this.fields.length > 0;
    }

    setError(msg: string) {
        this.hasError = true;
        this.error = msg;
    }

    handleError(err: any) {
        let error: any;
        if (err?.isAxiosError && err.message?.toLowerCase().indexOf('timeout') !== -1) {
            error = { 'type': 'Timeout' };
        } else if (err?.response?.data?.error) {
            error = err.response.data.error;
        } else if (err?.type) {
            error = err;
        }
        if (!error.type) {
            error = { 'type': 'Unknown' };
        }
        this.setError(`${this.$t('error-' + error.type)}`);
    }

    async uploadCSV() {
        if (!this.importType) { return; }
        const fileData = this.inputFile.files![0];
        const formData = new FormData();
        if (this.apiConfig.csrfToken) {
            formData.append('_csrf_token', this.apiConfig.csrfToken);
        }
        formData.append('importType', this.importType);
        formData.append('file', fileData);

        try {
            const res = await axios.post(this.apiConfig.processCsvURL, formData, {timeout: TIMEOUT_SEC * 1000});
            logResponse(res.data);
            if (res.data?.fields !== undefined && res.data?.results !== undefined) {
                const {fields, max_scores, results} = res.data;
                this.fields = fields;
                this.maxScores = max_scores || {};
                this.results = results;
            } else if (res.data?.result_code === 500) {
                this.has500Error = true;
                this.setError(res.data.result_message);
            } else if (typeof (res.data as unknown) === 'string' && res.data.toLowerCase().indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            } else {
                throw { 'type': 'Unknown' };
            }
        } catch (err) {
            logResponse(err);
            this.handleError(err);
        }
    }

    async uploadResults() {
        const formData = new FormData();
        formData.set('gradebookDataId', String(this.gradebookDataId));
        formData.set('version', String(this.currentVersion));
        if (this.apiConfig.csrfToken) {
            formData.append('_csrf_token', this.apiConfig.csrfToken);
        }
        formData.append('importType', this.importType!);
        const scores = this.importType === 'scores_comments' ?
            [this.getResultsForField(this.fields[3], this.fields[4])] :
            this.fields.slice(3).map(field => this.getResultsForField(field));
        formData.set('importScores', JSON.stringify(scores));

        try {
            const res: any = await axios.post(this.apiConfig.importCsvURL, formData, {timeout: TIMEOUT_SEC * 1000});
            logResponse(res.data);

            if (res.data?.missing_users !== undefined) {
                this.missingUsers = res.data.missing_users;
                this.imported = true;
            } else if (res.data?.result_code === 500) {
                this.has500Error = true;
                this.setError(res.data.result_message);
            } else if (typeof (res.data as unknown) === 'string' && res.data.toLowerCase().indexOf('login') !== -1) {
                throw { 'type': 'LoggedOut' };
            } else {
                throw { 'type': 'Unknown' };
            }
        } catch (err) {
            logResponse(err);
            this.handleError(err);
        }
    }

    getResultsForField(scoreField: CSVImportField, commentField: CSVImportField|null = null) {
        return {
            label: scoreField.label,
            maxScore: this.maxScores[scoreField.key] || null,
            results: this.results.filter(v => v.valid).map(v => {
                const score = v[scoreField.key];
                const comment = commentField === null ? null : (v[commentField.key] || null);
                const authAbsent = typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
                return {
                    id: v.user_id,
                    score: authAbsent ? null : score,
                    authAbsent,
                    comment
                };
            })
        };
    }

    reload() {
        window.location.reload();
    }
}
</script>

<style scoped>
.btn-light {
    background-color: #e5eff6;
    border-color: #c4d9ea;
    color: #3e5d75;
}
.btn-light:active {
    box-shadow: inset 0 3px 5px #c5d9ea;
}
.btn-light:hover, .btn-light:focus {
    background-color: #c1d8eb;
    color: #30485a;
}
.btn-light:active:hover {
    box-shadow: inset 0 3px 5px #96bad9;
}
.fs-13 {
    font-size: 13px;
}
.nav.nav-tabs.mod-steps {
    border: 0;
    border-radius: 0 5px 0 0;
    list-style: none;
    margin-bottom: 15px;
    overflow: hidden;
    padding: 0;
}
.nav.nav-tabs.mod-steps > li:first-child a {
    cursor: pointer;
}
.nav.mod-imports > li:last-child > a {
    display: flex;
}
.nav.nav-tabs.mod-steps > li > a, .nav.nav-tabs.mod-steps > li > a:hover {
    background: #f3f3f3;
    border: 0;
    border-radius: 0;
    color: #999;
    line-height: 20px;
    margin-bottom: 3px;
    outline-style: none;
    padding: 10px 25px 10px 30px;
    position: relative;
    text-decoration: none;
}
.nav.nav-tabs.mod-steps > li:first-child > a {
    padding-left: 20px;
}
.nav.nav-tabs.mod-steps > li > a:before {
    border-bottom: 20px inset transparent;
    border-left: 13px solid #fff;
    border-top: 20px inset transparent;
    content: '';
    display: block;
    height: 0;
    left: 100%;
    margin-left: 2px;
    position: absolute;
    top: 0;
    width: 0;
    z-index: 1;
}
.nav.nav-tabs.mod-steps > li > a:after {
    border-bottom: 20px solid transparent;
    border-left: 12px solid #f3f3f3;
    border-top: 20px solid transparent;
    content: '';
    display: block;
    height: 0;
    position: absolute;
    right: -12px;
    top: 0;
    width: 0;
    z-index: 2;
}
.nav.nav-tabs.mod-steps > li.done > a {
    background: #e8edf3;
    color: #1c303f;
}
.nav.nav-tabs.mod-steps > li.done > a:after {
    border-left-color: #e8edf3;
}
.nav.nav-tabs.mod-steps > li.active > a {
    color: #fff;
    background: #6192b8;
}
.nav.nav-tabs.mod-steps > li.active > a:after {
    border-left-color: #6192b8;
}
.nav.nav-tabs.mod-steps > li .step {
    background-color: hsla(0, 0%, 100%, .75);
    border-radius: 50%;
    box-shadow: 1px 1px 1px #c4c4c4;
    color: #919191;
    font-size: 12px;
    height: 18px;
    line-height: 18px;
    margin: 0 7px 0 auto;
    padding: 0;
    text-align: center;
    width: 18px;
}
.nav.nav-tabs.mod-steps > li.done .step {
    background-color: hsla(0, 0%, 100%, .55);
    box-shadow: 1px 1px 1px #a4c0d6;
    color: #1c303f;
}
.nav.nav-tabs.mod-steps > li.active .step {
    background-color: hsla(0, 0%, 0%, .1);
    box-shadow: 1px 1px 1px #3a6c92;
    color: #fff;
}

.gradebook-import-type {
    color: #507e86;
    font-size: 14px;
    margin-bottom: 14px;
    padding: 0 0 0 5px;
}

.gradebook-import-file {
    color: #507e86;
    margin-bottom: 14px;
}

.inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}
.lbl-input-file.mod-selected {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}
.lbl-input-file.mod-selected:not(:hover):not(:active) {
    background-color: #fcfcfc;
}
.lbl-input-file.mod-selected + button {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.lbl-input-file svg, #uploadbutton svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
    fill: currentColor;
    margin-top: -0.25em;
    margin-right: 0.25em;
}

.alert.mod-import-completed {
    margin-top: 15px;
    max-width: 90ch;
}

.csv-import-info {
    color: #31708f;
    flex-direction: column;
    font-size: 13px;
    gap: 8px;
    margin-bottom: 10px;
    max-width: 90ch;
    padding: 10px;
}
.gradebook-import-missing-users {
    color: #507e86;
    margin-left: 5px;
}
.import-errors {
    margin-top: 15px;
    max-width: 90ch;
}
.mb-20 {
    margin-bottom: 20px;
}
.errors > :first-child {
    border-bottom: 1px dotted hsla(0, 45%, 45%, .33);
    border-radius: 3px;
    color: #944646;
    font-weight: bold;
}
.errors > :nth-child(n+2) {
    border-bottom: 1px dotted hsla(0, 45%, 45%, .33);
    background-color: hsla(0, 0%, 100%, .20);
    border-radius: 3px;
    color: #944646;
    padding: 1px 3px;
}
.error-filename {
    font-size: 18px;
    margin-bottom: 5px;
}

</style>