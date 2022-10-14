<template>
    <div>
        <div class="gradebook-toolbar">
            <div class="input-group">
                <input class="form-control" type="text" v-model="searchTerm" placeholder="Zoek student">
                <div class="input-group-btn"><button name="clear" value="clear" class="btn btn-default" @click="searchTerm = ''"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></button></div>
            </div>
            <grades-dropdown id="dropdown-main" :graded-items="gradeBook.gradedItemsWithCheckedStatus" @toggle="toggleGradeItem"></grades-dropdown>
            <div class="gradebook-create-actions">
                <button class="btn btn-default btn-sm" @click="synchronizeGradeBook"><i aria-hidden="true" class="fa fa-refresh"></i>Synchronizeer scores</button>
                <button class="btn btn-default btn-sm" @click="updateTotalScores"><i aria-hidden="true" class="fa fa-refresh"></i>Update eindcijfers</button>
                <button class="btn btn-default btn-sm" @click="createNewScore"><i aria-hidden="true" class="fa fa-plus"></i>Nieuwe score</button>
                <button class="btn btn-default btn-sm" @click="createNewCategory"><i aria-hidden="true" class="fa fa-plus"></i>Categorie</button>
            </div>
        </div>
        <div class="gradebook-table-container">
            <grades-table :grade-book="gradeBook" :search-terms="studentSearchTerms" :busy="tableBusy" :save-column-id="saveColumnId" :save-category-id="saveCategoryId"
                          @item-settings="itemSettings = $event" @category-settings="categorySettings = $event"
                          @update-score-comment="onUpdateScoreComment" @overwrite-result="onOverwriteResult" @revert-overwritten-result="onRevertOverwrittenResult"
                          @change-category="onChangeCategory" @move-category="onMoveCategory"
                          @change-gradecolumn="onChangeGradeColumn" @change-gradecolumn-category="onChangeGradeColumnCategory" @move-gradecolumn="onMoveGradeColumn"></grades-table>
        </div>
        <item-settings v-if="itemSettings !== null" :grade-book="gradeBook" :column-id="itemSettings" @close="itemSettings = null"
                       @item-settings="itemSettings = $event" @change-gradecolumn="onChangeGradeColumn" @add-subitem="onAddSubItem" @remove-subitem="onRemoveSubItem" @remove-column="onRemoveColumn"></item-settings>
        <category-settings v-if="selectedCategory" :grade-book="gradeBook" :category="selectedCategory" @close="closeSelectedCategory" @change-category="onChangeCategory" @remove-category="onRemoveCategory"></category-settings>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import GradesDropdown from './GradesDropdown.vue';
    import GradesTable from './GradesTable.vue';
    import GradeBook, {Category, GradeColumn, GradeItem, ColumnId, GradeScore} from '../domain/GradeBook';
    import ItemSettings from './ItemSettings.vue';
    import CategorySettings from './CategorySettings.vue';
    import Connector from '../connector/Connector';

    @Component({
        components: { GradesTable, GradesDropdown, ItemSettings, CategorySettings }
    })
    export default class Main extends Vue {
        private itemSettings: number|null = null;
        private categorySettings: number|null = null;
        private studentSearchTerm = '';
        private studentSearchTerms: string[] = [];
        private tableBusy = false;
        private saveColumnId: ColumnId|null = null;
        private saveCategoryId: number|null = null;

        @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
        @Prop(Connector) readonly connector!: Connector|null;

        constructor() {
            super();
            this.updateResult = this.updateResult.bind(this);
        }

        get searchTerm() {
            return this.studentSearchTerm;
        }

        set searchTerm(term: string) {
            this.studentSearchTerm = term;
            this.studentSearchTerms = term.toLowerCase().split(' ').filter(s => s.length);
        }

        updateGradeColumnWithScores(column: GradeColumn, id: ColumnId, scores: GradeScore[]) {
            this.gradeBook.updateGradeColumnId(column, id);
            const resultsData = this.gradeBook.resultsData;
            scores.forEach(score => {
                if (!resultsData[score.columnId]) {
                    Vue.set(resultsData, score.columnId, {});
                }
                resultsData[score.columnId][score.targetUserId] = score;
            });
        }

        addGradeItem(item: GradeItem) {
            const column = this.gradeBook.addGradeColumnFromItem(item);
            this.tableBusy = true;
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
                this.tableBusy = false;
            });
        }

        removeGradeItem(item: GradeItem) {
            const column = this.gradeBook.findGradeColumnWithGradeItem(item.id);
            if (!column) { return; }
            if (column.type === 'item') {
                this.gradeBook.removeColumn(column);
                this.onRemoveColumn(column);
            } else {
                this.gradeBook.removeSubItem(item);
                this.onRemoveSubItem(item, column.id);
            }
        }

        toggleGradeItem(item: GradeItem, isAdding: boolean) {
            if (isAdding) {
                this.addGradeItem(item);
            } else {
                this.removeGradeItem(item);
            }
        }

        get selectedCategory() {
            return this.gradeBook.categories.find(cat => cat.id === this.categorySettings) || null;
        }

        createNewCategory() {
            const category = this.gradeBook.createNewCategory();
            this.tableBusy = true;
            this.connector?.addCategory(category, (cat: any) => {
                category.id = cat.id;
                this.categorySettings = cat.id;
                this.tableBusy = false;
            });
        }

        async synchronizeGradeBook() {
            this.tableBusy = true;
            await this.connector?.synchronizeGradeBook((scores: GradeScore[]) => {
                const resultsData = this.gradeBook.resultsData;
                scores.forEach(score => {
                    if (!resultsData[score.columnId]) {
                        Vue.set(resultsData, score.columnId, {});
                    }
                    resultsData[score.columnId][score.targetUserId] = score;
                });
                this.tableBusy = false;
            });
        }

        async updateTotalScores() {
            this.tableBusy = true;
            await this.connector?.calculateTotalScores((scores: GradeScore[]) => {
                const resultsData = this.gradeBook.resultsData;
                if (!resultsData['totals']) {
                    Vue.set(resultsData, 'totals', {});
                }
                scores.forEach(score => {
                    resultsData['totals'][score.targetUserId] = score;
                });
                this.tableBusy = false;
            });
        }

        createNewScore() {
            const column = this.gradeBook.createNewScore();
            this.tableBusy = true;
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
                this.tableBusy = false;
            });
        }

        closeSelectedCategory() {
            this.categorySettings = null;
        }

        onChangeCategory(category: Category) {
            this.saveCategoryId = category.id;
            this.connector?.updateCategory(category, () => {
                this.saveCategoryId = null;
            });
        }

        async onMoveCategory(category: Category) {
            this.tableBusy = true;
            await this.connector?.moveCategory(category, this.gradeBook.categories.indexOf(category), () => {
                this.tableBusy = false;
            });
        }

        onRemoveCategory(category: Category) {
            this.tableBusy = true;
            this.connector?.removeCategory(category, () => {
                this.tableBusy = false;
            });
        }

        onChangeGradeColumn(gradeColumn: GradeColumn) {
            this.saveColumnId = gradeColumn.id;
            this.connector?.updateGradeColumn(gradeColumn, () => {
                this.saveColumnId = null;
            });
        }

        onChangeGradeColumnCategory(gradeColumn: GradeColumn, categoryId: number|null) {
            this.tableBusy = true;
            this.connector?.updateGradeColumnCategory(gradeColumn, categoryId, () => {
                this.tableBusy = false;
            });
        }

        onMoveGradeColumn(column: GradeColumn) {
            const category = this.gradeBook.allCategories.find(category => category.columnIds.indexOf(column.id) !== -1);
            if (category) {
                this.tableBusy = true;
                this.connector?.moveGradeColumn(column, category.columnIds.indexOf(column.id), () => {
                    this.tableBusy = false;
                });
            }
        }

        onAddSubItem(item: GradeItem, columnId: ColumnId) {
            this.tableBusy = true;
            this.connector?.addColumnSubItem(columnId, item.id, (column: GradeColumn, scores: GradeScore[]) => {
                console.log('scores', scores);
                const resultsData = this.gradeBook.resultsData;
                delete resultsData[columnId];
                scores.forEach(score => {
                    if (!resultsData[columnId]) {
                        Vue.set(resultsData, columnId, {});
                    }
                    resultsData[columnId][score.targetUserId] = score;
                });
                this.tableBusy = false;
            });
        }

        onRemoveSubItem(item: GradeItem, columnId: ColumnId) {
            this.tableBusy = true;
            this.connector?.removeColumnSubItem(columnId, item.id, (column: GradeColumn, scores: GradeScore[]) => {
                console.log('scores', scores);
                const resultsData = this.gradeBook.resultsData;
                delete resultsData[columnId];
                scores.forEach(score => {
                    if (!resultsData[columnId]) {
                        Vue.set(resultsData, columnId, {});
                    }
                    resultsData[columnId][score.targetUserId] = score;
                });
                this.tableBusy = false;
            });
        }

        onRemoveColumn(column: GradeColumn) {
            this.tableBusy = true;
            this.connector?.removeGradeColumn(column, () => {
                this.tableBusy = false;
            });
        }

        updateResult(result: GradeScore) {
            this.saveColumnId = null;
            const colScores = this.gradeBook.resultsData[result.columnId];
            if (!colScores) { return; }
            colScores[result.targetUserId] = result;
        }

        onOverwriteResult(result: GradeScore) {
            this.saveColumnId = result.columnId;
            this.connector?.overwriteGradeResult(result, this.updateResult);
        }

        onRevertOverwrittenResult(result: GradeScore) {
            this.saveColumnId = result.columnId;
            this.connector?.revertOverwrittenGradeResult(result, this.updateResult);
        }

        onUpdateScoreComment(result: GradeScore) {
            this.saveColumnId = result.columnId;
            this.connector?.updateGradeResultComment(result, this.updateResult);
        }
    }
