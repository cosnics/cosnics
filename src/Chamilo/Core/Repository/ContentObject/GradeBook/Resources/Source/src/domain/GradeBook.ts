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

export interface ResultValue {
    id: ColumnId;
    value: ResultType;
    ref: ItemId|null;
    overwritten: boolean;
}

export type Results = ResultValue[];

export interface ResultsData {
    readonly id: number;
    readonly student: string;
    results: Results;
}

export interface Category {
    id: number;
    color: string;
    title: string;
    columnIds: ColumnId[];
}

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
    public originalResultsData: any[] = [];
    public resultsData: ResultsData[] = [];

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

    getResult(results: Results, columnId: ColumnId): ResultType {
        const result = results.find(r => r.id === columnId);
        if (typeof result === 'undefined') { return null; }
        return result.value;
    }

    getEndResult(studentId: number) {
        const r = this.resultsData.find(res => res.id === studentId);
        if (!r) { return 0; }
        const results = r.results;
        let endResult = 0;
        let maxWeight = 0;
        this.gradeColumns.filter(column => column.countForEndResult).forEach(column => {
            let result = this.getResult(results, column.id);
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

    public updateGradeColumnId(column: GradeColumn, newId: ColumnId) {
        const oldId = column.id;
        column.id = newId;
        this.allCategories.forEach(cat => {
            const index = cat.columnIds.indexOf(oldId);
            if (index !== -1) {
                cat.columnIds[index] = newId;
            }
        });
        this.resultsData.forEach(d => {
            const result = d.results.find(r => r.id === oldId);
            if (result) {
                result.id = newId;
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

        this.originalResultsData.forEach(data => {
            const r = this.resultsData.find(d => d.id === data.id);
            if (r) {
                r.results.push({ id: newId, value: data.results.find((r: any) => r.id === item.id)?.value || null, ref: item.id, overwritten: false });
            }
        });
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

        this.resultsData.forEach(d => {
            d.results.forEach(res => {
                if (res.ref === item.id) {
                    res.value = null;
                    res.ref = null;
                    res.overwritten = false;
                }
            })
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

    createNewScore() {
        const id = this.createNewStandaloneScoreId();
        this.gradeColumns.push({id, title: 'Score', type: 'standalone', subItemIds: [], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2});
        this.nullCategory.columnIds.push(id);
        this.resultsData.forEach(d => {
            d.results.push({id, value: null, ref: null, overwritten: true});
        });
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
        this.updateResultsData(item, columnId);
        column.subItemIds.push(item.id);
        if (srcColumn) {
            this.gradeColumns = this.gradeColumns.filter(column => column !== srcColumn);
            this.allCategories.forEach(cat => {
                cat.columnIds = cat.columnIds.filter(id => id !== srcColumn.id);
            });
        }
    }

    private static replaceByNewResult(origResult: ResultValue, newResult: ResultValue): boolean {
        const origValue = origResult.value;
        const newValue = newResult.value;
        if (newValue === null) {
            return false;
        }
        if (origValue === null) {
            return true;
        }
        if (!origResult.overwritten && newResult.overwritten) {
            return true;
        }
        if (origResult.overwritten && !newResult.overwritten) {
            return false;
        }
        if (typeof newValue === 'number') {
            if (typeof origValue === 'number' && newValue > origValue) {
                return true;
            }
            if (origValue === 'afw' || origValue === 'gafw') {
                return true;
            }
        }
        if (newValue === 'gafw' && origValue === 'afw') {
            return true;
        }
        return false;
    }

    private updateResultsData(item: GradeItem, columnId: ItemId) {

        const srcColumn = this.findGradeColumnWithGradeItem(item.id);

        this.originalResultsData.forEach(d => {
            const value = d.results.find((r: any) => r.id === item.id)?.value || null;
            if (value !== null) {
                const studentResults = this.resultsData.find(data => data.id === d.id);
                if (!studentResults) { return; }
                let newResult;
                if (srcColumn) {
                    newResult = studentResults.results.find(r => r.id === srcColumn.id) || undefined;
                }
                if (newResult === undefined) {
                    newResult = { id: columnId, value, ref: item.id, overwritten: false };
                }
                const resultValue = studentResults.results.find(r => r.id === columnId);
                if (resultValue && GradeBook.replaceByNewResult(resultValue, newResult)) {
                    resultValue.value = newResult.value;
                    resultValue.ref = newResult.ref;
                    resultValue.overwritten = newResult.overwritten;
                }
                if (srcColumn) {
                    studentResults.results = studentResults.results.filter(r => r.id !== srcColumn.id);
                }
            }
        });
    }

    removeColumn(column: GradeColumn) {
        column.subItemIds.forEach(itemId => {
            this.removeSubItem(this.getGradeItem(itemId)!);
        });
        this.gradeColumns = this.gradeColumns.filter(col => col !== column);
        this.allCategories.forEach(cat => {
            cat.columnIds = cat.columnIds.filter(id => id !== column.id);
        });
        this.resultsData.forEach(d => {
            d.results = d.results.filter(r => r.id !== column.id);
        });
    }

    static from(gradeBookObject: any): GradeBook {

        function convertedResultsData(columns: GradeColumn[], data: any[]) : ResultsData[] {
            return data.map(resultsData => {
                const results: Results = [];
                columns.forEach(column => {
                    if (column.subItemIds.length) {
                        let value: ResultType = null;
                        let ref = null;
                        column.subItemIds.forEach(itemId => {
                            const v = resultsData.results.find((r: any) => r.id === itemId)?.value || null;
                            if (v !== null && (value === null
                            || (value === 'afw' && v === 'gafw')
                            || ((value === 'afw' || value === 'gafw') && typeof v === 'number'))
                            || (typeof v === 'number' && typeof value === 'number' && v > value)) {
                                value = v;
                                ref = itemId;
                            }
                        });
                        results.push({id: column.id, value, overwritten: false, ref});
                    }/* else {
                        results[column.id] = {value: resultsData.results[column.id], overwritten: false, ref: column.id};
                    }*/
                })
                return {...resultsData, results};
            });
        }

        const gradeBook = new GradeBook(gradeBookObject.dataId, gradeBookObject.version, gradeBookObject.title);
        gradeBook.gradeItems = gradeBookObject.gradeItems;
        gradeBook.gradeColumns = gradeBookObject.gradeColumns;
        gradeBook.categories = gradeBookObject.categories;
        gradeBook.nullCategory = gradeBookObject.nullCategory;
        gradeBook.originalResultsData = gradeBookObject.resultsData;
        gradeBook.resultsData = convertedResultsData(gradeBook.gradeColumns, gradeBookObject.resultsData);

        return gradeBook;
    }
}