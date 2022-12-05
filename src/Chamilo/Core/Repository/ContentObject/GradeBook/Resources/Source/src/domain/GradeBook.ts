import APIConfig from "@/connector/APIConfig";

export type ItemId = string|number;
export type ColumnId = string|number;
export type ResultType = number|'aabs'|null;

export interface GradeItem {
    readonly id: ItemId;
    readonly title: string;
    readonly breadcrumb: string[];
    readonly removed: boolean;
    checked?: boolean;
    disabled?: boolean;
}

export interface GradeColumn {
    id: ColumnId;
    type: string;
    title?: string|null;
    weight: number|null;
    countForEndResult: boolean;
    released: boolean;
    authPresenceEndResult: number;
    unauthPresenceEndResult: number;
    subItemIds: ItemId[];
}

export interface Category {
    id: number;
    color: string;
    title: string;
    columnIds: ColumnId[];
}

export interface User {
    id: number;
    firstName: string;
    lastName: string;
}

export interface GradeScore {
    id: number;
    columnId: ColumnId;
    targetUserId: number;
    sourceScore: number|null;
    sourceScoreAuthAbsent: false;
    overwritten: boolean;
    newScore: number|null;
    newScoreAuthAbsent: boolean;
    isTotal: boolean;
    comment: string|null;
}

export interface CSVImportField {
    key: string;
    label: string;
    type: string;
}

export interface CSVImportResult {
    lastname: string;
    firstname: string;
    id: string;
    user_id: number;
    [key: string]: string|number|null;
}

export type ResultsData = Record<ColumnId, Record<number, GradeScore>>;

export default class GradeBook {
    static readonly NO_SCORE = 0;
    static readonly MAX_SCORE = 1;
    static readonly MIN_SCORE = 2;

    public gradeItems: GradeItem[] = [];
    public gradeColumns: GradeColumn[] = [];
    public categories: Category[] = [];
    public nullCategory: Category = {id: 0, title: '', color: '', columnIds: []};
    public users: User[] = [];
    public resultsData: ResultsData = {};

    public readonly dataId: number;
    public currentVersion: number|null;
    public readonly title: string;

    constructor(dataId: number, currentVersion: number|null, title: string) {
        this.dataId = dataId;
        this.title = title;
        this.currentVersion = currentVersion;
    }

    get allCategories(): Category[] {
        return [...this.categories, this.nullCategory];
    }

    getGradeItem(itemId: ItemId) {
        return this.gradeItems.find(item => item.id === itemId);
    }

    getGradeColumn(columnId: ColumnId): GradeColumn|undefined {
        return this.gradeColumns.find(column => column.id === columnId);
    }

    getCategory(categoryId: number) {
        return this.allCategories.find(category => category.id === categoryId);
    }

    get gradedItemsWithCheckedStatus(): GradeItem[] {
        const itemIds = this.gradeColumns.reduce((ids: ItemId[], column: GradeColumn) => ids.concat(column.subItemIds), []);

        return this.gradeItems.map(item => ({
            id: item.id, title: item.title, breadcrumb: item.breadcrumb, removed: item.removed, checked: itemIds.indexOf(item.id) !== -1
        }));
    }

    getGradedItemsFilteredByColumn(columnId: ColumnId): GradeItem[] {
        const column = this.getGradeColumn(columnId);
        if (!column) { return []; }
        return this.gradeItems.map(item => {
            const checked = column.subItemIds.indexOf(item.id) !== -1;
            let disabled = false;
            if (checked && column.type !== 'group') {
                disabled = true;
            } else {
                const col = this.findGradeColumnWithGradeItem(item.id);
                if (col && col.type === 'group' && col !== column) {
                    disabled = true;
                }
            }
            return {...item, checked, disabled};
        })
    }

    get hasUnreleasedScores() {
        return this.gradeColumns.some(column => column.countForEndResult && !column.released);
    }

    getWeight(column: GradeColumn): number {
        if (column.weight === null) {
            return this.eqRestWeight;
        }
        return column.weight;
    }

    get eqRestWeight() {
        let rest = 100;
        let noRest = 0;
        this.gradeColumns.filter(column => column.countForEndResult)
            .forEach(column => {
                if (column.weight !== null) {
                    rest -= column.weight;
                } else {
                    noRest += 1;
                }
            });
        return rest / noRest;
    }

