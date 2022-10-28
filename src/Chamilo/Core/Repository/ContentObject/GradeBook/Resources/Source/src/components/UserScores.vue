<i18n>
{
    "en": {
        "final-score": "Final score",
        "title": "Title",
        "score": "Score",
        "weight": "Weight"
    },
    "nl": {
        "final-score": "Eindcijfer",
        "title": "Titel",
        "score": "Score",
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <div style="min-width: fit-content;width: 400px;margin-left: 15px;margin-top: 20px;">
        <b-table-simple class="gradebook-table" style="margin-right:10px">
            <b-thead>
                <b-tr class="table-row table-head-row">
                    <b-th style="border-right-color: transparent;">{{ $t('title') }}</b-th>
                    <!--<b-th style="border-right-color: transparent;" class="u-text-end">{{ $t('weight') }}</b-th>-->
                    <b-th class="u-text-end">{{ $t('score') }}</b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <template v-for="category in gradeBook.allCategories">
                    <b-tr class="table-row table-body-row" :key="`cat-${category.id}`" v-if="category.columnIds.length && gradeBook.allCategories.length && gradeBook.allCategories[0].id !== 0">
                        <b-td colspan="2" class="u-font-medium" style="color: #5885a2;border-left-color: transparent;border-right-color: transparent;padding-bottom: 6px;padding-top:16px">{{ category.title }}</b-td>
                    </b-tr>
                    <template v-for="columnId in category.columnIds">
                        <b-tr :key="`col-${category.id}-${columnId}`" :id="`col-${category.id}-${columnId}`" class="table-row table-body-row result-row">
                            <b-td class="category-color u-relative" style="border-right-color: transparent" :style="`--color: ${category.color};`">{{ gradeBook.getTitle(columnId) }}</b-td>
                            <!--<b-td v-if="gradeBook.countsForEndResult(columnId)" style="font-size: 12px;line-height:20px;color:#477b7b;border-right-color: transparent;">
                                <div class="u-flex u-align-items-center u-justify-content-end">{{ gradeBook.getWeight(columnId)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                            </b-td>
                            <b-td v-else class="u-text-end" style="font-size: 12px;line-height:20px;color:#477b7b;border-right-color: transparent;"><i>Telt niet mee voor eindresultaat</i></b-td>-->
                            <b-td>
                                <div class="u-flex u-align-items-center u-justify-content-end">
                                    <template v-if="gradeBook.getResultComment(columnId, userId)">
                                        <i class="fa fa-comment-o" style="color: #5885a2;font-size:12px;line-height:14px;text-shadow:1px 1px #e2eaee;margin-right: 5px"></i>
                                    </template>
                                    <student-result :id="`result-${columnId}`" :result="gradeBook.getResult(columnId, userId)"
                                                    class="u-flex u-align-items-center u-justify-content-end" :class="{'uncounted-score': !gradeBook.countsForEndResult(columnId)}"></student-result>
                                    <!--{{ gradeBook.getResult(columnId, userId)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span>-->
                                </div>
                            </b-td>
                            <b-popover custom-class="gradebook-score-popover" :target="`col-${category.id}-${columnId}`" triggers="hover" placement="rightbottom">
                                <div class="score-info">
                                    <div v-if="gradeBook.countsForEndResult(columnId)" class="u-flex u-align-items-center" style="font-size: 12px;color:#477b7b;">Gewicht: {{ gradeBook.getWeight(columnId)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                                    <div v-else style="font-size: 12px;color:#477b7b"><i>Telt niet mee voor eindresultaat</i></div>
                                    <template v-if="gradeBook.getResultComment(columnId, userId)">
                                        <div style="font-size: 10px;color: #5885a3;padding-top: 4px;margin-top: 4px;margin-bottom: 2px;border-top: 1px solid #ebebeb">Feedback:</div>
                                        {{ gradeBook.getResultComment(columnId, userId) }}
                                    </template>
                                </div>
                            </b-popover>
                        </b-tr>
                    </template>
                </template>
                <b-tr v-if="gradeBook.allCategories.length && gradeBook.allCategories[0].id !== 0"><b-td colspan="2" style="padding: 12px;border-left-color: transparent;border-right-color: transparent"></b-td></b-tr>
                <b-tr class="table-row table-body-row">
                    <b-td style="color: #5885a2;font-weight: 700;background-color: #f5f9f9;border-color:#ebebeb;border-right-color: transparent;">{{ $t('final-score') }}</b-td>
                    <b-td style="background-color: #f5f9f9;font-weight: 500;border-color:#ebebeb;">
                        <div class="u-flex u-align-items-center u-justify-content-end">{{ gradeBook.getEndResult(userId)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                    </b-td>
                    <!--<b-td style="border-color: transparent"></b-td>-->
                </b-tr>
            </b-tbody>
        </b-table-simple>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook from '../domain/GradeBook';
import StudentResult from './StudentResult.vue';

@Component({
    components: { StudentResult },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        },
        formatNum2: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }
})
export default class UserScores extends Vue {
    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;

    get userId() {
        return this.gradeBook.users[0].id;
    }

    mounted() {
        console.log(this.gradeBook);
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
}

.table-body-row:first-child td {
    background: linear-gradient(to bottom, #e3eaed 0, #fff 4px);
    border-top: none;
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

.uncounted-score {
    color: #777;
    font-style: italic;
}

.score-info {
    font-size: 13px;
    /*max-width: 200px;*/
    padding: 6px 8px;
    width: 300px;
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