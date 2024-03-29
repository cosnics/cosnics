import Cluster, {ClusterJsonObject} from './Cluster';
import Level, {LevelId, LevelJsonObject} from './Level';
import Choice, {ChoiceJsonObject} from './Choice';
import Criterium, {CriteriumId} from './Criterium';
import TreeNode from './TreeNode';
import Category from './Category';

function add(v1: number, v2: number) {
    return v1 + v2;
}

function rounded2dec(v: number) {
    return Math.round(v * 100) / 100;
}

export interface RubricJsonObject {
    id: string,
    useScores: boolean,
    useRelativeWeights: boolean,
    title: string,
    levels: LevelJsonObject[],
    clusters: ClusterJsonObject[],
    choices: ChoiceJsonObject[]
}

export default class Rubric extends TreeNode {
    public useScores: boolean = true;
    public useRelativeWeights: boolean = true;
    public hasAbsoluteWeights: boolean = false;
    public levels: Level[] = [];
    public choices: Map<CriteriumId, Map<LevelId, Choice>> = new Map<CriteriumId, Map<LevelId, Choice>>();

    constructor(title: string = '', id?:string) {
        super(title, id);
        this.isRoot = true;
    }

    public getType(): string {
        return 'rubric';
    }

    get useWeights() {
        return this.useScores && (this.useRelativeWeights || this.hasAbsoluteWeights);
    }

    get rubricLevels(): Level[] {
        return this.levels.filter(level => !level.criteriumId);
    }

    get hasCustomLevels() {
        return this.levels.length !== this.rubricLevels.length;
    }

    get maxNumLevels(): number {
        let num = this.rubricLevels.length;
        if (!this.hasCustomLevels) { return num; }
        this.getAllCriteria().forEach(criterium => {
            const numCriteriumLevels = this.filterLevelsByCriterium(criterium).length;
            if (numCriteriumLevels > num) {
                num = numCriteriumLevels;
            }
        });
        return num;
    }

    public filterLevelsByCriterium(criterium: Criterium): Level[] {
        return this.levels.filter(level => level.criteriumId === criterium.id);
    }

    get clusters(): Cluster[] {
        return this.children as Cluster[]; //invariant garded at addChild
    }

    addCluster(cluster: Cluster) {
        this.addChild(cluster);
    }

    removeCluster(cluster: Cluster) {
        this.removeChild(cluster);
    }

    protected notifyAddChild(treeNode: TreeNode): void {
        if (treeNode instanceof Criterium) {
            this.onCriteriumAdded(treeNode);
        }
        else { //if for example a cluster is added, we add any choices from criteria in that cluster. This could happen when bootstrapping from json data model
            let addedCriteria = this.getAllCriteria(treeNode);
            addedCriteria.forEach(criterium => {
                this.onCriteriumAdded(criterium)
            });
        }
        //no more bubbling
    }

    protected onCriteriumAdded(criterium: Criterium) {
        if (this.filterLevelsByCriterium(criterium).length) { return; }

        this.rubricLevels.forEach(level => {
            //choice already exists for criterium? Could be through json bootstrapping.
            let choice = this.findChoice(criterium, level);
            if (!choice) {
                choice = new Choice(false, "");
            }
            this.addChoice(choice, criterium.id, level.id);
        });
    }

    protected notifyRemoveChild(parent: TreeNode, treeNode: TreeNode): void {
        const criteriaToBeRemoved = this.getAllCriteria(treeNode);
        criteriaToBeRemoved.forEach(criterium => this.removeChoicesByCriterium(criterium));
    }

    toJSON(): RubricJsonObject {
        return {
            id: this.id,
            useScores: this.useScores,
            useRelativeWeights: this.useRelativeWeights,
            title: this.title,
            levels: this.levels,
            clusters: this._children.map(cluster => (cluster as Cluster).toJSON()),
            choices: this.getChoicesJSON()
        }
    }

    protected getChoicesJSON(): ChoiceJsonObject[]{
        let choicesArray:ChoiceJsonObject[] = [];
        this.choices.forEach((levelMap, criteriumId) => {
            levelMap.forEach((choice, levelId) => {
                choicesArray.push(
                    choice.toJSON(criteriumId, levelId)
                )
            })
        } );
        return choicesArray;
    }

