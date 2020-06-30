import Cluster, {ClusterJsonObject} from "./Cluster";
import Level, {LevelId, LevelJsonObject} from "./Level";
import Choice, {ChoiceJsonObject} from "./Choice";
import Criterium, {CriteriumId} from "./Criterium";
import TreeNode from "./TreeNode";
import Category from "./Category";

export interface RubricJsonObject {
    id: string,
    useScores: boolean,
    title: string,
    levels: LevelJsonObject[],
    clusters: ClusterJsonObject[],
    choices: ChoiceJsonObject[]
}

export default class Rubric extends TreeNode {
    public useScores: boolean = true;
    public levels: Level[] = [];
    public choices: Map<CriteriumId, Map<LevelId, Choice>> = new Map<CriteriumId, Map<LevelId, Choice>>();

    constructor(title: string = '', id?:string) {
        super(title, id);
        this.isRoot = true;
    }

    get clusters():Cluster[] {
        return this.children as Cluster[]; //invariant garded at addChild
    }

    addCluster(cluster: Cluster) {
        this.addChild(cluster);
    }

    removeCluster(cluster: Cluster) {
        this.removeChild(cluster);
    }

    protected notifyAddChild(treeNode: TreeNode): void {
        if(treeNode instanceof Criterium) {
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

    protected onCriteriumAdded(criterium:Criterium) {
        this.levels.forEach(level => {
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
                rubricChoiceJsonObject.criteriumId,
                rubricChoiceJsonObject.levelId,
                )
        });

        const clusters = rubricObject.clusters
            .map(clusterJsonObject => Cluster.fromJSON(clusterJsonObject)) as TreeNode[];
        clusters.forEach(cluster => cluster.parent = newRubric);

        // Note: Setting children directly loses the notifyAddChild behavior. So we have to perform the actions here.
        newRubric._children = clusters;
        newRubric.useScores = rubricObject.useScores;
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
        this.getAllCriteria().forEach(criterium => {
            this.addChoice(new Choice(false, ""), criterium.id, level.id)
        })
    }

    public removeLevel(level: Level) {
        const index = this.levels.indexOf(level);
        this.levels.splice(index, 1);
        this.removeChoicesByLevel(level);
    }

    public moveLevelDown(level: Level) {
        this.moveItemInArray(
            this.levels, this.levels.indexOf(level), this.levels.indexOf(level) + 1
        )
    }

    public moveLevelUp(level: Level) {
        this.moveItemInArray(
            this.levels, this.levels.indexOf(level), this.levels.indexOf(level) - 1
        )
    }

    protected moveItemInArray(array: any[], from: number, to: number) {
        if (to >= array.length || from >= array.length)
            return;
        if (to < 0 || from < 0)
            return;

        array.splice(to, 0, array.splice(from, 1)[0]);
    }

    public getChoiceScore(criterium: Criterium, level: Level){
        let choice = this.getChoice(criterium, level);
        if (choice.hasFixedScore)
            return choice.fixedScore;

        return Math.round(criterium.weight * level.score / 10) / 10;
    }

    public getScore() {
        return this._children
            .reduce((accumulator, currentTreeNode) => accumulator + currentTreeNode.getScore(), 0);
    }

    public getMaximumScore() : number {
        let maxScore = 0;
        this.getAllCriteria().forEach(criterium => {
            const levelScores = this.levels.map(level => this.getChoiceScore(criterium, level));
            const max = levelScores.reduce((curr, score) => Math.max(curr, score), 0);
            maxScore += max;
        });
        return maxScore;
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
        )
    }

}
