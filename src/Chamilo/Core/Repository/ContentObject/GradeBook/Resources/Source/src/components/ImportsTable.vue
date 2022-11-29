<i18n>
{
    "en": {
        "all-imports": "All imports",
        "auth-absent": "Authorized absent",
        "no-score-found": "No score found",
        "not-subscribed": "Not subscribed to course",
        "show": "Show",
        "total": "Total",
        "user-not-in-course": "Student is not subscribed to this course",
        "valid-imports": "Valid imports"
    },
    "nl": {
        "all-imports": "Alle imports",
        "auth-absent": "Gewettigd afwezig",
        "no-score-found": "Geen score gevonden",
        "not-subscribed": "Niet ingeschreven in cursus",
        "show": "Toon",
        "total": "Totaal",
        "user-not-in-course": "Student maakt geen deel uit van deze cursus",
        "valid-imports": "Geldige imports"
    }
}
</i18n>

<template>
    <div>
        <ul v-if="hasInvalidResults" role="tablist" class="nav mod-imports u-flex u-align-items-baseline">
            <li role="presentation" :class="{active: tab === 'all'}" @click="tab = 'all'"><a aria-controls="imports-table" role="tab">{{ $t('all-imports') }}</a></li>
            <li role="presentation" :class="{active: tab === 'valid'}" @click="tab = 'valid'"><a aria-controls="imports-table" role="tab">{{ $t('valid-imports') }}</a></li>
            <li role="presentation" :class="{active: tab === 'invalid'}" @click="tab = 'invalid'"><a aria-controls="imports-table" role="tab">{{ $t('not-subscribed') }}<span class="badge mod-invalid">{{ invalidResultRows.length }}</span></a></li>
        </ul>
        <table id="imports-table" class="imports-table">
            <thead>
                <tr class="table-row table-head-row">
                    <th v-for="field in fields" :key="`field-${field.key}`" class="table-cell" :class="{'mod-score': field.type === 'score'}">
                        {{ field.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(result, row_index) in filteredResultRows" class="table-row table-body-row" :class="{ 'mod-invalid': (showAll || !hasInvalidResults) ? !result.valid : showInvalid}" :key="`result-row-${row_index}`">
                    <td v-for="(field, col_index) in fields" class="table-cell" :class="{'mod-score': field.type === 'score', 'mod-comment': col_index === 4 && field.type === 'string'}" :key="`result-${row_index}-${col_index}`" :title="(!result.valid && field.key === 'id') ? $t('user-not-in-course') : ((col_index === 4 && field.type === 'string') ? result[field.key] : '')">
                        <div v-if="(showAll || !hasInvalidResults) && field.key === 'id'" class="u-flex u-justify-content-between u-align-items-center">
                            {{ result[field.key] }}
                            <i aria-hidden="true" class="fa" :class="result.valid ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                            <span v-if="!result.valid" class="sr-only">{{ $t('user-not-in-course') }}</span>
                        </div>
                        <div v-else-if="field.type === 'score' && isNullScore(result[field.key])" :title="$t('no-score-found')" class="color-code mod-none">
                            <span class="sr-only">{{ $t('no-score-found') }}</span>
                        </div>
                        <div v-else-if="field.type === 'score' && isAuthAbsentScore(result[field.key])" class="color-code amber-700" :title="$t('auth-absent')">
                            <span>{{ result[field.key] }}</span>
                        </div>
                        <template v-else><span>{{ result[field.key] }}</span></template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {CSVImportField, CSVImportResult} from '../domain/GradeBook';

@Component({})
export default class ImportsTable extends Vue {
    private tab = 'all';

    private pagination = {
        currentPage: 1,
        itemsPerPage: 5
    };

    private sortBy = 'lastname';
    private sortDesc = false;

    @Prop({type: Array, default: () => []}) readonly fields!: CSVImportField[];
    @Prop({type: Array, default: () => []}) readonly results!: CSVImportResult[];

    isNullScore(score: number|string|null) {
        return score === null;
    }

    isAuthAbsentScore(score: number|string|null) {
        return typeof score === 'string' && (score.toLowerCase() === 'aabs' || score.toLowerCase() === 'gafw');
    }

    get showValid() {
        return this.tab === 'valid';
    }

    get showInvalid() {
        return this.tab === 'invalid';
    }

    get showAll() {
        return this.tab === 'all';
    }

    get validResultRows() {
        return this.results.filter(v => v.valid);
    }

    get invalidResultRows() {
        return this.results.filter(v => !v.valid);
    }

    get hasInvalidResults() {
        return this.invalidResultRows.length > 0;
    }

    get filteredResultRows() {
        if (this.showValid) { return this.validResultRows; }
        if (this.showInvalid) { return this.invalidResultRows; }
        return this.results;
    }
}
</script>

<style scoped>
.nav.mod-imports {
    font-size: 13px;
    margin-bottom: 3px;
}
.nav.mod-imports > li:not(:last-child) {
    border-right: 1px solid #b6dbf1;
}
.nav.mod-imports > li > a {
    padding: 4px 8px;
}
.nav.mod-imports > li > a:hover, .nav.mod-imports > li > a:focus {
    background-color: initial;
    color: #143b5d;
    cursor: pointer;
}
.nav.mod-imports > li.active a {
    color: #1c5282;
    font-weight: 500;
}
.nav.mod-imports > li:last-child > a {
    display: flex;
}
.badge.mod-invalid {
    background-color: transparent;
    box-shadow: 1px 1px 1px hsla(207, 38%, 75%, .58);
    color: #e44a28;
}

.imports-table {
    font-size: 13px;
}

thead {
    background-color: #f8fbfb;
    border-bottom: 1px solid #ebebeb;
    border-top: 1px solid #ebebeb;
}

.table-row {
    border-bottom: 1px solid #ebebeb;
    border-left: 1px solid #ebebeb;
}

.table-body-row:first-child {
    background: linear-gradient(to bottom, #dde5e9 0, rgba(255, 255, 255, 0) 3px);
}

.table-body-row.mod-invalid {
    color: #e44a28;
}

.table-cell {
    border-right: 1px solid #ebebeb;
    padding: 5px;
}
.table-head-row .table-cell {
    color: #727879;
}

.table-cell.mod-score {
    text-align: right;
}

.table-cell.mod-comment {
    max-width: 240px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
/*.tbl-cell.mod-bl {
    border-right-color: transparent;
}*/
.table-cell .fa {
    margin-left: 3px;
}
.table-cell .fa-check-circle {
    color: limegreen;
}

.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: flex;
    height: 20px;
    justify-content: center;
    margin-left: auto;
    padding: 2px 4px;
    width: 40px;
}

.color-code > span {
    font-weight: 900;
    line-height: 12px;
}

.color-code.mod-none {
    justify-content: flex-end;
    width: 100%;

}

.color-code.mod-none > span {
    font-weight: 500;
}

.deep-orange-500 {
    --color: #ff5722;
    --text-color: white;
}

.amber-700 {
    --color: #ffa000;
    --text-color: white;
}
</style>