    static fromJSON(rubric:string|RubricJsonObject):Rubric {
        let rubricObject: RubricJsonObject;
        if(typeof rubric === 'string') {
            rubricObject = JSON.parse(rubric);
        } else {
            rubricObject = rubric;
        }

        const newRubric = new Rubric(rubricObject.title, rubricObject.id);

        newRubric.levels.push(...rubricObject.levels.map(level => Level.fromJSON(level)));

        rubricObject.choices.forEach(rubricChoiceJsonObject => {
            newRubric.addChoice(
                Choice.fromJSON(rubricChoiceJsonObject),
                rubricChoiceJsonObject.criterium_id,
                rubricChoiceJsonObject.level_id,
                )
        });

        const clusters = rubricObject.clusters
            .map(clusterJsonObject => Cluster.fromJSON(clusterJsonObject)) as TreeNode[];
        clusters.forEach(cluster => cluster.parent = newRubric);

        // Note: Setting children directly loses the notifyAddChild behavior. So we have to perform the actions here.
        newRubric._children = clusters;
        newRubric.useScores = rubricObject.useScores;
        newRubric.useRelativeWeights = rubricObject.useRelativeWeights;
        newRubric.getAllCriteria().forEach(criterium => newRubric.onCriteriumAdded(criterium));

        return newRubric;
    }

    protected addChoice(choice: Choice, criteriumId: CriteriumId, levelId: LevelId){
        let criteriumChoices = this.choices.get(criteriumId);
        if (criteriumChoices === undefined) {
            criteriumChoices = new Map<LevelId, Choice>();
            this.choices.set(criteriumId, criteriumChoices);
        }
        criteriumChoices.set(levelId, choice);
    }

    protected removeChoicesByCriterium(criterium: Criterium) {
        this.choices.delete(criterium.id);
    }

    public setChoicesCriteriumId(oldId: CriteriumId, newId: CriteriumId) {
        const criteriumChoices = this.choices.get(oldId);
        if (criteriumChoices) {
            this.choices.delete(oldId);
            this.choices.set(newId, criteriumChoices);
        }
    }

    public setChoicesLevelId(oldId: LevelId, newId: LevelId) {
        this.getAllCriteria().forEach(criterium => {
            const criteriumChoices = this.choices.get(criterium.id);
            if (criteriumChoices) {
                const choice = criteriumChoices.get(oldId);
                if (choice) {
                    criteriumChoices.delete(oldId);
                    criteriumChoices.set(newId, choice);
                }
            }
        });
    }

    protected removeChoicesByLevel(level: Level) {
        Array.from(this.choices.values()).forEach(levelChoices => levelChoices.delete(level.id))
    }

    protected findChoice(criterium: Criterium, level: Level):Choice|undefined {
        let criteriumChoices = this.choices.get(criterium.id);
        if (criteriumChoices === undefined) {
            return undefined;
        }

        return criteriumChoices.get(level.id);
    }

    /**
     * Invariant: to the outside world a choice is always available for a criterium and level of the rubric.
     * @param criterium
     * @param level
     */
    public getChoice(criterium: Criterium, level: Level):Choice {
        let choice = this.findChoice(criterium, level);
        if(!choice) {
            throw new Error(`No choice found for criteria: ${criterium} and level: ${level}`);
        }
        return choice;
    }

    public addLevel(level: Level) {
        this.levels.push(level);
        if (level.criteriumId) {
            this.choices.delete(level.criteriumId);
            return;
        }
        this.getAllCriteria().forEach(criterium => {
            if (!this.filterLevelsByCriterium(criterium).length) {
                this.addChoice(new Choice(false, ""), criterium.id, level.id);
            }
        });
    }

    public removeLevel(level: Level) {
        let criterium: Criterium|undefined;
        if (level.criteriumId) {
            criterium = this.getAllCriteria().find(criterium => criterium.id === level.criteriumId);
        }
        const index = this.levels.indexOf(level);
        this.levels.splice(index, 1);
        this.removeChoicesByLevel(level);
        if (criterium && !this.filterLevelsByCriterium(criterium).length) {
            const criteriumId = criterium.id;
            this.rubricLevels.forEach(level => {
                this.addChoice(new Choice(false, ""), criteriumId, level.id);
            })
        }
    }

