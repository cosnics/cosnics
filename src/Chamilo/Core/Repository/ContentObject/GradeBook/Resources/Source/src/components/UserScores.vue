<i18n>
{
    "en": {
        "count-towards-endresult-not": "Score does not count towards final result",
        "final-score": "Final score",
        "not-yet-released": "Not yet released",
        "title": "Title",
        "score": "Score",
        "weight": "Weight"
    },
    "nl": {
        "count-towards-endresult-not": "Score telt niet mee voor het eindresultaat",
        "final-score": "Eindcijfer",
        "not-yet-released": "Nog niet vrijgegeven",
        "title": "Titel",
        "score": "Score",
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <div>
        <b-table-simple class="gradebook-table">
            <b-thead>
                <b-tr class="table-row table-head-row">
                    <b-th>{{ $t('title') }}</b-th>
                    <b-th class="u-text-end">{{ $t('score') }}</b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <template v-for="category in gradeBook.allCategories">
                    <b-tr class="table-row table-body-row" :key="`cat-${category.id}`" v-if="category.columnIds.length && gradeBook.allCategories.length && gradeBook.allCategories[0].id !== 0">
                        <b-td colspan="2" class="table-category u-font-medium">{{ category.title }}</b-td>
                    </b-tr>
                    <b-tr v-for="column in getColumns(category)" :key="`col-${category.id}-${column.id}`" :id="`col-${category.id}-${column.id}`" class="table-row table-body-row result-row">
                        <b-td class="category-color u-relative" :style="`--color: ${category.color};`">{{ column.title }}</b-td>
                        <b-td>
                            <div v-if="column.released" class="u-flex u-align-items-center u-justify-content-end">
                                <i v-if="column.comment" class="fa fa-comment-o" aria-hidden="true"></i>
                                <student-result :id="`result-${column.id}`" :result="column.result"
                                                class="u-flex u-align-items-center u-justify-content-end" :class="{'uncounted-score': !column.countsForEndResult}"></student-result>
                            </div>
                            <div v-else class="u-flex u-align-items-center u-justify-content-end not-yet-released">{{ $t('not-yet-released') }}</div>
                        </b-td>
                        <b-popover custom-class="gradebook-score-popover" :target="`col-${category.id}-${column.id}`" triggers="hover" placement="rightbottom">
                            <div class="score-info">
                                <div v-if="column.countsForEndResult" class="u-flex u-align-items-center popover-weight-header">{{ $t('weight') }}: {{ column.weight|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                                <div v-else class="popover-count-endresult-not"><i>{{ $t('count-towards-endresult-not') }}</i></div>
                                <template v-if="column.comment">
                                    <div class="popover-feedback-header">Feedback:</div>
                                    {{ column.comment }}
                                </template>
                            </div>
                        </b-popover>
                    </b-tr>
                </template>
                <b-tr v-if="gradeBook.allCategories.length && gradeBook.allCategories[0].id !== 0" class="table-row table-body-row"><b-td colspan="2" class="table-empty-cell"></b-td></b-tr>
                <b-tr class="table-row table-body-row">
                    <b-td class="table-final-score-header">{{ $t('final-score') }}</b-td>
                    <b-td class="table-final-score u-font-medium">
                        <div v-if="!gradeBook.hasUnreleasedScores" class="u-flex u-align-items-center u-justify-content-end">{{ gradeBook.getEndResult(userId)|formatNum2 }}<template v-if="gradeBook.getDisplayTotal() === 100"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template><template v-else> / {{gradeBook.getDisplayTotal()}}</template></div>
                        <div v-else class="u-flex u-align-items-center u-justify-content-end not-yet-released">{{ $t('not-yet-released') }}</div>
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook, {Category, ColumnId, ResultType} from '../domain/GradeBook';
import StudentResult from './StudentResult.vue';

interface Column {
    id: ColumnId;
    released: boolean;
    countsForEndResult: boolean;
    title: string;
    weight: number;
    result: ResultType;
    comment: string|null;
}

@Component({
    components: { StudentResult },
    filters: {
        formatNum2: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class UserScores extends Vue {
    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;

    get userId() {
        return this.gradeBook.users[0].id;
    }

    getColumnData(columnId: ColumnId): Column {
        const gradeBook = this.gradeBook;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) { throw new Error(`GradeColumn with id ${columnId} not found.`); }

        return {
            id: columnId,
            released: column.released,
            countsForEndResult: column.countForEndResult,
            title: gradeBook.getTitle(column),
            weight: gradeBook.getWeight(column),
            result: gradeBook.getResult(columnId, this.userId),
            comment: gradeBook.getResultComment(columnId, this.userId)
        };
    }

    getColumns(category: Category): Column[] {
        return category.columnIds.map(columnId => this.getColumnData(columnId));
    }
}
</script>

<style lang="scss" scoped>
.gradebook-table {
    th, td {
        border: 1px solid #f0f0f0;
        border-left-color: #ebebeb;
        border-right-color: #ebebeb;
        font-size: 1.35rem;
        vertical-align: top;
    }

    .table-row {
        th, td {
            background-clip: padding-box;
        }
    }
}

.table.gradebook-table .table-head-row th {
    background-color: #f8fbfb;
    border: 1px solid #ebebeb;
    border-bottom: none;
    color: #5885a2;

    &:first-child {
        border-right-color: transparent;
    }
}

.table-body-row:first-child td {
    background: linear-gradient(to bottom, #e3eaed 0, #fff 4px);
    border-top: none;
}

.table-body-row .table-category {
    border-left-color: transparent;
    border-right-color: transparent;
    color: #5885a2;
    padding-bottom: 6px;
    padding-top: 16px;
}

.table-body-row.result-row:hover td {
    background-color: #f5f6f9;
}

.table-body-row.result-row:first-child:hover td {
    background: linear-gradient(to bottom, #e3eaed 0, #f5f6f9 4px);
}

.fa-percent {
    font-size: 1.1rem;
    margin-left: .15rem;
    opacity: .8;
}

.table-body-row .category-color {
    border-right-color: transparent;
}

.category-color:before {
    content: '';
    position: absolute;
    top: -1px;
    left: -7px;
    bottom: -1px;
    width: 7px;
    background-color: var(--color);
}

.table-body-row.result-row:first-child .category-color:before {
    top: 0;
}

.table-body-row .table-empty-cell {
    border-left-color: transparent;
    border-right-color: transparent;
    padding: 12px;
}

.table-body-row .table-final-score-header {
    background-color: #f5f9f9;
    border-color: #ebebeb;
    border-right-color: transparent;
    color: #5885a2;
    font-weight: 700;
}

.table-body-row .table-final-score {
    background-color: #f5f9f9;
    border-color: #ebebeb;
}

.fa-comment-o {
    color: #5885a2;
    font-size: 12px;
    line-height: 14px;
    margin-right: 5px;
    text-shadow: 1px 1px #e2eaee;
}

.uncounted-score {
    color: #777;
    font-style: italic;
}

.score-info {
    font-size: 13px;
    padding: 6px 8px;
    width: 300px;
}

.not-yet-released {
    color: #4d748f;
    font-size: 12.5px;
}

.popover-weight-header {
    color: #477b7b;
    font-size: 12px;
}

.popover-count-endresult-not {
    color: #477b7b;
    font-size: 12px;
}

.popover-feedback-header {
    font-size: 10px;color: #5885a3;padding-top: 4px;margin-top: 4px;margin-bottom: 2px;border-top: 1px solid #ebebeb;
}
</style>
<style>
.gradebook-score-popover {
    border-color: #ebebeb;
    box-shadow: 0 3px 10px rgb(0 0 0 / 10%);
    left: 8px!important;
    top: -12px!important;
    max-width: 300px;
}
</style>