export type ItemId = string|number;
export type ResultType = number|'gafw'|'afw'|null;

export interface GradeItem {
    readonly id: ItemId;
    readonly name: string;
    readonly breadcrumb: string;
    checked?: boolean;
    disabled?: boolean;
}

export interface GradeColumn {
    readonly id: ItemId;
    readonly type: string;
    name?: string|null;
    subItemIds?: ItemId[];
    weight: number|null;
    countForEndResult: boolean;
    authPresenceEndResult: number;
    unauthPresenceEndResult: number;
}

export interface ResultValue {
    value: ResultType;
    ref: ItemId|null;
    overwritten: boolean;
}

export type Results = Record<string, ResultValue>;

export interface ResultsData {
    readonly id: number;
    readonly student: string;
    readonly results: Results;
}

export interface Category {
    readonly id: number;
    color: string;
    name: string;
    itemIds: ItemId[];
}

const COUNT_FOR_END_RESULT_DEFAULT = true;
const AUTH_PRESENCE_END_RESULT_DEFAULT = 0;
const UNAUTH_PRESENCE_END_RESULT_DEFAULT = 2;

export default class GradeBook {
    public gradeItems: GradeItem[] = [];
    public gradeColumns: GradeColumn[] = [];
    public categories: Category[] = [];
    public nullCategory: Category = {id: 0, name: '', color: '', itemIds: []};
    public originalResultsData: any[] = [];
    public resultsData: ResultsData[] = [];

    get allCategories(): Category[] {
        return [...this.categories, this.nullCategory];
    }

    getGradeItem(itemId: ItemId) {
        return this.gradeItems.find(item => item.id === itemId);
    }

    getGradeColumn(itemId: ItemId) {
        return this.gradeColumns.find(item => item.id === itemId);
    }

    getCategory(categoryId: number) {
        return this.allCategories.find(category => category.id === categoryId);
    }

    get gradedItemsWithStatusAdded(): GradeItem[] {
        const itemIds: ItemId[] = [];
        this.gradeColumns.forEach(item => {
            if (item.subItemIds) {
                // eslint-disable-next-line prefer-spread
                itemIds.push.apply(itemIds, item.subItemIds);
            }
        })

        this.allCategories.forEach(cat => {
            // eslint-disable-next-line prefer-spread
            itemIds.push.apply(itemIds, cat.itemIds);
        });

        return this.gradeItems.map(item => ({
            id: item.id, name: item.name, breadcrumb: item.breadcrumb, checked: itemIds.indexOf(item.id) !== -1
        }));
    }

    getGradedItemsFilteredByItem(itemId: ItemId): GradeItem[] {
        return this.gradeItems.map(item => {
            const checked = this.getGroupItems(itemId).indexOf(item) !== -1;
            let disabled = false;
            if (item.id === itemId && this.getGradeColumn(itemId)?.type !== 'group') {
                disabled = true;
            } else {
                const groupItems = this.gradeColumns.filter(item => item.type === 'group' && item.id !== itemId);
                groupItems.forEach(groupItem => {
                    if (groupItem.subItemIds!.indexOf(item.id) !== -1) {
                        disabled = true;
                    }
                });
            }
            return {...item, checked, disabled};
        })
    }

    countsForEndResult(itemId: ItemId): boolean {
        return !!(this.getGradeColumn(itemId)?.countForEndResult);
    }

    getWeight(itemId: ItemId): number {
        const col = this.getGradeColumn(itemId);
        const weight = col ? col.weight : null;
        if (weight === null) {
            let rest = 100;
            let noRest = 0;
            this.gradeColumns.filter(item => item.countForEndResult).forEach(item => {
                if (item.weight !== null) {
                    rest -= item.weight;
                } else {
                    noRest += 1;
                }
            });
            return rest / noRest;
        }
        return weight;
    }

    setWeight(itemId: ItemId, weight: number|null) {
        const item = this.getGradeColumn(itemId);
        if (item) {
            item.weight = weight;
        }
    }

    getName(itemId: ItemId): string|null {
        return this.getGradeColumn(itemId)?.name || this.getGradeItem(itemId)?.name || null;
    }

    setName(itemId: ItemId, name: string) {
        const item = this.getGradeColumn(itemId);
        if (item) {
            item.name = name;
        }
    }

    isGrouped(itemId: ItemId) {
        return this.getGradeColumn(itemId)?.type === 'group';
    }