    public getFilteredLevels(level: Level) {
        if (level.criteriumId) {
            const criterium = this.getAllCriteria().find(c => c.id === level.criteriumId);
            if (!criterium) { return null; }
            return this.filterLevelsByCriterium(criterium);
        }
        return this.rubricLevels;
    }

    public moveLevelDown(level: Level) {
        const levels = this.getFilteredLevels(level);
        if (!levels) { return; }
        const levelIndex = levels.indexOf(level);
        const nextLevel = levels[levelIndex + 1];
        if (!nextLevel) { return; }

        this.moveItemInArray(
            this.levels, this.levels.indexOf(level), this.levels.indexOf(nextLevel)
        )
    }

    public moveLevelUp(level: Level) {
        const levels = this.getFilteredLevels(level);
        if (!levels) { return; }
        const levelIndex = levels.indexOf(level);
        const nextLevel = levels[levelIndex - 1];
        if (!nextLevel) { return; }

        this.moveItemInArray(
            this.levels, this.levels.indexOf(level), this.levels.indexOf(nextLevel)
        )
    }

    protected moveItemInArray(array: any[], from: number, to: number) {
        if (to >= array.length || from >= array.length)
            return;
        if (to < 0 || from < 0)
            return;

        array.splice(to, 0, array.splice(from, 1)[0]);
    }

    public getChoiceScore(criterium: Criterium, level: Level) {
        let choice = this.getChoice(criterium, level);
        if (choice.hasFixedScore)
            return choice.fixedScore;
        return Math.round(criterium.weight * level.score) / 100;
    }

    public getScore() {
        return this._children
            .reduce((accumulator, currentTreeNode) => accumulator + currentTreeNode.getScore(), 0);
    }

    public getMaximumScore() : number {
        if (this.useRelativeWeights) { return 100; }
        let maxScore = 0;
        this.getAllCriteria().forEach(criterium => {
            const filteredLevels = this.filterLevelsByCriterium(criterium);
            let levelScores;
            if (filteredLevels.length) {
                levelScores = filteredLevels.map(level => level.score);
            } else {
                levelScores = this.rubricLevels.map(level => this.getChoiceScore(criterium, level));
            }
            const max = levelScores.reduce((curr, score) => Math.max(curr, score), 0);
            maxScore += max;
        });
        return maxScore;
    }

    public getCriteriumMaxScore(criterium: Criterium, precise = false) : number {
        if (this.useRelativeWeights) {
            return criterium.rel_weight !== null ? criterium.rel_weight : (precise ? this.eqRestWeightPrecise : this.eqRestWeight);
        }
        const scores : number[] = [0];
        const filteredLevels = this.filterLevelsByCriterium(criterium);
        if (filteredLevels.length) {
            filteredLevels.forEach(level => {
                scores.push(level.score);
            });
        } else {
            const criteriumChoices = this.choices.get(criterium.id);
            if (!criteriumChoices) {
                throw new Error(`No choice data found for: ${criterium}`);
            }
            criteriumChoices.forEach((choice, levelId) => {
                const level = this.rubricLevels.find(level => level.id === levelId);
                scores.push(this.getChoiceScore(criterium, level!));
            })
        }
        return Math.max.apply(null, scores);
    }

    public getCategoryMaxScore(category: Category) : number {
        const score = this.getAllCriteria(category).map(criterium => this.getCriteriumMaxScore(criterium, true)).reduce(add, 0);
        return rounded2dec(score);
    }

    public getClusterMaxScore(cluster: Cluster) : number {
        const score = this.getAllCriteria(cluster).map(criterium => this.getCriteriumMaxScore(criterium, true)).reduce(add, 0);
        return rounded2dec(score);
    }

    public getAllTreeNodes(treeNode: TreeNode = this) {
        const nodes: TreeNode[] = [];
        this.getChildrenRecursive(treeNode, nodes);
        return nodes;
    }