</script>

<style>
.u-flex {
    display: flex;
}

.u-gap-small {
    gap: 5px;
}

.u-flex-wrap {
    flex-flow: wrap;
}

.u-txt-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/*.banner {
    background-color: #2b6597;
    border-bottom: 1px solid #14578f;
    padding: 20px 15px;
}

.banner-header {
    color: #fff;
    font-size: 2.2rem;
    margin: 0;
}*/
</style>

<style lang="scss" scoped>
.gradebook-toolbar {
    display: flex;
    column-gap: 20px;
    flex-flow: wrap;
    margin: 25px 20px 20px;
    row-gap: 10px;

    .input-group {
        flex: 1;
        min-width: 200px;
        z-index: 1;
    }
}

.gradebook-table-container {
    margin: 0 20px 20px;
}

.gradebook-create-actions {
    display:flex;
    gap: 5px;
    justify-content: flex-end;
    margin-left: auto;

    .btn {
        padding: 3px 9px 3px 7px;
    }

    .fa {
        color: #406e8e;
        margin-right: 5px;
    }
}

#dropdown-main {
    flex: 1;
}
</style>

<style>
.lds-ellipsis {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}

.lds-ellipsis div {
    position: absolute;
    top: 13px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: hsla(190, 40%, 45%, 1);
    animation-timing-function: cubic-bezier(0, 1, 1, 0);
}

.lds-ellipsis div:nth-child(1) {
    left: 8px;
    animation: lds-ellipsis1 0.6s infinite;
}

.lds-ellipsis div:nth-child(2) {
    left: 8px;
    animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(3) {
    left: 32px;
    animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(4) {
    left: 56px;
    animation: lds-ellipsis3 0.6s infinite;
}

@keyframes lds-ellipsis1 {
    0% {
        transform: scale(0);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes lds-ellipsis3 {
    0% {
        transform: scale(1);
    }
    100% {
        transform: scale(0);
    }
}

@keyframes lds-ellipsis2 {
    0% {
        transform: translate(0, 0);
    }
    100% {
        transform: translate(24px, 0);
    }
}
</style>