    setWeight(columnId: ColumnId, weight: number|null) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.weight = weight;
        }
    }

    getTitle(column: GradeColumn): string {
        if (column.title) { return column.title; }
        if (column.type === 'item' || column.type === 'group') {
            return this.getGradeItem(column.subItemIds[0])?.title || '';
        }
        return '';
    }

    setTitle(columnId: ColumnId, title: string) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.title = title || null;
        }
    }

    hasRemovedSourceData(column: GradeColumn) {
        const subItems = this.getColumnSubItems(column);
        return subItems.some(item => item.removed);
    }

    getColumnSubItems(column: GradeColumn): GradeItem[] {
        return column.subItemIds.map(itemId => this.getGradeItem(itemId)!);
    }

    hasResult(columnId: ColumnId, userId: number) {
        if (!this.resultsData[columnId]) { return false; }
        const score: GradeScore = this.resultsData[columnId][userId];
        return !!score;
    }

    getResult(columnId: ColumnId, userId: number): ResultType {
        if (!this.resultsData[columnId]) { return null; }
        const score: GradeScore = this.resultsData[columnId][userId];
        if (!score) { return null; }
        if (score.overwritten) {
            if (score.newScoreAuthAbsent) { return 'aabs'; }
            return score.newScore;
        }
        if (score.sourceScoreAuthAbsent) { return 'aabs'; }
        return score.sourceScore;
    }

    getEndResult(userId: number) {
        let endResult = 0;
        let maxWeight = 0;
        this.gradeColumns.filter(column => column.countForEndResult).forEach(column => {
            const result = this.getResult(column.id, userId);
            const weight = this.getWeight(column);
            if (typeof result === 'number') {
                maxWeight += weight;
            } else if (result === 'aabs') {
                if (column.authPresenceEndResult !== GradeBook.NO_SCORE) {
                    maxWeight += weight;
                    if (column.authPresenceEndResult === GradeBook.MAX_SCORE) {
                        endResult += weight;
                    }
                }
            } else if (result === null) {
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

    isOverwrittenResult(columnId: ColumnId, userId: number): boolean {
        if (!this.resultsData[columnId]) { return false; }
        const score = this.resultsData[columnId][userId];
        if (!score) { return false; }
        return score.overwritten;
    }

    overwriteResult(columnId: ColumnId, userId: number, value: ResultType): GradeScore|false {
        if (!this.resultsData[columnId]) { return false; }
        const score: GradeScore = this.resultsData[columnId][userId];
        if (!score) { return false; }
        score.overwritten = true;
        if (value === 'aabs') {
            score.newScoreAuthAbsent = true;
            score.newScore = null;
        } else {
            score.newScoreAuthAbsent = false;
            score.newScore = value;
        }
        return score;
    }

    revertOverwrittenResult(columnId: ColumnId, userId: number): GradeScore|false {
        if (!this.resultsData[columnId]) { return false; }
        const score: GradeScore = this.resultsData[columnId][userId];
        if (!score) { return false; }
        score.overwritten = false;
        score.newScoreAuthAbsent = false;
        score.newScore = null;
        return score;
    }

    userTotalNeedsUpdating(user: User): boolean {
        const total = this.getResult('totals', user.id);
        if (total === null) { return false; } // unsynchronized user, cannot update
        if (typeof total !== 'number') { return true; }
        return total.toFixed(2) !== this.getEndResult(user.id).toFixed(2);
    }

    get totalsNeedUpdating(): boolean {
        return this.users.some(user => this.userTotalNeedsUpdating(user));
    }

    getResultComment(columnId: ColumnId, userId: number): string|null {
        if (!this.resultsData[columnId]) { return null; }
        const score: GradeScore = this.resultsData[columnId][userId];
        if (!score) { return null; }
        return score.comment;
    }

    updateResultComment(columnId: ColumnId, userId: number, comment: string|null): GradeScore|false {
        if (!this.resultsData[columnId]) { return false; }
        const score = this.resultsData[columnId][userId];
        if (!score) { return false; }
        score.comment = comment;
        return score;
    }

    addItemToCategory(categoryId: number, columnId: ColumnId) {
        const category = categoryId === 0 ? this.nullCategory : this.getCategory(categoryId);
        if (category?.columnIds.indexOf(columnId) === -1) {
            this.allCategories.forEach(cat => {
                if (cat.columnIds.indexOf(columnId) !== -1) {
                    cat.columnIds = cat.columnIds.filter(id => id !== columnId);
                }
            });
            category.columnIds.push(columnId);
        }
    }

    removeCategory(category: Category) {
        if (category === this.nullCategory) { return; }
        const columnIds = category.columnIds;
        const index = this.categories.indexOf(category);
        if (index < 0) { return; }
        this.categories.splice(index, 1);
        if (columnIds.length) {
            this.nullCategory.columnIds = [...this.nullCategory.columnIds, ...columnIds];
        }
    }

    public updateGradeColumnId(column: GradeColumn, newId: ColumnId) {
        const oldId = column.id;
        column.id = newId;
        this.allCategories.forEach(cat => {
            const index = cat.columnIds.indexOf(oldId);
            if (index !== -1) {
                cat.columnIds[index] = newId;
            }
        });
    }

    addGradeColumnFromItem(item: GradeItem) {
        const newId = this.createNewColumnId();

        const column = {
            id: newId, type: 'item', title: null, subItemIds: [item.id], weight: null,
            countForEndResult: true,
            released: true,
            authPresenceEndResult: GradeBook.NO_SCORE,
            unauthPresenceEndResult: GradeBook.MIN_SCORE
        };
        this.gradeColumns.push(column);
        this.addItemToCategory(0, newId);

        return column;
    }

    findGradeColumnWithGradeItem(itemId: ItemId): GradeColumn|null {
        const column = this.gradeColumns.find(column => column.subItemIds.indexOf(itemId) !== -1);
        return column || null;
    }

    removeSubItem(item: GradeItem) {
        this.gradeColumns.forEach(column => {
            if (column.subItemIds.length) {
                column.subItemIds = column.subItemIds.filter(id => id !== item.id);
            }
        });
        if (item.removed) {
            this.gradeItems = this.gradeItems.filter(gradeItem => gradeItem !== item);
        }
    }

    createNewIdWithPrefix(prefix: string): string {
        const itemIds = this.gradeColumns.map(column => column.id);

        let i = 1;
        while (itemIds.indexOf(prefix + i) !== -1) {
            i += 1;
        }
        return prefix + i;
    }

    createNewColumnId() {
        return this.createNewIdWithPrefix('col');
    }

    createNewStandaloneScoreId() {
        return this.createNewIdWithPrefix('sc');
    }

    createNewScore(): GradeColumn {
        const id = this.createNewStandaloneScoreId();
        const newScore = {id, title: 'Score', type: 'standalone', subItemIds: [], weight: null, countForEndResult: true, released: true, authPresenceEndResult: GradeBook.NO_SCORE, unauthPresenceEndResult: GradeBook.MIN_SCORE};
        this.gradeColumns.push(newScore);
        this.nullCategory.columnIds.push(id);
        return newScore;
    }

    createNewCategory() {
        const id = this.categories.length ? Math.max.apply(null, this.categories.map(cat => cat.id)) + 1 : 1;
        const newCategory = { id, title: 'Categorie', color: '#92eded', columnIds: [] };
        this.categories.push(newCategory);
        return newCategory;
    }

    addSubItem(item: GradeItem, columnId: ColumnId) {
        const column = this.getGradeColumn(columnId);
        if (!column) { return; }
        const srcColumn = this.findGradeColumnWithGradeItem(item.id);
        column.title = this.getTitle(column);
        column.type = 'group';
        column.subItemIds.push(item.id);
        if (srcColumn) {
            this.gradeColumns = this.gradeColumns.filter(column => column !== srcColumn);
            this.allCategories.forEach(cat => {
                cat.columnIds = cat.columnIds.filter(id => id !== srcColumn.id);
            });
            delete this.resultsData[srcColumn.id];
        }
    }

    removeColumn(column: GradeColumn) {
        column.subItemIds.forEach(itemId => {
            this.removeSubItem(this.getGradeItem(itemId)!);
        });
        delete this.resultsData[column.id];
        this.gradeColumns = this.gradeColumns.filter(col => col !== column);
        this.allCategories.forEach(cat => {
            cat.columnIds = cat.columnIds.filter(id => id !== column.id);
        });
    }

    static from(gradeBookObject: any): GradeBook {
        const gradeBook = new GradeBook(gradeBookObject.dataId, gradeBookObject.version, gradeBookObject.title);
        gradeBook.gradeItems = gradeBookObject.gradeItems;
        gradeBook.gradeColumns = gradeBookObject.gradeColumns;
        gradeBook.categories = gradeBookObject.categories;
        gradeBook.nullCategory = gradeBookObject.nullCategory;
        return gradeBook;
    }
}