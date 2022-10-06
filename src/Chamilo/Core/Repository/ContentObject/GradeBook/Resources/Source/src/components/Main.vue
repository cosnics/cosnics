<template>
    <div>
        <div class="gradebook-toolbar">
            <input class="form-control" type="text" placeholder="Zoek student">
            <grades-dropdown id="dropdown-main" :graded-items="gradeBook.gradedItemsWithCheckedStatus" @toggle="toggleGradeItem"></grades-dropdown>
        </div>
        <div class="gradebook-table-container">
            <div class="gradebook-create-actions">
                <button class="btn btn-default btn-sm" @click="synchronizeGradeBook"><i aria-hidden="true" class="fa fa-refresh"></i>Synchronizeer scores</button>
                <button class="btn btn-default btn-sm" @click="updateTotalScores"><i aria-hidden="true" class="fa fa-refresh"></i>Update eindcijfers</button>
                <button class="btn btn-default btn-sm" @click="createNewScore"><i aria-hidden="true" class="fa fa-plus"></i>Nieuwe score</button>
                <button class="btn btn-default btn-sm" @click="createNewCategory"><i aria-hidden="true" class="fa fa-plus"></i>Categorie</button>
            </div>
            <grades-table :grade-book="gradeBook" @item-settings="itemSettings = $event" @category-settings="categorySettings = $event"
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

        @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
        @Prop(Connector) readonly connector!: Connector|null;

        constructor() {
            super();
            this.updateResult = this.updateResult.bind(this);
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
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
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
            this.categorySettings = category.id;
            this.connector?.addCategory(category);
        }

        async synchronizeGradeBook() {
            await this.connector?.synchronizeGradeBook((scores: GradeScore[]) => {
                const resultsData = this.gradeBook.resultsData;
                scores.forEach(score => {
                    if (!resultsData[score.columnId]) {
                        Vue.set(resultsData, score.columnId, {});
                    }
                    resultsData[score.columnId][score.targetUserId] = score;
                });
            });
        }

        async updateTotalScores() {
            await this.connector?.calculateTotalScores((scores: GradeScore[]) => {
                const resultsData = this.gradeBook.resultsData;
                if (!resultsData['totals']) {
                    Vue.set(resultsData, 'totals', {});
                }
                scores.forEach(score => {
                    resultsData['totals'][score.targetUserId] = score;
                });
                console.log(this.gradeBook);
            });
        }

        createNewScore() {
            const column = this.gradeBook.createNewScore();
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
            });
        }

        closeSelectedCategory() {
            this.categorySettings = null;
        }

        onChangeCategory(category: Category) {
            this.connector?.updateCategory(category);
        }

        onMoveCategory(category: Category) {
            this.connector?.moveCategory(category, this.gradeBook.categories.indexOf(category));
        }

        onRemoveCategory(category: Category) {
            this.connector?.removeCategory(category);
        }

        onChangeGradeColumn(gradeColumn: GradeColumn) {
            this.connector?.updateGradeColumn(gradeColumn);
        }

        onChangeGradeColumnCategory(gradeColumn: GradeColumn, categoryId: number|null) {
            this.connector?.updateGradeColumnCategory(gradeColumn, categoryId);
        }

        onMoveGradeColumn(column: GradeColumn) {
            const category = this.gradeBook.allCategories.find(category => category.columnIds.indexOf(column.id) !== -1);
            if (category) {
                this.connector?.moveGradeColumn(column, category.columnIds.indexOf(column.id));
            }
        }

        onAddSubItem(item: GradeItem, columnId: ColumnId) {
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
            });
        }

        onRemoveSubItem(item: GradeItem, columnId: ColumnId) {
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
            });
        }

        onRemoveColumn(column: GradeColumn) {
            this.connector?.removeGradeColumn(column);
        }

        updateResult(result: GradeScore) {
            const colScores = this.gradeBook.resultsData[result.columnId];
            if (!colScores) { return; }
            colScores[result.targetUserId] = result;
        }

        onOverwriteResult(result: GradeScore) {
            this.connector?.overwriteGradeResult(result, this.updateResult);
        }

        onRevertOverwrittenResult(result: GradeScore) {
            this.connector?.revertOverwrittenGradeResult(result, this.updateResult);
        }

        onUpdateScoreComment(result: GradeScore) {
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
    gap: 20px;
    margin: 25px 20px 20px;

    .form-control {
        flex: 1;
    }
}

.gradebook-table-container {
    margin: -10px 20px 20px;
}

.gradebook-create-actions {
    display:flex;
    gap: 5px;
    justify-content: flex-end;
    margin: 0 0 10px;

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