    getGroupItems(itemId: ItemId) {
        if (!this.isGrouped(itemId)) {
            return this.gradeItems.filter(item => item.id === itemId);
        }
        const itemIds = this.getGradeColumn(itemId)?.subItemIds || [];
        return itemIds.map(itemId => this.getGradeItem(itemId));
    }

    getResult(results: Results, itemId: ItemId): ResultType {
        if (typeof results[itemId] === 'undefined') { return null; }
        return results[itemId].value;
    }

    getEndResult(studentId: number) {
        const r = this.resultsData.find(res => res.id === studentId);
        if (!r) { return 0; }
        const results = r.results;
        let endResult = 0;
        let maxWeight = 0;
        this.gradeColumns.filter(item => item.countForEndResult).forEach(item => {
            let result = this.getResult(results, item.id);
            if (result === null) {
                result = 'afw';
            }
            const weight = this.getWeight(item.id);
            if (typeof result === 'number') {
                maxWeight += weight;
            } else if (result === 'gafw') {
                if (item.authPresenceEndResult > 0) {
                    maxWeight += weight;
                    if (item.authPresenceEndResult === 1) {
                        endResult += (weight * 0.01);
                    }
                }
            } else if (result === 'afw') {
                if (item.unauthPresenceEndResult > 0) {
                    maxWeight += weight;
                    if (item.unauthPresenceEndResult === 1) {
                        endResult += (weight * 0.01);
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

    addItemToCategory(categoryId: number, itemId: ItemId) {
        const category = categoryId === 0 ? this.nullCategory : this.getCategory(categoryId);
        if (category?.itemIds.indexOf(itemId) === -1) {
            this.allCategories.forEach(cat => {
                if (cat.itemIds.indexOf(itemId) !== -1) {
                    cat.itemIds = cat.itemIds.filter(id => id !== itemId);
                }
            });
            category.itemIds.push(itemId);
        }
    }

    addGradeItem(item: GradeItem) {
        this.addItemToCategory(0, item.id);
        this.gradeColumns.push({
            id: item.id, type: 'item', name: null, weight: null,
            countForEndResult: COUNT_FOR_END_RESULT_DEFAULT,
            authPresenceEndResult: AUTH_PRESENCE_END_RESULT_DEFAULT,
            unauthPresenceEndResult: UNAUTH_PRESENCE_END_RESULT_DEFAULT
        });
        this.originalResultsData.forEach(data => {
            const r = this.resultsData.find(d => d.id === data.id);
            if (r) {
                r.results[item.id] = { value: data.results[item.id], ref: item.id, overwritten: false };
            }
        });
    }

    removeGradeItem(item: GradeItem) {
        this.allCategories.forEach(cat => {
            if (cat.itemIds.indexOf(item.id) !== -1) {
                cat.itemIds = cat.itemIds.filter(id => id !== item.id);
            }
        });
        const itemId = item.id;
        if (this.getGradeColumn(itemId)) {
            this.gradeColumns = this.gradeColumns.filter(item => item.id !== itemId);
        }
        this.gradeColumns.forEach(item => {
            if (item.subItemIds?.length) {
                item.subItemIds = item.subItemIds.filter(id => id !== itemId);
            }
        });
        this.resultsData.forEach(d => {
            Object.keys(d.results).forEach(id => {
                if (id === item.id) {
                    delete d.results[item.id];
                } else if (d.results[id].ref === item.id) {
                    d.results[id].value = null;
                    d.results[id].ref = null;
                    d.results[id].overwritten = false;
                }
            })
        });
    }

    toggleGradeItem(item: GradeItem, isAdding: boolean) {
        if (isAdding) {
            this.addGradeItem(item);
        } else {
            this.removeGradeItem(item);
        }
    }

    createNewIdWithPrefix(prefix: string): string {
        const itemIds = this.gradeItems.map(item => item.id);
        // eslint-disable-next-line prefer-spread
        itemIds.push.apply(itemIds, this.gradeColumns.map(item => item.id));

        let i = 1;
        while (itemIds.indexOf(prefix + i) !== -1) {
            i += 1;
        }
        return prefix + i;
    }

    createNewGroupId() {
        return this.createNewIdWithPrefix('gr');
    }

    createNewStandaloneScoreId() {
        return this.createNewIdWithPrefix('sc');
    }

    addSubItem(item: GradeItem, itemId: ItemId): ItemId|null {
        if (this.isGrouped(itemId)) {
            // group exists, push item
            this.gradeColumns = this.gradeColumns.filter(it => it.id !== item.id);
            this.getGradeColumn(itemId)?.subItemIds?.push(item.id);
            this.updateResultsData(item, itemId);
            this.allCategories.forEach(cat => {
                cat.itemIds = cat.itemIds.filter(id => id !== item.id);
            });
            return null;
        } else {
            // create a new group
            const groupItemId = this.createNewGroupId();
            const oldItem = this.getGradeColumn(itemId);
            this.gradeColumns = this.gradeColumns.filter(it => it.id !== item.id).filter(item => item.id !== itemId);
            this.gradeColumns.push({
                id: groupItemId, type: 'group', name: this.getGradeItem(itemId)?.name, subItemIds: [itemId, item.id], weight: null,
                countForEndResult: oldItem?.countForEndResult!,
                authPresenceEndResult: oldItem?.authPresenceEndResult!,
                unauthPresenceEndResult: oldItem?.unauthPresenceEndResult!
            });
            this.resultsData.forEach(d => {
                d.results[groupItemId] = d.results[itemId];
                delete d.results[itemId];
            });
            this.updateResultsData(item, groupItemId);
            this.allCategories.forEach(cat => {
                const index = cat.itemIds.indexOf(itemId);
                if (index !== -1) {
                    cat.itemIds[index] = groupItemId;
                }
                cat.itemIds = cat.itemIds.filter(id => id !== item.id);
            });
            return groupItemId;
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

    private updateResultsData(item: GradeItem, itemId: ItemId) {

        this.originalResultsData.forEach(d => {
            if (d.results[item.id] !== null) {
                const studentResults = this.resultsData.find(data => data.id === d.id);
                if (!studentResults) { return; }
                let newResult = studentResults.results[item.id];
                if (newResult === undefined) {
                    newResult = { value: d.results[item.id], ref: item.id, overwritten: false };
                }
                if (GradeBook.replaceByNewResult(studentResults.results[itemId], newResult)) {
                    studentResults.results[itemId] = newResult;
                }
                if (studentResults.results[item.id] !== undefined) {
                    delete studentResults.results[item.id];
                }
            }
        });
    }

    removeColumn(column: GradeColumn) {
        if (column.type === 'item') {
            this.removeGradeItem(this.getGradeItem(column.id)!);
        } else {
            column.subItemIds?.forEach(itemId => {
                this.removeGradeItem(this.getGradeItem(itemId)!);
            });
            this.gradeColumns = this.gradeColumns.filter(item => item !== column);
            this.allCategories.forEach(cat => {
                cat.itemIds = cat.itemIds.filter(itemId => itemId !== column.id);
            });
            this.resultsData.forEach(d => {
                Object.keys(d.results).forEach(id => {
                    if (id === column.id) {
                        delete d.results[column.id];
                    }
                });
            });
        }
    }

    static from(gradeBookObject: any): GradeBook {

        function convertedResultsData(columns: GradeColumn[], data: any[]) : ResultsData[] {
            return data.map(resultsData => {
                const results: Results = {};
                columns.forEach(item => {
                    if (item.subItemIds?.length) {
                        let value: ResultType = null;
                        let ref = null;
                        item.subItemIds.forEach(itemId => {
                            const v = resultsData.results[itemId];
                            if (v !== null && (value === null
                            || (value === 'afw' && v === 'gafw')
                            || ((value === 'afw' || value === 'gafw') && typeof v === 'number'))
                            || (typeof v === 'number' && typeof value === 'number' && v > value)) {
                                value = v;
                                ref = itemId;
                            }
                        });
                        results[item.id] = {value, overwritten: false, ref};
                    } else {
                        results[item.id] = {value: resultsData.results[item.id], overwritten: false, ref: item.id};
                    }
                })
                return {...resultsData, results};
            });
        }

        const gradeBook = new GradeBook();
        gradeBook.gradeItems = gradeBookObject.gradeItems;
        gradeBook.gradeColumns = gradeBookObject.gradeColumns;
        gradeBook.categories = gradeBookObject.categories;
        gradeBook.nullCategory = gradeBookObject.nullCategory;
        gradeBook.originalResultsData = gradeBookObject.resultsData;
        gradeBook.resultsData = convertedResultsData(gradeBook.gradeColumns, gradeBookObject.resultsData);
        return gradeBook;
    }
}