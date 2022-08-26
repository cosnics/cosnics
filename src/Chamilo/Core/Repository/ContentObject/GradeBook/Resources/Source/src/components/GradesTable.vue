<template>
    <div class="table-wrap">
        <b-table-simple bordered striped class="gradebook-table" :class="{'is-dragging': isDraggingColumn, 'is-category-drop': categoryDropArea !== null }">
            <b-thead>
                <b-tr class="table-row table-head-row">
                    <b-th class="col-sticky table-student"></b-th>
                    <draggable :list="gradeBook.categories" tag="div" style="display: contents" @end="onDragEnd" :disabled="catEditItemId !== null">
                        <template v-for="{id, title, color, columnIds} in gradeBook.categories">
                            <b-th draggable :key="`category-${id}`" :colspan="Math.max(columnIds.length, 1)"
                                  :class="{'is-droppable': categoryDropArea === id}" style="color:#3e5c6e" :style="`background-color: ${color};` + (catEditItemId === id ? 'position: relative; z-index: 2;' : '')"
                                  @dragstart="startDragCategory($event, id)" @dragover.prevent="onDropAreaOverEnter($event, id)" @dragenter.prevent="onDropAreaOverEnter($event, id)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, id)">
                                <div v-if="id !== 0" style="display: flex; cursor: pointer; justify-content: space-between;align-items: center" @dblclick="catEditItemId = id">{{ title }}
                                    <button style="padding:0; background: none; border: none;margin-left: 15px" @click="$emit('category-settings', id)"><i class="fa fa-gear" style="color:#55717c;margin-left: auto;display:inline-block"></i></button>
                                </div>
                                <item-title-input v-if="catEditItemId === id" :item-title="title" @cancel="catEditItemId = null" @ok="setCategoryTitle(id, $event)"></item-title-input>
                            </b-th>
                        </template>
                    </draggable>
                    <b-th v-if="showNullCategory" :colspan="Math.max(gradeBook.nullCategory.columnIds.length, 1)" class="mod-no-category-assigned" :class="{'is-droppable': categoryDropArea === 0}" title="Zonder categorie"
                          @dragover.prevent="onDropAreaOverEnter($event, 0)" @dragenter.prevent="onDropAreaOverEnter($event, 0)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, 0)"
                    ></b-th>
                    <b-th class="col-sticky table-student-total"></b-th>
                </b-tr>
                <b-tr class="table-row table-head-row">
                    <b-th class="col-sticky table-student">Student</b-th>
                    <draggable v-for="({id, columnIds}) in displayedCategories" :key="`category-score-${id}`" :list="columnIds" tag="div" style="display: contents" ghost-class="ghost" @end="onDragEnd" :disabled="editItemId !== null || weightEditItemId !== null">
                        <b-th v-if="columnIds.length === 0" :key="`item-id-${id}`"></b-th>
                        <b-th v-else v-for="(columnId) in columnIds" :key="`${columnId}-name`" draggable @dragstart="startDragColumn($event, columnId)" :style="(editItemId === columnId || weightEditItemId === columnId) ? 'position: relative; z-index: 2' : ''" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, -1)">
                            <div style="cursor: pointer;display:flex;justify-content:space-between;align-items:center" @dblclick="editItemId = columnId"><span style="white-space: nowrap"><i v-if="gradeBook.isGrouped(columnId)" class="fa fa-group" style="margin-right: .5rem"></i>{{ gradeBook.getTitle(columnId) }}</span>
                                <button style="padding:0; background: none; border: none;margin-left: 15px" @click="$emit('item-settings', columnId)"><i class="fa fa-gear" style="margin-left: auto;display:inline-block"></i></button>
                            </div>
                            <div class="weight" :class="{'mod-custom': gradeBook.getGradeColumn(columnId).weight !== null}" @dblclick="weightEditItemId = columnId" v-if="gradeBook.countsForEndResult(columnId)">{{ gradeBook.getWeight(columnId)|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                            <item-title-input v-if="editItemId === columnId" :item-title="gradeBook.getTitle(columnId)" @cancel="editItemId = null" @ok="setTitle(columnId, $event)"></item-title-input>
                            <weight-input v-if="weightEditItemId === columnId" :item-weight="gradeBook.getWeight(columnId)" @cancel="weightEditItemId = null" @ok="setWeight(columnId, $event)"></weight-input>
                        </b-th>
                    </draggable>
                    <b-th class="col-sticky table-student-total">Eindcijfer</b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr v-for="user in gradeBook.users" :key="'user-' + user.id" class="table-row table-body-row">
                    <b-td class="col-sticky table-student">{{ user.firstName }} {{ user.lastName }}</b-td>
                    <template v-for="category in displayedCategories">
                        <b-td v-if="category.columnIds.length === 0" :key="`category-results-${category.id}`"></b-td>
                        <b-td v-else v-for="columnId in category.columnIds" :key="`${category.id}-${columnId}-result`" :style="editStudentScoreId === user.id && editScoreId === columnId ? 'position: relative; z-index: 2' : ''">
                            <student-result :result="getResult(columnId, user.id)"
                                            style="cursor: pointer;" :style="gradeBook.countsForEndResult(columnId) ? '' : 'font-style: italic'"
                                            @edit="showStudentScoreDialog(id, columnId)"></student-result>
                        </b-td>
                    </template>
                    <b-td class="col-sticky table-student-total"> {{ getEndResult(user.id)|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></b-td>
                </b-tr>
                <!--<b-tr v-for="{id, student, results} in gradeBook.resultsData" :key="student" class="table-row table-body-row">
                    <b-td class="col-sticky table-student">{{ student }}</b-td>
                    <template v-for="category in displayedCategories">
                        <b-td v-if="category.columnIds.length === 0" :key="`category-results-${category.id}`"></b-td>
                        <b-td v-else v-for="columnId in category.columnIds" :key="`${category.id}-${columnId}-result`" :style="editStudentScoreId === id && editScoreId === columnId ? 'position: relative; z-index: 2' : ''">
                            <student-result :result="gradeBook.getResult(results, columnId)"
                                            style="cursor: pointer;" :style="gradeBook.countsForEndResult(columnId) ? '' : 'font-style: italic'"
                                            @edit="showStudentScoreDialog(id, columnId)"></student-result>
                            <score-input v-if="isStudentScoreDialogShown(id, columnId)" :score="gradeBook.getResult(results, columnId)" @ok="handleUpdatedScoreValue(results, columnId, $event)" @cancel="hideStudentScoreDialog"></score-input>
                        </b-td>
                    </template>
                    <b-td class="col-sticky table-student-total"> {{ gradeBook.getEndResult(id)|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></b-td>
                </b-tr>-->
            </b-tbody>
        </b-table-simple>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Watch, Vue } from 'vue-property-decorator';
import GradeBook, {Category, ColumnId, ItemId, Results, ResultType} from '../domain/GradeBook';
import ItemTitleInput from './ItemTitleInput.vue';
import WeightInput from './WeightInput.vue';
import ScoreInput from './ScoreInput.vue';
import StudentResult from './StudentResult.vue';
import draggable from 'vuedraggable';

@Component({
    name: 'grades-table',
    components: { draggable, ItemTitleInput, WeightInput, ScoreInput, StudentResult },
    filters: {
        formatNum: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {maximumFractionDigits: 2});
        }
    }
})
export default class GradesTable extends Vue {
    private isDraggingColumn = false;
    private isDraggingCategory = false;
    private categoryDropArea: number|null = null;
    private editItemId: ItemId|null = null;
    private catEditItemId: number|null = null;
    private weightEditItemId: ItemId|null = null;
    private editStudentScoreId: number|null = null;
    private editScoreId: ItemId|null = null;

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;

    handleUpdatedScoreValue(results: Results, itemId: ItemId, value: ResultType) {
        const result = results.find(r => r.id === itemId);
        if (!result) { return; }
        result.value = value;
        result.overwritten = true;
        this.hideStudentScoreDialog();
    }

    getResult(columnId: ColumnId, userId: number): ResultType {
        if (!this.gradeBook.tscores[columnId]) { return null; }
        const score = this.gradeBook.tscores[columnId][userId];
        if (score.sourceScoreAbsent) { return 'afw'; }
        if (score.sourceScoreAuthAbsent) { return 'gafw'; }
        //console.log(columnId, userId, score.sourceScore);
        return score.sourceScore;
    }

    getEndResult(userId: number) {
        /*const r = this.resultsData.find(res => res.id === studentId);
        if (!r) { return 0; }
        const results = r.results;*/
        let endResult = 0;
        let maxWeight = 0;
        this.gradeBook.gradeColumns.filter(column => column.countForEndResult).forEach(column => {
            let result = this.getResult(column.id, userId);
            if (result === null) {
                result = 'afw';
            }
            const weight = this.gradeBook.getWeight(column.id);
            if (typeof result === 'number') {
                maxWeight += weight;
            } else if (result === 'gafw') {
                if (column.authPresenceEndResult !== GradeBook.NO_SCORE) {
                    maxWeight += weight;
                    if (column.authPresenceEndResult === GradeBook.MAX_SCORE) {
                        endResult += weight;
                    }
                }
            } else if (result === 'afw') {
                if (column.unauthPresenceEndResult !== GradeBook.NO_SCORE) {
                    maxWeight += weight;
                    if (column.unauthPresenceEndResult === GradeBook.MAX_SCORE) {
                        endResult += weight;
                    }
                }
            }
            if (typeof result === 'number') {
                endResult += (result * weight * 0.01);
            }
        });

        if (maxWeight === 0) {
            return 0;
        }

        return endResult / maxWeight * 100;
    }

    get showNullCategory() {
        return this.isDraggingColumn || this.gradeBook.nullCategory.columnIds.length > 0;
    }

    get displayedCategories(): Category[] {
        if (this.showNullCategory) {
            return [...this.gradeBook.categories, this.gradeBook.nullCategory];
        }
        return this.gradeBook.categories;
    }

    showStudentScoreDialog(id: number, itemId: ItemId) {
        this.editStudentScoreId = id;
        this.editScoreId = itemId;
    }

    hideStudentScoreDialog() {
        this.editStudentScoreId = null;
        this.editScoreId = null;
    }

    isStudentScoreDialogShown(id: number, itemId: ItemId) {
        return this.editStudentScoreId === id && this.editScoreId === itemId;
    }

    setCategoryTitle(id: number, title: string) {
        const category = this.gradeBook.getCategory(id);
        if (category) {
            category.title = title;
            this.$emit('change-category', category);
        }
        this.catEditItemId = null;
    }

    setTitle(columnId: ColumnId, title: string) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            this.gradeBook.setTitle(columnId, title);
            this.$emit('change-gradecolumn', gradeColumn);
        }
        this.editItemId = null;
    }

    setWeight(columnId: ColumnId, weight: number|null) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            this.gradeBook.setWeight(columnId, weight);
            this.$emit('change-gradecolumn', gradeColumn);
        }
        this.weightEditItemId = null;
    }

    startDragColumn(evt: DragEvent, id: ColumnId) {
        if (!evt.dataTransfer) { return; }
        evt.dataTransfer.setData('__COLUMN_ID', JSON.stringify({id}));
        this.isDraggingColumn = true;
    }

    startDragCategory(evt: DragEvent, id: number) {
        if (!evt.dataTransfer) { return; }
        evt.dataTransfer.setData('__CATEGORY_ID', JSON.stringify({id}));
        this.isDraggingCategory = true;
    }

    onDropAreaOverEnter(evt: DragEvent, index: number) {
        if (!evt.dataTransfer) { return; }
        this.categoryDropArea = index;
        evt.dataTransfer.dropEffect = 'move';
        evt.dataTransfer.effectAllowed = 'copyMove';
    }

    onDragEnd() {
        this.categoryDropArea = null;
        this.isDraggingColumn = false;
        this.isDraggingColumn = false;
    }

    onDrop(evt: DragEvent, categoryId: number) {
        if (!evt.dataTransfer) { return; }
        if (this.isDraggingColumn) {
            const id = JSON.parse(evt.dataTransfer.getData('__COLUMN_ID')).id;
            if (categoryId === -1) {
                window.setTimeout(() => {
                    this.$emit('move-gradecolumn', this.gradeBook.getGradeColumn(id)!);
                });
            } else {
                this.gradeBook.addItemToCategory(categoryId, id);
                this.$emit('change-gradecolumn-category', this.gradeBook.getGradeColumn(id)!, categoryId || null);
            }
        } else if (this.isDraggingCategory) {
            const id = JSON.parse(evt.dataTransfer.getData('__CATEGORY_ID')).id;
            window.setTimeout(() => {
                this.$emit('move-category', this.gradeBook.getCategory(id)!);
            }, 200);
        }
    }

    @Watch('showNullCategory')
    onShowNullCategoryChange(showNullCategory: boolean) {
        if (showNullCategory) {
            window.setTimeout(() => {
                document.querySelector('.table-wrap')?.scrollBy(21, 0);
            }, 100);
        }
    }
}
</script>

