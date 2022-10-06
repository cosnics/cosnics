import APIConfig from "@/connector/APIConfig";

export type ItemId = string|number;
export type ColumnId = string|number;
export type ResultType = number|'gafw'|'afw'|null;

export interface GradeItem {
    readonly id: ItemId;
    readonly title: string;
    readonly breadcrumb: string[];
    checked?: boolean;
    disabled?: boolean;
}

export interface GradeColumn {
    id: ColumnId;
    type: string;
    title?: string|null;
    weight: number|null;
    countForEndResult: boolean;
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
    sourceScoreAbsent: false;
    sourceScoreAuthAbsent: false;
    overwritten: boolean;
    newScore: number|null;
    newScoreAbsent: boolean;
    newScoreAuthAbsent: boolean;
    isTotal: boolean;
    comment: string|null;
}

export type ResultsData = Record<ColumnId, Record<number, GradeScore>>;

const COUNT_FOR_END_RESULT_DEFAULT = true;
const AUTH_PRESENCE_END_RESULT_DEFAULT = 0;
const UNAUTH_PRESENCE_END_RESULT_DEFAULT = 2;

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

    public readonly dataId;
    public currentVersion: number|null;
    public readonly title;

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

    getGradeColumn(columnId: ColumnId) {
        return this.gradeColumns.find(column => column.id === columnId);
    }

    getCategory(categoryId: number) {
        return this.allCategories.find(category => category.id === categoryId);
    }

    get gradedItemsWithCheckedStatus(): GradeItem[] {
        const itemIds = this.gradeColumns.reduce((ids: ItemId[], column: GradeColumn) => ids.concat(column.subItemIds), []);

        return this.gradeItems.map(item => ({
            id: item.id, title: item.title, breadcrumb: item.breadcrumb, checked: itemIds.indexOf(item.id) !== -1
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

    countsForEndResult(columnId: ColumnId): boolean {
        return !!(this.getGradeColumn(columnId)?.countForEndResult);
    }

    getWeight(columnId: ColumnId): number {
        const column = this.getGradeColumn(columnId);
        const weight = column ? column.weight : null;
        if (weight === null) {
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
        return weight;
    }

    setWeight(columnId: ColumnId, weight: number|null) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.weight = weight;
        }
    }

    getTitle(columnId: ColumnId): string|null {
        const column = this.getGradeColumn(columnId);
        if (column) {
            if (column.title) { return column.title; }
            if (column.type === 'item' || column.type === 'group') {
                return this.getGradeItem(column.subItemIds[0])!.title;
            }
        }
        return null;
    }

    setTitle(columnId: ColumnId, title: string) {
        const column = this.getGradeColumn(columnId);
        if (column) {
            column.title = title || null;
        }
    }

    isGrouped(columnId: ColumnId) {
        return this.getGradeColumn(columnId)?.type === 'group';
    }

    getColumnSubItems(columnId: ColumnId): GradeItem[] {
        const column = this.getGradeColumn(columnId);
        if (!column) return [];
        return column.subItemIds.map(itemId => this.getGradeItem(itemId)!);
    }

    getResult(columnId: ColumnId, userId: number): ResultType {
        if (!this.resultsData[columnId]) { return null; }
        const score: GradeScore = this.resultsData[columnId][userId];
        if (!score) { return null; }
        if (score.overwritten) {
            if (score.newScoreAbsent) { return 'afw'; }
            if (score.newScoreAuthAbsent) { return 'gafw'; }
            return score.newScore;
        }
        if (score.sourceScoreAbsent) { return 'afw'; }
        if (score.sourceScoreAuthAbsent) { return 'gafw'; }
        return score.sourceScore;
    }

    getEndResult(userId: number) {
        let endResult = 0;
        let maxWeight = 0;
        this.gradeColumns.filter(column => column.countForEndResult).forEach(column => {
            let result = this.getResult(column.id, userId);
            if (result === null) {
                result = 'afw';
            }
            const weight = this.getWeight(column.id);
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
        if (value === 'afw') {
            score.newScoreAbsent = true;
            score.newScoreAuthAbsent = false;
            score.newScore = null;
        } else if (value === 'gafw') {
            score.newScoreAbsent = false;
            score.newScoreAuthAbsent = true;
            score.newScore = null;
        } else {
            score.newScoreAbsent = false;
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
        score.newScoreAbsent = false;
        score.newScoreAuthAbsent = false;
        score.newScore = null;
        return score;
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
        this.categories = this.categories.filter(c => c !== category);
        this.nullCategory.columnIds = [...this.nullCategory.columnIds, ...columnIds];
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
            countForEndResult: COUNT_FOR_END_RESULT_DEFAULT,
            authPresenceEndResult: AUTH_PRESENCE_END_RESULT_DEFAULT,
            unauthPresenceEndResult: UNAUTH_PRESENCE_END_RESULT_DEFAULT
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
        const newScore = {id, title: 'Score', type: 'standalone', subItemIds: [], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2};
        this.gradeColumns.push(newScore);
        this.nullCategory.columnIds.push(id);
        return newScore;
    }

    createNewCategory() {
        const id = Math.max.apply(null, this.categories.map(cat => cat.id)) + 1;
        const newCategory = { id, title: 'Categorie', color: '#92eded', columnIds: [] };
        this.categories.push(newCategory);
        return newCategory;
    }

    addSubItem(item: GradeItem, columnId: ColumnId) {
        const column = this.getGradeColumn(columnId);
        if (!column) { return; }
        const srcColumn = this.findGradeColumnWithGradeItem(item.id);
        column.title = this.getTitle(columnId);
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