    protected getChildrenRecursive(treeNode: TreeNode, nodes: TreeNode[]) {
        nodes.push(treeNode);

        treeNode.children.filter(child => (child instanceof Criterium)).forEach(
            criterium => nodes.push(criterium)
        );

        treeNode.children.filter(child => child.hasChildren()).forEach(
            child => this.getChildrenRecursive(child, nodes)
        );
    }

    public getAllCriteria(treeNode: TreeNode = this) {
        const criteria: Criterium[] = [];
        this.getCriteriaRecursive(treeNode, criteria);

        return criteria;
    }

    public getAllCategories(treeNode: TreeNode = this) {
        const categories: Category[] = [];
        this.children.filter(child => (child instanceof Category)).forEach(
            category => categories.push(category as Category)
        );
        this.children.filter(child => (child instanceof Cluster)).forEach(
            cluster => categories.push(...(cluster as Cluster).categories)
        );
        return categories;
    }

    protected getCriteriaRecursive(treeNode: TreeNode, criteria: Criterium[]) {
        treeNode.children.filter(child => (child instanceof Criterium)).forEach(
            criterium => criteria.push(criterium as Criterium)
        );

        treeNode.children.filter(child => child.hasChildren()).forEach(
            child => this.getCriteriaRecursive(child, criteria)
        );
    }

    get eqRestWeightPrecise() {
        // n => number of criteria without an explicitly set relative weight (only relevant when useRelativeWeights === true)
        const n = this.getAllCriteria().filter(criterium => criterium.rel_weight === null).length;
        if (!n) { return 0; }
        const sum = this.getAllCriteria().map(criterium => criterium.rel_weight || 0).reduce(add, 0);
        return (100 - sum) / n;
    }

    get eqRestWeight() {
        return rounded2dec(this.eqRestWeightPrecise);
    }

    getCriteriumWeight(criterium: Criterium) {
        if (this.useRelativeWeights) {
            if (criterium.rel_weight !== null) {
                return criterium.rel_weight;
            }
            return this.eqRestWeightPrecise;
        }
        return criterium.weight;
    }

    getRelativeWeight(treeNode: TreeNode) {
        if (!this.useRelativeWeights) { return 0; }
        if (treeNode instanceof Criterium) {
            return this.getCriteriumWeight(treeNode);
        }
        return this.getAllCriteria(treeNode).map(criterium => this.getCriteriumWeight(criterium)).reduce(add, 0);
    }


    /*public getMaxDecimals() : number {
        let maxDecimals = 0;
        if (this.useScores && !this.useRelativeWeights) {
            this.getAllCriteria().forEach(criterium => {
                this.levels.forEach(level => {
                    const score = this.getChoiceScore(criterium, level);
                    const intScore = parseInt(score as any);
                    if (intScore !== score) {
                        const decimals = (score - intScore).toLocaleString('en-us').length - 2;
                        if (decimals > maxDecimals) {
                            maxDecimals = decimals;
                        }
                    }
                });
            });
        }
        return maxDecimals;
    }*/

    static resetAbsoluteWeights(rubric: Rubric) {
        const criteria = rubric.getAllCriteria();
        const levels = rubric.levels;
        for (let i = 0; i < criteria.length; i++) {
            if (criteria[i].weight !== 100) {
                criteria[i].weight = 100;
            }
            for (let j = 0; j < levels.length; j++) {
                const choice = rubric.findChoice(criteria[i], levels[j]);
                if (choice?.hasFixedScore) {
                    choice.fixedScore = 0;
                    choice.hasFixedScore = false;
                }
            }
        }
        rubric.hasAbsoluteWeights = false;
    }

    static usesAbsoluteWeights(rubric: Rubric) {
        const criteria = rubric.getAllCriteria();
        const levels = rubric.levels;
        for (let i = 0; i < criteria.length; i++) {
            if (criteria[i].weight !== 100) {
                return true;
            }
            for (let j = 0; j < levels.length; j++) {
                const choice = rubric.findChoice(criteria[i], levels[j]);
                if (choice?.hasFixedScore) {
                    return true;
                }
            }
        }
        return false;
    }
}
