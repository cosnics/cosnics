<template>
    <div>
        <div class="table-wrap">
            <b-table-simple class="gradebook-table" :aria-busy="busy" :class="{'is-dragging': isDraggingColumn, 'is-category-drop': categoryDropArea !== null }">
                <b-thead>
                    <b-tr class="table-row table-head-row" :style="gradeBook.categories.length ? '' : 'visibility: collapse'">
                        <b-th class="col-sticky table-student" style="background-color: #fff;background-clip: padding-box"></b-th>
                        <draggable :list="gradeBook.categories" tag="div" style="display: contents" @end="onDragEnd" :disabled="catEditItemId !== null">
                            <template v-for="{id, title, color, columnIds} in gradeBook.categories">
                                <b-th draggable :key="`category-${id}`" :colspan="Math.max(columnIds.length, 1)"
                                      :class="{'is-droppable': categoryDropArea === id}" class="category" style="padding-bottom:14px;" :style="`--color: ${color};` + (catEditItemId === id ? 'z-index: 3;' : '')"
                                      @dragstart="startDragCategory($event, id)" @dragover.prevent="onDropAreaOverEnter($event, id)" @dragenter.prevent="onDropAreaOverEnter($event, id)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, id)">
                                    <div v-if="id !== 0" style="display: flex; cursor: pointer; justify-content: space-between;align-items: center" @dblclick="catEditItemId = id">{{ title }}
                                        <div class="spin" v-if="isSavingCategoryWithId(id)" style="margin-left: auto; margin-right: -10px;">
                                            <div class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                                        </div>
                                        <button style="padding:0; background: none; border: none;margin-left: 15px" @click="$emit('category-settings', id)"><i class="fa fa-gear" style="margin-left: auto;display:inline-block"></i></button>
                                    </div>
                                    <item-title-input v-if="catEditItemId === id" :item-title="title" @cancel="catEditItemId = null" @ok="setCategoryTitle(id, $event)"></item-title-input>
                                </b-th>
                            </template>
                        </draggable>
                        <b-th v-if="showNullCategory" :colspan="Math.max(gradeBook.nullCategory.columnIds.length, 1)" class="mod-no-category-assigned" :class="{'is-droppable': categoryDropArea === 0}" title="Zonder categorie"
                              @dragover.prevent="onDropAreaOverEnter($event, 0)" @dragenter.prevent="onDropAreaOverEnter($event, 0)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, 0)"
                        ></b-th>
                        <b-th class="col-sticky table-student-total" style="background-color: #fff;background-clip: padding-box"></b-th>
                    </b-tr>
                    <b-tr class="table-row table-head-row" style="position: sticky; top: 0;z-index: 2;">
                        <b-th class="col-sticky table-student">
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">FAMILIENAAM</a> <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">Voornaam</a>
                        </b-th>
                        <draggable v-for="({id, columnIds}) in displayedCategories" :key="`category-score-${id}`" :list="columnIds" tag="div" style="display: contents" ghost-class="ghost" @end="onDragEnd" :disabled="editItemId !== null || weightEditItemId !== null">
                            <b-th v-if="columnIds.length === 0" :key="`item-id-${id}`"></b-th>
                            <b-th v-else v-for="(columnId) in columnIds" :key="`${columnId}-name`" draggable @dragstart="startDragColumn($event, columnId)" :style="(editItemId === columnId || weightEditItemId === columnId) ? 'position: relative; z-index: 2' : ''" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, -1)">
                                <div style="cursor: pointer;display:flex;justify-content:space-between;align-items:center" @dblclick="editItemId = columnId"><span style="white-space: nowrap"><i v-if="gradeBook.isGrouped(columnId)" class="fa fa-group" style="margin-right: .5rem"></i>{{ gradeBook.getTitle(columnId) }}</span>
                                    <button style="padding:0; background: none; border: none;margin-left: 15px" @click="$emit('item-settings', columnId)"><i class="fa fa-gear" style="margin-left: auto;display:inline-block"></i></button>
                                </div>
                                <div style="display: flex; align-items: center;" :style="gradeBook.countsForEndResult(columnId) ? 'justify-content: space-between' : 'justify-content: flex-end'">
                                    <div class="weight" :class="{'mod-custom': gradeBook.getGradeColumn(columnId).weight !== null}" @dblclick="weightEditItemId = columnId" v-if="gradeBook.countsForEndResult(columnId)" title="Pas gewicht aan">{{ gradeBook.getWeight(columnId)|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                                    <div class="spin">
                                        <div v-if="isSavingColumnWithId(columnId)" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                                    </div>
                                </div>
                                <item-title-input v-if="editItemId === columnId" :item-title="gradeBook.getTitle(columnId)" @cancel="editItemId = null" @ok="setTitle(columnId, $event)"></item-title-input>
                                <weight-input v-if="weightEditItemId === columnId" :item-weight="gradeBook.getWeight(columnId)" @cancel="weightEditItemId = null" @ok="setWeight(columnId, $event)"></weight-input>
                            </b-th>
                        </draggable>
                        <b-th class="col-sticky table-student-total">Eindcijfer</b-th>
                    </b-tr>
                </b-thead>
                <b-tbody>
                    <b-tr v-for="user in displayedUsers" :key="'user-' + user.id" class="table-row table-body-row">
                        <b-td class="col-sticky table-student">{{ user.lastName.toUpperCase() }}, {{ user.firstName }}</b-td>
                        <template v-for="category in displayedCategories">
                            <b-td v-if="category.columnIds.length === 0" :key="`category-results-${category.id}`"></b-td>
                            <b-td v-else v-for="columnId in category.columnIds" :key="`${category.id}-${columnId}-result`" :style="editStudentScoreId === user.id && editScoreId === columnId ? 'position: relative; z-index: 2' : ''" :class="{'no-value': gradeBook.getResult(columnId, user.id) === null}">
                                <student-result :id="`result-${columnId}-${user.id}`" :result="gradeBook.getResult(columnId, user.id)" :is-overwritten="gradeBook.isOverwrittenResult(columnId, user.id)" :comment="gradeBook.getResultComment(columnId, user.id)"
                                                style="cursor: pointer;display: flex;align-items: center;justify-content: flex-end" :style="gradeBook.countsForEndResult(columnId) ? '' : 'font-style: italic; color: #777'"
                                                @edit="showStudentScoreDialog(user.id, columnId)" @edit-comment="showStudentScoreDialog(user.id, columnId, 'comment')" @revert="revertOverwrittenResult(columnId, user.id)"></student-result>
                                <score-input v-if="isStudentScoreDialogShown(user.id, columnId)" :menu-tab="scoreMenuTab" @menu-tab-changed="scoreMenuTab = $event" :score="gradeBook.getResult(columnId, user.id)" :comment="gradeBook.getResultComment(columnId, user.id)" @comment-updated="updateResultComment(columnId, user.id, $event)" @ok="overwriteResult(columnId, user.id, $event)" @cancel="hideStudentScoreDialog"></score-input>
                            </b-td>
                        </template>
                        <b-td v-if="totalsNeedUpdate(user.id)" class="col-sticky table-student-total mod-needs-update">
                            <i class="fa fa-exclamation-circle" title="Eindtotaal nog niet geüpdated" aria-hidden="true" style="margin-right: 5px;color: hsl(204deg 28% 56%);"></i><span class="sr-only">Eindtotaal nog niet geüpdated</span>{{ gradeBook.getEndResult(user.id)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span>
                        </b-td>
                        <b-td v-else class="col-sticky table-student-total">{{ gradeBook.getEndResult(user.id)|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></b-td>
                    </b-tr>
                </b-tbody>
            </b-table-simple>
            <div class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
        </div>
        <div class="pagination-container u-flex" style="justify-content: flex-end">
            <b-pagination v-model="pagination.currentPage" :total-rows="sortedUsers.length" :per-page="itemsPerPage"
                          aria-controls="data-table"></b-pagination>
            <ul class="pagination">
                <li class="page-item active"><a class="page-link">Totaal {{ sortedUsers.length }}</a></li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { Component, Prop, Watch, Vue } from 'vue-property-decorator';
import GradeBook, {Category, ColumnId, ItemId, ResultType, User} from '../domain/GradeBook';
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
        },
        formatNum2: function (v: number|null) {
            if (v === null) { return ''; }
            return v.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
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
    private scoreMenuTab = 'score';
    private sortBy = 'lastname';
    private sortDesc = false;

    private pagination = {
        currentPage: 1
    };

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: Array, default: () => []}) readonly searchTerms!: string[];
    @Prop({type: Boolean, default: false}) readonly busy!: boolean;
    @Prop({type: [String, Number], default: null}) readonly saveColumnId!: ColumnId|null;
    @Prop({type: Number, default: null}) readonly saveCategoryId!: number|null;
    @Prop({type: Number, default: 5}) readonly itemsPerPage!: number;

    get showNullCategory() {
        return this.isDraggingColumn || this.gradeBook.nullCategory.columnIds.length > 0;
    }

    get displayedCategories(): Category[] {
        if (this.showNullCategory) {
            return [...this.gradeBook.categories, this.gradeBook.nullCategory];
        }
        return this.gradeBook.categories;
    }

    get displayedUsers() {
        const {currentPage} = this.pagination;
        const perPage = this.itemsPerPage;

        return this.sortedUsers.slice((currentPage - 1) * perPage, currentPage * perPage);
    }

    get filteredUsers() {
        if (!this.searchTerms) { return this.gradeBook.users; }
        return this.gradeBook.users.filter(user => {
            const fullName = user.firstName.toLowerCase() + ' ' + user.lastName.toLowerCase();
            return this.searchTerms.every(term => fullName.indexOf(term) !== -1);
        });
    }

    get sortedUsers() {
        let field: 'lastName'|'firstName';
        if (this.sortBy === 'lastname') {
            field = 'lastName';
        } else if (this.sortBy === 'firstname') {
            field = 'firstName';
        } else {
            return this.filteredUsers;
        }
        const users = [...this.filteredUsers];
        const mul = this.sortDesc ? -1 : 1;
        users.sort((u1: User, u2: User) => {
            if (u1[field] > u2[field]) {
                return 1 * mul;
            }
            if (u1[field] < u2[field]) {
                return -1 * mul;
            }
            return 0;
        });
        return users;
    }

    getSortStatus(name: string) {
        if (this.sortBy !== name) { return 'none'; }
        return this.sortDesc ? 'descending' : 'ascending';
    }

    sortByNameField(namefield: string) {
        if (this.sortBy === namefield) {
            this.sortDesc = !this.sortDesc;
            return;
        }
        this.sortBy = namefield;
        this.sortDesc = false;
    }

    showStudentScoreDialog(id: number, itemId: ItemId, menuTab = 'score') {
        this.scoreMenuTab = menuTab;
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

    overwriteResult(columnId: ColumnId, userId: number, value: ResultType) {
        const score = this.gradeBook.overwriteResult(columnId, userId, value);
        if (!score) { return; }
        this.$emit('overwrite-result', score);
        this.hideStudentScoreDialog();
    }

    revertOverwrittenResult(columnId: ColumnId, userId: number) {
        const score = this.gradeBook.revertOverwrittenResult(columnId, userId);
        if (!score) { return; }
        this.$emit('revert-overwritten-result', score);
    }

    updateResultComment(columnId: ColumnId, userId: number, comment: string|null) {
        const score = this.gradeBook.updateResultComment(columnId, userId, comment);
        if (!score) { return; }
        this.$emit('update-score-comment', score);
        this.hideStudentScoreDialog();
    }

    totalsNeedUpdate(userId: number) {
        const total = this.gradeBook.getResult('totals', userId);
        if (typeof total !== 'number') { return true; }
        return total.toFixed(2) !== this.gradeBook.getEndResult(userId).toFixed(2);
    }

    isSavingColumnWithId(columnId: ColumnId) {
        return this.saveColumnId === columnId;
    }

    isSavingCategoryWithId(categoryId: number) {
        return this.saveCategoryId === categoryId;
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
        max-height: 500px;
        /*overflow-y: auto;*/
        /*max-height: 60vh;*/
    }

    th {
        font-weight: 700;
    }

    .gradebook-table {
        margin-bottom: 0;

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
            min-width: 21px;
            /*--color: #e3e8e4;
            background: transparent linear-gradient(135deg,var(--color) 10%,transparent 0,transparent 50%,var(--color) 0,var(--color) 60%,transparent 0,transparent) 0 0/5px 5px;
            color: #888;
            min-width: 21px;
            text-shadow: 1px 2px 0 white;*/
        }

        .table-head-row:first-child th {
            /*border-bottom-color: #eef1f3;*/

            &:not(:first-child):not(.is-droppable) {
                border-left: 1px double #eee;
            }

            &:not(:first-child).mod-no-category-assigned:not(.is-droppable) {
                border-left: 1px;
            }

            &:not(:last-child):not(.is-droppable) {
                /*border-right: 1px double transparent;*/
            }

            &:not(.col-sticky) {
                border-top: 1px solid white;
                background:  linear-gradient(to bottom, transparent 0, transparent 1px, #f8fbfb 1px) 0 0 repeat, linear-gradient(to bottom, #ebebeb 0px, #ebebeb 1px, white 1px) 0 0 repeat-x;
                background-clip: padding-box;
            }
        }

        .table-head-row:first-child div th:nth-last-child(1):not(.is-droppable) {
            /*border-right: 1px double transparent;*/
        }

        .table-head-row:last-child div:last-of-type th:last-child {
            border-right: none;
        }

        .table-head-row:last-child th {
            border-bottom: none;
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

        + .lds-ellipsis {
            display: none;
        }

        &[aria-busy=true] {
            pointer-events: none;
        }

        &[aria-busy=true] + .lds-ellipsis {
            display: inline-block;
            left: calc(50% - 20px);
            position: absolute;
            top: 40px;
            z-index: 2;
        }
    }

    .table-head-row:last-child th {
        background-color: #f8fbfb;
        color: #5885a2;
    }

    .table-body-row td.no-value {
        --color: #ebebeb;
        background: transparent linear-gradient(135deg, var(--color) 10%, transparent 0, transparent 50%, var(--color) 0, var(--color) 60%, transparent 0, transparent) 0 0/6px 6px;
    }

    .table-body-row:first-child td.no-value {
        --color: #ebebeb;
        background: linear-gradient(135deg, var(--color) 10%, transparent 0, transparent 50%, var(--color) 0, var(--color) 60%, transparent 0, transparent) 0 0/6px 6px, linear-gradient(to bottom, #e3eaed 0, #fff 4px);
    }

    .table-body-row td {
        background-color: #fff;
    }

    .table-body-row:first-child td {
        background: linear-gradient(to bottom, #e3eaed 0, #fff 4px);
        border-top: none;
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
            text-align: right;
        }
    }

    .table-head-row:first-child .col-sticky {
        background-color: #fff;

        &:first-child {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%;
        }

        &:last-child {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%;
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

    .table-body-row .table-student, .table-body-row .table-student-total {
        background-color: #fff;
    }

    .table-body-row:nth-child(even) .col-sticky {
        /*background-color: #fff;*/

        &.table-student-total.mod-needs-update {
            /*background-color: #fdfce6;*/
        }
    }

    .table-body-row:nth-child(odd) .col-sticky {
        /*background-color: #f9f9f9;*/

        &.table-student-total.mod-needs-update {
            /*background-color: #fdfce6;*/
        }
    }

    .table-body-row:first-child .col-sticky {
        background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%, linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%,linear-gradient(to bottom, #e3eaed 0, #fff 4px);

        &.table-student-total.mod-needs-update {
            /*background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%,
            linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%,
            linear-gradient(to bottom, #e3eaed 0, #fdfce6 4px);*/
        }
    }

    .table-student-total {
        white-space: nowrap;
    }

    .table-student-total.mod-needs-update {
        color: #758895;
        font-style: italic;
    }

    .category {
        background-color: #f8fbfb;
        color:#606060;
        color: #5885a2;
        font-weight: 500;
        position: relative;
    }

    .category::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 7px;
        background: var(--color);
    }

    .table-head-row:first-child .table-student + div > .category:first-child::before {
        left: -1px;
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

    .pagination-container {
        margin-top: 0;

        > * {
            z-index: 1;
        }
    }

    .spin {
        font-size: 12px;
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
<style>
.tbl-sort-option {
    background-position: right calc(.75rem / 2) center;
    background-repeat: no-repeat;
    background-size: .65em 1em;
    cursor: pointer;
    padding-right: calc(.75rem + .85em);
    pointer-events: all;
}

.tbl-sort-option[aria-sort=none] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' opacity='.3' d='M51 1l25 23 24 22H1l25-22zM51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}

.tbl-sort-option[aria-sort=ascending] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' d='M51 1l25 23 24 22H1l25-22z'/%3e%3cpath fill='black' opacity='.3' d='M51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}

.tbl-sort-option[aria-sort=descending] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='101' height='101' view-box='0 0 101 101' preserveAspectRatio='none'%3e%3cpath fill='black' opacity='.3' d='M51 1l25 23 24 22H1l25-22z'/%3e%3cpath fill='black' d='M51 101l25-23 24-22H1l25 22z'/%3e%3c/svg%3e");
}
</style>