<style lang="scss" scoped>
    .table-wrap {
        position: relative;
        overflow-x: auto;
        overflow-y: auto;
    }

    th {
        font-weight: 700;
    }

    .gradebook-table {
        &.is-dragging * {
            cursor: grab;
        }

        &.is-dragging.is-category-drop * {
            cursor: copy;
        }

        &.is-dragging .table-body-row td {
            color: #ccc;
        }

        th, td {
            border: 1px solid #ebebeb;
            font-size: 1.35rem;
            vertical-align: top;
            z-index: 0;
        }

        th.mod-no-category-assigned {
            /*background-color: #f3f3f3;*/
            --color: #e3e8e4;
            background: transparent linear-gradient(135deg,var(--color) 10%,transparent 0,transparent 50%,var(--color) 0,var(--color) 60%,transparent 0,transparent) 0 0/5px 5px;
            color: #888;
            min-width: 21px;
            text-shadow: 1px 2px 0 white;
        }

        .table-head-row:first-child th {
            border-bottom-color: #eef1f3;
            border-top: 1px solid #ebebeb;

            &:not(:first-child):not(.is-droppable) {
                border-left: 1px double #eee;
            }

            &:not(:first-child).mod-no-category-assigned:not(.is-droppable) {
                border-left: 1px;
            }

            &:not(:last-child):not(.is-droppable) {
                border-right: 1px double transparent;
            }
        }

        .table-head-row:last-child div:last-of-type th:last-child {
            border-right: none;
        }

        .table-head-row:last-child th {
            border-top: none;
        }

        .table-row.table-head-row .is-droppable {
            border: 1px double #aaa;
        }

        .table-head-row > div > th {
            padding: 8px;
            line-height: 1.42857143;

            &.ghost {
                /*background: red;*/
                border: 1px double #ccc;
                border-radius: 3px;
                color: transparent;

                &:after, > * {
                    visibility: hidden;
                }
            }
        }

        &.is-dragging.is-category-drop .table-head-row > div > th.ghost {
            border: none;
        }

        .table-row {
            th, td {
                background-clip: padding-box;

                &:nth-last-child(2) {
                    border-right: none;
                }
            }
        }
    }

    .table-head-row:last-child th {
        background-color: #f8fbfb;
        color: #5885a2;
    }

    .table-body-row:nth-child(even) td {
        background-color: #fff;
    }

    .table-body-row:nth-child(odd) td {
        background-color: #f9f9f9;
    }

    .table-body-row:first-child td {
        background: linear-gradient(to bottom, #e3eaed 0, #f9f9f9 4px);
    }

    .table-row .col-sticky {
        background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%, linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%;
        position: sticky;
        z-index: 1;

        &.table-student {
            border-left-color: #fff;
            border-right-color: transparent;
            left: -1px;
        }

        &.table-student-total {
            border-left-color: transparent;
            border-right-color: #fff;
            right: 0;
        }
    }

    @-moz-document url-prefix() {
        .table-row {
            &.table-head-row, &.table-body-row {
                .col-sticky {
                    &.table-student {
                        left: 0;
                    }
                    &.table-student-total {
                        right: -1px;
                    }
                }
            }
        }
    }

    .table-head-row:first-child .col-sticky {
        background-color: #fff;
    }

    .table-body-row:nth-child(even) .col-sticky {
        background-color: #fff;
    }

    .table-body-row:nth-child(odd) .col-sticky {
        background-color: #f9f9f9;
    }

    .table-body-row:first-child .col-sticky {
        background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%, linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%,linear-gradient(to bottom, #e3eaed 0, #f9f9f9 4px);
    }

    .fa-percent {
        font-size: 1.1rem;
        margin-left: .15rem;
        opacity: .8;
    }

    .weight {
        cursor: pointer;
        margin-top: 2px;
        font-weight: 400;
        color: #477b7b;
        font-size: 1.2rem;
        padding: 3px 0 1px 0;
        width: fit-content;
    }

    .weight.mod-custom {
        background: rgb(211, 238, 224);
        border-radius: 3px;
        color: #466981;
        padding-left: 6px;
        padding-right: 8px;
    }

/*    .dropdown-scores .btn {
        width: 100%;
        text-align: right;
    }

    .dropdown-scores ul.dropdown-menu {
        width: 100%;
    }

    .dropdown-scores .dropdown-score-title {
        width: 100%;
        text-align: left;
        display: inline-block;
    }

    .dropdown-scores .btn:after {
        margin-left: -1em;
    }

    .score-breadcrumb-trail {
        font-size: 0.80rem;
        margin-top: 0.4em;
    }*/
</style>
