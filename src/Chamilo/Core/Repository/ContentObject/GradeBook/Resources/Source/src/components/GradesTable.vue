<i18n>
{
    "en": {
        "adjust-title": "Adjust title",
        "adjust-weight": "Adjust weight",
        "category-settings": "Category Settings",
        "count-towards-endresult-not": "Score does not count towards final result",
        "final-score": "Final score",
        "first-name": "First name",
        "grouped-score": "Grouped score",
        "invisible": "Final score is hidden",
        "item-settings": "Score Settings",
        "last-name": "NAME",
        "make-invisible": "Score is shown. Click to hide.",
        "make-visible": "Score is hidden. Click to show.",
        "saving": "Saving",
        "source-results-warning": "The results of this column refers to source data that no longer exists. You can keep on using this data but synchronizing will have no effect on this column. If you remove the column its results will be gone forever.",
        "total": "Total",
        "uncounted": "Not counted",
        "visible": "Final score is shown",
        "without-category": "Without category"
    },
    "nl": {
        "adjust-title": "Pas titel aan",
        "adjust-weight": "Pas gewicht aan",
        "category-settings": "Categorie-instellingen",
        "count-towards-endresult-not": "Score wordt niet meegeteld voor het eindresultaat",
        "final-score": "Eindcijfer",
        "first-name": "Voornaam",
        "grouped-score": "Gegroepeerde score",
        "invisible": "Eindscore is verborgen",
        "item-settings": "Score-instellingen",
        "last-name": "FAMILIENAAM",
        "make-invisible": "Score wordt weergegeven. Klik om te verbergen.",
        "make-visible": "Score is verborgen. Klik om te tonen.",
        "saving": "Aan het opslaan",
        "source-results-warning": "De resultaten in deze kolom verwijzen naar brondata die niet meer bestaat. Je kan de data verder blijven gebruiken maar synchroniseren zal op deze kolom geen effect hebben. Als je de kolom verwijdert zijn de resultaten ervan voorgoed weg.",
        "total": "Totaal",
        "uncounted": "Niet meegeteld",
        "visible": "Eindscore wordt weergegeven",
        "without-category": "Zonder categorie"
    }
}
</i18n>
<template>
    <div>
        <div class="table-wrap u-relative">
            <table id="gradebook-table" class="gradebook-table" :aria-busy="busy" :class="{'is-dragging': isDraggingColumn, 'is-category-drop': categoryDropArea !== null }">
                <thead>
                    <tr class="table-row table-head-row table-categories-row" v-if="gradeBook.categories.length">
                        <th class="col-sticky table-student"></th>
                        <draggable :list="gradeBook.categories" tag="div" class="u-contents" @end="onDragEnd" :disabled="catEditItemId !== null">
                                <th v-for="{id, title, color, columnIds} in gradeBook.categories" draggable :key="`category-${id}`" :colspan="Math.max(columnIds.length, 1)"
                                      class="category u-relative u-font-medium" :class="{'is-droppable': categoryDropArea === id}" :style="`--color: ${color};`"
                                      @dragstart="startDragCategory($event, id)" @dragover.prevent="onDropAreaOverEnter($event, id)" @dragenter.prevent="onDropAreaOverEnter($event, id)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, id)">
                                    <item-title-input v-if="catEditItemId === id" :item-title="title" @cancel="catEditItemId = null" @ok="setCategoryTitle(id, $event)" class="item-title-input"></item-title-input>
                                    <div v-else-if="id !== 0" class="u-flex u-align-items-center u-justify-content-between u-cursor-pointer" @dblclick="showCategoryTitleDialog(id)" :title="$t('adjust-title')">{{ title }}
                                        <div class="spin" v-if="isSavingCategoryWithId(id)" role="status" aria-busy="true" :aria-label="$t('saving')">
                                            <div aria-hidden="true" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                                        </div>
                                        <button class="btn-settings" :title="$t('category-settings')" @click="showCategorySettings(id)"><i class="fa fa-gear u-inline-block" aria-hidden="true"></i><span class="sr-only">{{ $t('category-settings') }}</span></button>
                                    </div>
                                </th>
                        </draggable>
                        <th v-if="showNullCategory" :colspan="Math.max(gradeBook.nullCategory.columnIds.length, 1)" class="mod-no-category-assigned" :class="{'is-droppable': categoryDropArea === 0}" :title="$t('without-category')"
                              @dragover.prevent="onDropAreaOverEnter($event, 0)" @dragenter.prevent="onDropAreaOverEnter($event, 0)" @dragleave="categoryDropArea = null" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, 0)"
                        ></th>
                        <th class="col-sticky table-student-total"></th>
                    </tr>
                    <tr class="table-row table-head-row table-scores-row">
                        <th class="col-sticky table-student">
                            <a class="tbl-sort-option" :aria-sort="getSortStatus('lastname')" @click="sortByNameField('lastname')">{{ $t('last-name') }}</a> <a class="tbl-sort-option" :aria-sort="getSortStatus('firstname')" @click="sortByNameField('firstname')">{{ $t('first-name') }}</a>
                        </th>
                        <draggable v-for="category in displayedCategories" :key="`category-score-${category.id}`" :list="category.columnIds" tag="div" class="u-contents" ghost-class="ghost" @end="onDragEnd" :disabled="editItemId !== null || weightEditItemId !== null">
                            <th v-if="category.columnIds.length === 0" :key="`item-id-${category.id}`"></th>
                            <th v-else v-for="column in getColumns(category)" :key="`item-id-${category.id}--${column.id}-name`" draggable @dragstart="startDragColumn($event, column.id)" :class="{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing}" @drop="(isDraggingColumn || isDraggingCategory) && onDrop($event, -1)">
                                <item-title-input v-if="column.isEditingTitle" :item-title="column.title" @cancel="editItemId = null" @ok="setTitle(column.id, $event)" class="item-title-input"></item-title-input>
                                <template v-else-if="column.isEditingWeight">
                                    <span class="column-title"><i v-if="column.isGrouped" class="fa fa-group" aria-hidden="true"></i><span class="sr-only">{{ $t('grouped-score') }}</span>{{ column.title }}</span>
                                    <weight-input :item-weight="column.weight" @cancel="weightEditItemId = null" @ok="setWeight(column.id, $event)" class="m-dialog"></weight-input>
                                </template>
                                <template v-else>
                                    <div class="u-flex u-align-items-center u-justify-content-between u-cursor-pointer" @dblclick="showColumnTitleDialog(column.id)" :title="$t('adjust-title')">
                                        <span class="column-title" :id="`${column.id}-title`"><i v-if="column.isGrouped" class="fa fa-group" aria-hidden="true"></i><span class="sr-only">{{ $t('grouped-score') }}</span>{{ column.title }}
                                            <i v-if="column.hasRemovedSourceData" class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                        <b-popover v-if="column.hasRemovedSourceData" :target="`${column.id}-title`" triggers="hover" placement="bottom">
                                            <p class="source-results-warning">{{ $t('source-results-warning') }}</p>
                                        </b-popover>
                                        <button class="btn-settings" @click="showColumnSettings(column.id)" :title="$t('item-settings')"><i class="fa fa-gear u-inline-block" aria-hidden="true"></i><span class="sr-only">{{$t('item-settings')}}</span></button>
                                    </div>
                                    <div class="u-flex u-align-items-center u-justify-content-between">
                                        <div v-if="column.countsForEndResult" class="weight u-font-normal u-cursor-pointer" :class="{'mod-custom': column.hasWeightSet , 'is-error': gradeBook.eqRestWeight < 0}" @dblclick="showColumnWeightDialog(column.id)" :title="$t('adjust-weight')">{{ column.weight|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                                        <div v-else class="weight u-font-normal u-font-italic" :title="$t('count-towards-endresult-not')"><span aria-hidden="true">{{ $t('uncounted') }}</span><span class="sr-only">{{ $t('count-towards-endresult-not') }}</span></div>
                                        <button class="btn-released u-ml-auto" v-if="!column.isSaving" @click="toggleVisibility(column.id)" :title="column.released ? $t('make-invisible') : $t('make-visible')"><i class="fa" :class="{'fa-eye': column.released, 'fa-eye-slash': !column.released}" aria-hidden="true"></i><span class="sr-only">{{ column.released ? $t('make-invisible') : $t('make-visible') }}</span></button>
                                        <div class="spin" role="status" :aria-busy="column.isSaving" :aria-label="$t('saving')">
                                            <div v-if="column.isSaving" aria-hidden="true" class="glyphicon glyphicon-repeat glyphicon-spin"></div>
                                        </div>
                                    </div>
                                </template>
                            </th>
                        </draggable>
                        <th class="col-sticky table-student-total" :class="{'unreleased-score-cell': gradeBook.hasUnreleasedScores, 'u-text-end': !editDisplayTotalDialog}">
                            <template v-if="editDisplayTotalDialog">
                                <div>{{ $t('final-score') }}</div>
                                <display-total-input :display-total="gradeBook.getDisplayTotal()" @cancel="editDisplayTotalDialog = false" @ok="setDisplayTotal($event)" class="m-dialog"></display-total-input>
                            </template>
                            <template v-else>
                                <div class="u-flex u-align-items-center u-justify-content-end">
                                    <div>{{ $t('final-score') }}</div>
                                    <button class="btn-settings" @click="showFinalScoreSettings" :title="$t('final-score-settings')"><i class="fa fa-gear u-inline-block" aria-hidden="true"></i><span class="sr-only">{{$t('final-score-settings')}}</span></button>
                                </div>
                                <div class="u-flex u-align-items-center u-justify-content-end u-gap-small-2x">
                                    <div class="weight u-font-normal u-cursor-pointer" style="width: 40px" @dblclick="showFinalScoreDialog">
                                        <template v-if="gradeBook.getDisplayTotal() === 100"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template>
                                        <template v-else>{{ gradeBook.getDisplayTotal() }} pt.</template>
                                    </div>
                                    <div class="final-score-released" :title="gradeBook.hasUnreleasedScores ? $t('invisible') : $t('visible')"><i class="fa" :class="{'fa-eye': !gradeBook.hasUnreleasedScores, 'fa-eye-slash': gradeBook.hasUnreleasedScores}" aria-hidden="true"></i><span class="sr-only">{{gradeBook.hasUnreleasedScores ? $t('invisible') : $t('visible')}}</span></div>
                                </div>
                            </template>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <student-result-row v-for="user in displayedUsers" :key="'user-' + user.id"
                        :grade-book="gradeBook" :user="user" :grade-book-root-url="gradeBookRootUrl" :exclude-column-id="addColumnId"
                        :show-null-category="showNullCategory" :edit-score-id="editScoreId" :edit-student-score-id="editStudentScoreId" :score-menu-tab="scoreMenuTab"
                        @edit-score="showStudentScoreDialog(user.id, $event)" @edit-canceled="hideStudentScoreDialog" @edit-comment="showStudentScoreDialog(user.id, $event, 'comment')"
                        @menu-tab-changed="scoreMenuTab = $event" @result-updated="overwriteResult(user.id, $event)" @result-reverted="revertOverwrittenResult(user.id, $event)" @comment-updated="updateResultComment(user.id, $event)" />
                </tbody>
            </table>
            <div class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
        </div>
        <div class="pagination-container u-flex u-justify-content-end">
            <b-pagination v-model="pagination.currentPage" :total-rows="sortedUsers.length" :per-page="itemsPerPage" aria-controls="gradebook-table"></b-pagination>
            <ul class="pagination">
                <li class="page-item active"><a class="page-link">{{ $t('total') }} {{ sortedUsers.length }}</a></li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
import GradeBook, {Category, ColumnId, ItemId, ResultType, User} from '../domain/GradeBook';
import StudentResultRow from './StudentResultRow.vue';
import ItemTitleInput from './ItemTitleInput.vue';
import WeightInput from './WeightInput.vue';
import DisplayTotalInput from './DisplayTotalInput.vue';
import ScoreInput from './ScoreInput.vue';
import StudentResult from './StudentResult.vue';
import draggable from 'vuedraggable';

interface Column {
    id: ColumnId;
    released: boolean;
    countsForEndResult: boolean;
    isGrouped: boolean;
    title: string;
    hasWeightSet: boolean;
    weight: number;
    hasRemovedSourceData: boolean;
    isEditingTitle: boolean;
    isEditingWeight: boolean;
    isEditing: boolean;
    isSaving: boolean;
}

@Component({
    name: 'grades-table',
    components: {StudentResultRow, ItemTitleInput, WeightInput, DisplayTotalInput, ScoreInput, StudentResult, draggable },
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
    private editDisplayTotalDialog = false;
    private scoreMenuTab = 'score';
    private sortBy = 'lastname';
    private sortDesc = false;

    private pagination = {
        currentPage: 1
    };

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: Array, default: () => []}) readonly searchTerms!: string[];
    @Prop({type: Boolean, default: false}) readonly busy!: boolean;
    @Prop({type: [String, Number], default: null}) readonly addColumnId!: ColumnId|null;
    @Prop({type: [String, Number], default: null}) readonly saveColumnId!: ColumnId|null;
    @Prop({type: Number, default: null}) readonly saveCategoryId!: number|null;
    @Prop({type: Number, default: 5}) readonly itemsPerPage!: number;
    @Prop({type: String, default: ''}) readonly gradeBookRootUrl!: string;

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

    resetDialogs() {
        this.editItemId = null;
        this.catEditItemId = null;
        this.weightEditItemId = null;
        this.editStudentScoreId = null;
        this.editScoreId = null;
        this.editDisplayTotalDialog = false;
    }

    showCategorySettings(categoryId: number) {
        this.resetDialogs();
        this.$emit('category-settings', categoryId);
    }

    showColumnSettings(columnId: ColumnId) {
        this.resetDialogs();
        this.$emit('item-settings', columnId);
    }

    showFinalScoreSettings() {
        this.resetDialogs();
        this.$emit('final-score-settings');
    }

    showCategoryTitleDialog(categoryId: number) {
        this.resetDialogs();
        this.catEditItemId = categoryId;
    }

    showColumnTitleDialog(columnId: ColumnId) {
        this.resetDialogs();
        this.editItemId = columnId;
    }

    showColumnWeightDialog(columnId: ColumnId) {
        this.resetDialogs();
        this.weightEditItemId = columnId;
    }

    showFinalScoreDialog() {
        this.resetDialogs();
        this.editDisplayTotalDialog = true;
    }

    showStudentScoreDialog(userId: number, itemId: ItemId, menuTab = 'score') {
        this.resetDialogs();
        this.scoreMenuTab = menuTab;
        this.editStudentScoreId = userId;
        this.editScoreId = itemId;
    }

    hideStudentScoreDialog() {
        this.editStudentScoreId = null;
        this.editScoreId = null;
    }

    getColumnData(columnId: ColumnId): Column {
        const gradeBook = this.gradeBook;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) { throw new Error(`GradeColumn with id ${columnId} not found.`); }

        return {
            id: columnId,
            released: column.released,
            countsForEndResult: column.countForEndResult,
            isGrouped: column.type === 'group',
            title: gradeBook.getTitle(column),
            hasWeightSet: column.weight !== null,
            weight: gradeBook.getWeight(column),
            hasRemovedSourceData: gradeBook.hasRemovedSourceData(column),
            isEditingTitle: this.editItemId === columnId,
            isEditingWeight: this.weightEditItemId === columnId,
            isEditing: this.editItemId === columnId || this.weightEditItemId === columnId,
            isSaving: this.isSavingColumnWithId(columnId)
        };
    }

    getColumns(category: Category): Column[] {
        return category.columnIds.map(columnId => this.getColumnData(columnId));
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

    setDisplayTotal(displayTotal: number|null) {
        this.gradeBook.displayTotal = displayTotal;
        this.$emit('change-display-total');
        this.editDisplayTotalDialog = false;
    }

    toggleVisibility(columnId: ColumnId) {
        const gradeColumn = this.gradeBook.getGradeColumn(columnId);
        if (gradeColumn) {
            gradeColumn.released = !gradeColumn.released;
            this.$emit('change-gradecolumn', gradeColumn);
        }
    }

    overwriteResult(userId: number, {columnId, value}: {columnId: ColumnId, value: ResultType}) {
        const score = this.gradeBook.overwriteResult(columnId, userId, value);
        if (!score) { return; }
        this.$emit('overwrite-result', score);
        this.hideStudentScoreDialog();
    }

    revertOverwrittenResult(userId: number, columnId: ColumnId) {
        const score = this.gradeBook.revertOverwrittenResult(columnId, userId);
        if (!score) { return; }
        this.$emit('revert-overwritten-result', score);
        this.hideStudentScoreDialog();
    }

    updateResultComment(userId: number, {columnId, comment}: {columnId: ColumnId, comment: string|null}) {
        const score = this.gradeBook.updateResultComment(columnId, userId, comment);
        if (!score) { return; }
        this.$emit('update-score-comment', score);
        this.hideStudentScoreDialog();
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
        /*max-height: 540px;*/
        overflow-x: auto;
    }

    .gradebook-table {
        margin-bottom: 0;
        max-width: 100%;
        width: 100%;

        &.is-dragging {
            * {
                cursor: grab;
            }

            .table-scores-row:last-child th {
                border-bottom: 1px solid #ebebeb;
            }

            &.is-category-drop {
                * {
                    cursor: copy;
                }

                .table-head-row > div > th.ghost {
                    border: none;
                }
            }

            &::v-deep .table-body-row td {
                border-color: transparent;
                opacity: 0.15;
            }
        }

        &:not(.is-dragging) .table-categories-row th.mod-no-category-assigned {
            &, & + th {
                background: #fff;
            }
        }

        &:not(.is-dragging) .table-categories-row .u-contents + th.table-student-total {
            background: #fff;
        }

        th, &::v-deep td {
            border: 1px solid #ebebeb;
            font-size: 1.35rem;
            padding: 8px;
            vertical-align: top;
            z-index: 0;

            &:nth-last-child(2) {
                border-right: none;
            }
        }

        .table-row {
            th, &::v-deep td {
                background-clip: padding-box;

                &:nth-last-child(2) {
                    border-right: none;
                }
            }
        }

        th.mod-no-category-assigned {
            min-width: 21px;
        }

        .table-categories-row th {
            border-top: none;
        }

        .table-categories-row th {
            padding-bottom: 14px;

            &:not(:first-child):not(.table-student-total):not(.is-droppable) {
                border-left: 1px double #eee;
            }

            &:not(:first-child).mod-no-category-assigned:not(.is-droppable) {
                border-left: 1px;
            }

            &:not(.col-sticky) {
                background: linear-gradient(to bottom, transparent 0, transparent 1px, #f8fbfb 1px) 0 0 repeat, linear-gradient(to bottom, #ebebeb 0px, #ebebeb 1px, white 1px) 0 0 repeat-x;
                background-clip: padding-box;
            }
        }

        .table-scores-row div:last-of-type th:last-child {
            border-right: none;
        }

        .table-scores-row th {
            border-bottom: none;
        }

        .table-categories-row + .table-scores-row th {
            border-top: none;
        }

        .table-row.table-head-row.table-categories-row .is-droppable {
            border: 1px double #aaa;
        }

        .table-head-row.table-scores-row > div > th {
            padding: 8px;
            line-height: 1.42857143;

            &.ghost {
                border: 1px double #ccc;
                border-radius: 3px;
                color: transparent;

                &:after, > * {
                    visibility: hidden;
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

    .table-scores-row th {
        background-color: #f8fbfb;
        color: #5885a2;

        &.uncounted-score-cell {
            background-color: #f0f4fa;
        }

        &.unreleased-score-cell {
            background-color: #f3f3f3;
        }

        &.unreleased-score-cell.uncounted-score-cell {
            background-color: #eff2f6;
        }
    }

    .table-row::v-deep .col-sticky {
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

    .table-scores-row .col-sticky {
        &.table-student, &.table-student-total {
            background-color: #f8fbfb;
        }

        &.table-student-total.unreleased-score-cell {
            background-color: #f3f3f3;
        }
    }

    .table-categories-row .col-sticky {
        &.table-student {
            background: #fff linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%;
            background-clip: padding-box;
        }

        &.table-student-total {
            background: #fff linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%;
            background-clip: padding-box;
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

    .category {
        background-color: #f8fbfb;
        color: #5885a2;

        &::before {
            background-color: var(--color);
            bottom: 0;
            content: '';
            height: 7px;
            left: 0;
            position: absolute;
            right: 0;
        }
    }

    .column-title {
        white-space: nowrap;

        .fa-exclamation-circle {
            color: #e24a03;
            margin-left: 5px;
        }
    }

    .fa-group {
        margin-right: .5rem;
    }

    .btn-settings {
        background: none;
        border: none;
        margin-left: 15px;
        padding: 0;
    }

    .btn-released {
        background: none;
        border: none;
        padding: 0;
    }

    .table-categories-row .table-student + div > .category:first-child::before {
        left: -1px;
    }

    .fa-percent {
        font-size: 1.1rem;
        margin-left: .15rem;
        opacity: .8;
    }

    .weight {
        color: #477b7b;
        font-size: 1.2rem;
        margin-top: 2px;
        padding: 3px 0 1px 0;
        width: fit-content;

        &.mod-custom {
            background-color: rgb(211, 238, 224);
            border-radius: 3px;
            color: #466981;
            padding-left: 6px;
            padding-right: 8px;

            &.is-error {
                background-color: #feecea;
                border: 1px solid #ff8080;
            }
        }

        &:not(.mod-custom).is-error {
            color: #ff8080;
        }
    }

    .item-title-input {
        margin: -6px -8px;
    }

    .m-dialog {
        margin: -5px -8px -6px;
    }

    .source-results-warning {
        font-size: 11.5px;
        margin: 6px;
    }

    .final-score-released {
        color: #657681;
        margin-top: 2px;
    }

    .pagination-container {
        > * {
            z-index: 1;
        }
    }

    .spin {
        font-size: 12px;
    }

    .category .spin {
        margin-inline: auto -10px;
    }
</style>

<style lang="scss" scoped>
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
