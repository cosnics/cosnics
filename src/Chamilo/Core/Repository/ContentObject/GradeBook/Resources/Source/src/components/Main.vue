<i18n>
{
    "en": {
        "category": "Category",
        "error-Conflict": "The server responded with an error due to a conflict. Probably someone else is working on the same gradebook at this time. Please refresh the page and try again.",
        "error-Forbidden": "The server responded with an error. Possibly your last change(s) haven't been saved correctly. Please refresh the page and try again.",
        "error-LoggedOut": "It looks like you have been logged out. Your changes have not been saved. Please reload the page after logging in and try again.",
        "error-NotFound": "The server responded with an error. Possibly your last change(s) haven't been saved correctly. Please refresh the page and try again.",
        "error-Timeout": "The server took too long to respond. Your changes have possibly not been saved. You can try again later.",
        "error-Unknown": "An unknown error happened. Possibly your last change(s) haven't been saved. Please refresh the page and try again.",
        "find-student": "Find student",
        "import": "Import",
        "new": "New",
        "new-category": "New category",
        "new-score": "New score",
        "show": "Show",
        "synchronize-scores": "Synchronize",
        "update-final-scores": "Update final scores"
    },
    "nl": {
        "category": "Categorie",
        "error-Conflict": "Serverfout vanwege een conflict. Misschien werkt iemand anders ook nog aan dit puntenboekje op dit moment. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-Forbidden": "Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-LoggedOut": "Het lijkt erop dat je uitgelogd bent. Je wijzigingen werden niet opgeslagen. Herlaad deze pagina nadat je opnieuw ingelogd bent en probeer het opnieuw.",
        "error-NotFound": "Serverfout. Mogelijk werden je wijzigingen niet (correct) opgeslagen. Gelieve de pagina te herladen en opnieuw te proberen.",
        "error-Timeout": "De server deed er te lang over om te antwoorden. Je wijzigingen werden mogelijk niet opgeslagen. Probeer het later opnieuw.",
        "error-Unknown": "Je laatste wijzigingen werden mogelijk niet opgeslagen vanwege een onbekende fout. Gelieve de pagina te herladen en opnieuw te proberen.",
        "find-student": "Zoek student",
        "import": "Importeer",
        "new": "Nieuw",
        "new-category": "Nieuwe categorie",
        "new-score": "Nieuwe score",
        "show": "Toon",
        "synchronize-scores": "Synchronizeer",
        "update-final-scores": "Update eindcijfers"
    }
}
</i18n>
<template>
    <div class="u-contents">
        <div v-if="gradeBook">
            <div class="u-flex u-flex-wrap gradebook-toolbar">
                <div class="input-group">
                    <input class="form-control" type="text" v-model="searchTerm" :placeholder="$t('find-student')">
                    <div class="input-group-btn"><button name="clear" value="clear" class="btn btn-default" @click="searchTerm = ''"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></button></div>
                </div>
                <grades-dropdown id="dropdown-main" :graded-items="gradeBook.gradedItemsWithCheckedStatus" @toggle="toggleGradeItem" />
                <div class="u-flex u-justify-content-end u-gap-small u-ml-auto gradebook-create-actions">
                    <button class="btn btn-default btn-sm" @click="synchronizeGradeBook"><i class="fa fa-refresh" aria-hidden="true"></i>{{ $t('synchronize-scores') }}</button>
                    <div class="btn-group">
                        <a data-toggle="dropdown" aria-haspopup="true" class="btn btn-default btn-sm dropdown-toggle">
                            <i class="fa fa-plus" aria-hidden="true"></i><span>{{ $t('new') }}</span> <span class="caret" aria-hidden="true"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="u-cursor-pointer"><a @click.prevent="createNewScore">{{ $t('new-score') }}</a></li>
                            <li class="u-cursor-pointer"><a @click.prevent="createNewCategory">{{ $t('new-category') }}</a></li>
                            <li class="u-cursor-pointer"><a :href="apiConfig.gradeBookImportCsvURL">{{ $t('import') }}&mldr;</a></li>
                        </ul>
                    </div>
                    <button v-if="gradeBook.totalsNeedUpdating" class="btn btn-update-totals btn-primary btn-sm u-font-medium u-text-upper" @click="updateTotalScores">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>{{ $t('update-final-scores') }}
                    </button>
                    <div class="btn-group">
                        <a data-toggle="dropdown" aria-haspopup="true" class="btn btn-default btn-sm dropdown-toggle" :title="`${$t('show')} ${itemsPerPage} items`">
                            <span>{{ $t('show') }} {{itemsPerPage}} items</span> <span class="caret" aria-hidden="true"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li v-for="count in [5, 10, 15, 20, 50]" :key="'per-page-' + count" class="u-cursor-pointer">
                                <a :class="itemsPerPage === count ? 'selected' : 'not-selected'" @click="setItemsPerPage(count)">
                                    <span>{{ $t('show') }} {{count}} items</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="gradebook-table-container">
                <grades-table :grade-book="gradeBook" :search-terms="studentSearchTerms" :busy="tableBusy" :add-column-id="addColumnId" :save-column-id="saveColumnId" :save-category-id="saveCategoryId" :items-per-page="itemsPerPage" :grade-book-root-url="apiConfig.gradeBookRootURL"
                              @item-settings="itemSettings = $event" @category-settings="categorySettings = $event"
                              @update-score-comment="onUpdateScoreComment" @overwrite-result="onOverwriteResult" @revert-overwritten-result="onRevertOverwrittenResult"
                              @change-category="onChangeCategory" @move-category="onMoveCategory"
                              @change-gradecolumn="onChangeGradeColumn" @change-gradecolumn-category="onChangeGradeColumnCategory" @move-gradecolumn="onMoveGradeColumn"></grades-table>
            </div>
            <item-settings v-if="itemSettings !== null" :grade-book="gradeBook" :column-id="itemSettings" @close="itemSettings = null"
                           @item-settings="itemSettings = $event" @change-gradecolumn="onChangeGradeColumn" @add-subitem="onAddSubItem" @remove-subitem="onRemoveSubItem" @remove-column="onRemoveColumn" />
            <category-settings v-if="selectedCategory" :grade-book="gradeBook" :category="selectedCategory" @close="closeSelectedCategory" @change-category="onChangeCategory" @remove-category="onRemoveCategory" />
        </div>
        <div v-else class="lds-ellipsis" aria-hidden="true"><div></div><div></div><div></div><div></div></div>
        <error-display v-if="errorData" @close="closeErrorDisplay">{{ $t(`error-${errorData.type}`) }}</error-display>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import GradesDropdown from './GradesDropdown.vue';
    import GradesTable from './GradesTable.vue';
    import GradeBook, {Category, GradeColumn, GradeItem, ColumnId, GradeScore, ResultsData} from '../domain/GradeBook';
    import ItemSettings from './ItemSettings.vue';
    import CategorySettings from './CategorySettings.vue';
    import Connector from '../connector/Connector';
    import APIConfig from "@/connector/APIConfig";
    import ErrorDisplay from './ErrorDisplay.vue';

    const ITEMS_PER_PAGE_KEY = 'chamilo-gradebook.itemsPerPage';

    @Component({
        components: {ErrorDisplay, GradesTable, GradesDropdown, ItemSettings, CategorySettings }
    })
    export default class Main extends Vue {
        private gradeBook: GradeBook|null = null;
        private connector: Connector|null = null;
        private itemSettings: number|null = null;
        private categorySettings: number|null = null;
        private studentSearchTerm = '';
        private studentSearchTerms: string[] = [];
        private tableBusy = false;
        private saveColumnId: ColumnId|null = null;
        private saveCategoryId: number|null = null;
        private itemsPerPage: number = 5;
        private errorData: string|null = null;
        private addColumnId: ColumnId|null = null;

        @Prop({type: Object, default: () => null}) readonly apiConfig!: APIConfig;

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
            if (!this.gradeBook) { return; }
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
            if (!this.gradeBook) { return; }
            const column = this.gradeBook.addGradeColumnFromItem(item);
            this.addColumnId = column.id;
            this.tableBusy = true;
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
                this.resetGradeBook();
                this.tableBusy = false;
                this.addColumnId = null;
            });
        }

        removeGradeItem(item: GradeItem) {
            if (!this.gradeBook) { return; }
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
            return this.gradeBook?.categories.find(cat => cat.id === this.categorySettings) || null;
        }

        resetGradeBook() {
            const gradeBook = this.gradeBook;
            this.gradeBook = null;
            this.$nextTick(() => { this.gradeBook = gradeBook; });
        }

        createNewCategory() {
            if (!this.gradeBook) { return; }
            const category = this.gradeBook.createNewCategory();
            this.tableBusy = true;
            this.connector?.addCategory(category, (cat: any) => {
                category.id = cat.id;
                this.categorySettings = cat.id;
                this.resetGradeBook();
                this.tableBusy = false;
            });
        }

        async synchronizeGradeBook() {
            if (!this.gradeBook) { return; }
            const gradeBook = this.gradeBook;
            this.tableBusy = true;
            await this.connector?.synchronizeGradeBook((scores: GradeScore[]) => {
                const resultsData = gradeBook.resultsData;
                if (!resultsData['totals']) {
                    Vue.set(resultsData, 'totals', {});
                }
                scores.forEach(score => {
                    if (score.isTotal) {
                        resultsData['totals'][score.targetUserId] = score;
                        return;
                    }
                    if (!resultsData[score.columnId]) {
                        Vue.set(resultsData, score.columnId, {});
                    }
                    resultsData[score.columnId][score.targetUserId] = score;
                });
                this.tableBusy = false;
            });
        }

        async updateTotalScores() {
            if (!this.gradeBook) { return; }
            const gradeBook = this.gradeBook;
            this.tableBusy = true;
            await this.connector?.calculateTotalScores((scores: GradeScore[]) => {
                const resultsData = gradeBook.resultsData;
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
            if (!this.gradeBook) { return; }
            const column = this.gradeBook.createNewScore();
            this.addColumnId = column.id;
            this.tableBusy = true;
            this.connector?.addGradeColumn(column, ({id}: {id: ColumnId}, scores: GradeScore[]) => {
                this.updateGradeColumnWithScores(column, id, scores);
                this.resetGradeBook();
                this.tableBusy = false;
                this.addColumnId = null;
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
            if (!this.gradeBook) { return; }
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
            if (!this.gradeBook) { return; }
            const category = this.gradeBook.allCategories.find(category => category.columnIds.indexOf(column.id) !== -1);
            if (category) {
                this.tableBusy = true;
                this.connector?.moveGradeColumn(column, category.columnIds.indexOf(column.id), () => {
                    this.tableBusy = false;
                });
            }
        }

        onAddSubItem(item: GradeItem, columnId: ColumnId) {
            if (!this.gradeBook) { return; }
            const gradeBook = this.gradeBook;
            this.tableBusy = true;
            this.connector?.addColumnSubItem(columnId, item.id, (column: GradeColumn, scores: GradeScore[]) => {
                //console.log('scores', scores);
                const resultsData = gradeBook.resultsData;
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
            if (!this.gradeBook) { return; }
            const gradeBook = this.gradeBook;
            this.tableBusy = true;
            this.connector?.removeColumnSubItem(columnId, item.id, (column: GradeColumn, scores: GradeScore[]) => {
                //console.log('scores', scores);
                const resultsData = gradeBook.resultsData;
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
            if (!this.gradeBook) { return; }
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

        loadItemsPerPage() {
            this.itemsPerPage = parseInt(localStorage.getItem(ITEMS_PER_PAGE_KEY) || '5');
        }

        setItemsPerPage(count: number) {
            this.itemsPerPage = count;
            localStorage.setItem(ITEMS_PER_PAGE_KEY, String(count));
        }

        setError(data: any): void {
            this.errorData = data;
        }

        closeErrorDisplay() {
            this.errorData = null;
            this.saveColumnId = null;
            this.saveCategoryId = null;
            this.tableBusy = false;
        }

        async load(): Promise<void> {
            const allData: any = await Connector.loadGradeBookData(this.apiConfig.loadGradeBookDataURL, this.apiConfig.csrfToken);
            console.log(allData);
            if (allData) {
                this.gradeBook = GradeBook.from(allData.gradebook);
                this.gradeBook.users = allData.users;
                this.connector = new Connector(this.apiConfig, this.gradeBook.dataId, this.gradeBook.currentVersion);
                this.connector.addErrorListener(this);
                const resultsData: ResultsData = {'totals': {}};
                allData.scores.forEach((score: GradeScore) => {
                    if (score.isTotal) {
                        resultsData['totals'][score.targetUserId] = score;
                        return;
                    }
                    if (!resultsData[score.columnId]) {
                        resultsData[score.columnId] = {};
                    }
                    resultsData[score.columnId][score.targetUserId] = score;
                });
                this.gradeBook.resultsData = resultsData;
            }
            console.log(this.gradeBook);
        }

        mounted() {
            this.load();
            this.loadItemsPerPage();
            //console.log(this);
        }
    }
</script>

<style>
.u-relative {
    position: relative;
}

.u-block {
    display: block;
}

.u-contents {
    display: contents;
}

.u-inline-block {
    display: inline-block;
}

.u-flex {
    display: flex;
}

.u-flex-column {
    flex-direction: column;
}

.u-align-items-baseline {
    align-items: baseline;
}

.u-align-items-center {
    align-items: center;
}

.u-gap-small {
    gap: 5px;
}

.u-gap-small-2x {
    gap: 10px;
}

.u-flex-wrap {
    flex-flow: wrap;
}

.u-justify-content-center {
    justify-content: center;
}

.u-justify-content-end {
    justify-content: flex-end;
}

.u-justify-content-between {
    justify-content: space-between;
}

.u-ml-auto {
    margin-left: auto;
}

.u-txt-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.u-text-center {
    text-align: center;
}

.u-text-end {
    text-align: end;
}

.u-text-upper {
    text-transform: uppercase;
}

.u-font-normal {
    font-weight: 400;
}

.u-font-medium {
    font-weight: 500;
}

.u-font-italic {
    font-style: italic;
}

.u-cursor-pointer {
    cursor: pointer;
}

.u-cursor-help {
    cursor: help;
}
</style>

<style lang="scss" scoped>
.gradebook-toolbar {
    column-gap: 20px;
    margin: 25px 20px 16px;
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
    .btn {
        padding: 3px 9px 3px 7px;
    }

    .fa {
        color: #406e8e;
        margin-right: 5px;
    }
}

.dropdown-toggle {
    line-height: 26px;
}

#dropdown-main {
    flex: 1;
}

.btn-update-totals {
    margin-inline: 10px;
    padding-inline: 30px;

    .fa-exclamation-circle {
        color: white;
        font-size: 14px;
        margin-right: 5px;
    }
}

.lds-ellipsis {
    margin-left: 10px;
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