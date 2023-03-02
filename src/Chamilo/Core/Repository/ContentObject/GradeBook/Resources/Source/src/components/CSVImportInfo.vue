<i18n>
{
    "en": {
        "comment": "comment",
        "csv-must-look-like": "The CSV file must look like this",
        "import-comment-title": "Choose a title for the column of feedback you wish to import (can be left empty).",
        "import-id": "One of the following: <ul><li>email address</li><li>username</li><li>official code</li></ul>",
        "import-score": "One of the following: <ul><li>number</li><li>aabs (authorized absent)</li></ul>",
        "import-score-title": "Choose a title for the column of scores you wish to import.",
        "mandatory-fields": "mandatory fields are marked in bold",
        "max-score": "Max score (for example: 20)",
        "title": "title"
    },
    "nl": {
        "comment": "commentaar",
        "csv-must-look-like": "Het CSV bestand moet er als volgt uit zien",
        "import-comment-title": "Kies hier een titel voor de feedbackkolom die je wenst te importeren (mag ook leeggelaten worden).",
        "import-id": "Een van de volgende: <ul><li>e-mailadres</li><li>gebruikersnaam</li><li>officiële code (stamboeknummer)</li></ul>",
        "import-score": "Een van de volgende: <ul><li>cijfer</li><li>gafw (gewettigd afwezig)</li></ul>",
        "import-score-title": "Kies hier een titel voor de scorekolom die je wenst te importeren.",
        "mandatory-fields": "verplichte velden zijn in het vet aangeduid",
        "max-score": "Maximumscore (bvb.: 20)",
        "title": "titel"
    }
}
</i18n>

<template>
    <div>
        <p>{{ $t('csv-must-look-like') }} ({{ $t('mandatory-fields') }}):</p>
        <div class="csv-example" v-if="importType === 'scores'">
            <div><b>lastname</b>;<b>firstname</b>;<b class="csv-field csv-header-id">id</b>;<b id="csv-header-title-1" class="csv-field csv-header-title-1 u-cursor-help">{{ $t('title') }} 1</b>;<span id="csv-header-title-2" class="csv-field csv-header-title-2 u-cursor-help">{{ $t('title') }} 2</span>;&mldr;</div>
            <div><b>xxx</b>;<b>xxx</b>;<b id="csv-expl-id" class="csv-field csv-field-id u-cursor-help">xxx</b>;<b id="csv-expl-title-1" class="csv-field csv-field-title-1 u-cursor-help">xxx</b>;<span id="csv-expl-title-2" class="csv-field csv-field-title-2 u-cursor-help">xxx</span>;&mldr;</div>
        </div>
        <div class="csv-example" v-else>
            <div><b>lastname</b>;<b>firstname</b>;<b class="csv-field csv-header-id">id</b>;<b id="csv-header-title-1" class="csv-field csv-header-title-1 u-cursor-help">{{ $t('title') }}</b>;<b id="csv-header-comment" class="csv-field csv-header-comment u-cursor-help">{{ $t('comment') }}</b></div>
            <div><b>xxx</b>;<b>xxx</b>;<b id="csv-expl-id" class="csv-field csv-field-id u-cursor-help">xxx</b>;<b id="csv-expl-title-1" class="csv-field csv-field-title-1 u-cursor-help">xxx</b>;<b>xxx</b></div>
        </div>
        <p style="max-width: 90ch">Indien je geen procentuele score wil importeren maar een andere totaalscore (bvb. op 20 punten) voeg je een extra tweede regel in die er als volgt uitziet:</p>
        <div class="csv-example" v-if="importType === 'scores'">
            <div><b>totaal</b>;;;<b id="csv-expl-max-1" class="csv-field csv-field-title-1 u-cursor-help">xxx</b>;<span id="csv-expl-max-2" class="csv-field csv-field-title-2 u-cursor-help">xxx</span>;&mldr;</div>
        </div>
        <div class="csv-example" v-else>
            <div><b>totaal</b>;;;<b id="csv-expl-max-1" class="csv-field csv-field-title-1 u-cursor-help">xxx</b>;</div>
        </div>
        <b-popover target="csv-expl-id" triggers="hover" placement="bottom">
            <div class="csv-import-help mod-list">
                <div class="u-font-medium" style="color: #507e86;">id</div>
                <div v-html="$t('import-id')"></div>
            </div>
        </b-popover>
        <b-popover target="csv-header-title-1" triggers="hover" placement="bottom">
            <div class="csv-import-help" v-html="$t('import-score-title')"></div>
        </b-popover>
        <b-popover target="csv-expl-title-1" triggers="hover" placement="bottom">
            <div class="csv-import-help mod-list" v-html="$t('import-score')"></div>
        </b-popover>
        <b-popover target="csv-expl-max-1" triggers="hover" placement="bottom">
            <div class="csv-import-help" v-html="$t('max-score')"></div>
        </b-popover>
        <b-popover target="csv-header-title-2" triggers="hover" placement="bottom">
            <div class="csv-import-help" v-html="$t('import-score-title')"></div>
        </b-popover>
        <b-popover target="csv-expl-title-2" triggers="hover" placement="bottom">
            <div class="csv-import-help mod-list" v-html="$t('import-score')"></div>
        </b-popover>
        <b-popover target="csv-expl-max-2" triggers="hover" placement="bottom">
            <div class="csv-import-help" v-html="$t('max-score')"></div>
        </b-popover>
        <b-popover target="csv-header-comment" triggers="hover" placement="bottom">
            <div class="csv-import-help" v-html="$t('import-comment-title')"></div>
        </b-popover>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({})
export default class CsvImportInfo extends Vue {
    @Prop({type: String, default: 'scores'}) readonly importType!: 'scores'|'scores_comments';
}
</script>

<style scoped>
.csv-example {
    background-color: #f7f7f7;
    border: 1px solid #f0f0f0;
    border-radius: 4px;
    color: #666666;
    font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
    font-size: 13px;
    margin-bottom: 20px;
    margin-left: 15px;
    max-width: 100ch;
    padding: 9.5px;
}
.csv-field {
    border: 1px dashed transparent;
    border-radius: 3px;
    padding: 0 2px;
}
.csv-header-id {
    background-color: hsl(30, 100%, 90%);
}
.csv-field-id {
    background-color: hsl(30, 100%, 90%);
    border-bottom-color: #888;
}
.csv-header-title-1, .csv-field-title-1 {
    background-color: hsl(170, 100%, 93%);
    border-bottom-color: #888;
}
.csv-header-title-2, .csv-field-title-2 {
    background-color: hsl(75, 100%, 90%);
    border-bottom-color: #888;
}
.csv-header-title-1, .csv-header-title-2, .csv-header-comment {
    font-style: italic;
}
.csv-header-comment {
    border-bottom-color: #888;
}
.csv-import-popover {
    top: 5px!important;
}
.csv-import-help {
    font-size: 13px;
    padding: 8px;
}
.csv-import-help.mod-list {
    padding: 8px 8px 0;
}
.csv-import-help.mod-list::v-deep ul {
    padding: 5px 10px 0 25px;
}
</style>