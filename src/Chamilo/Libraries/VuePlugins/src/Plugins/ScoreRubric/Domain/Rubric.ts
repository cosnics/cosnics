import Cluster, {ClusterJsonObject} from "@/Plugins/ScoreRubric/Domain/Cluster";
import Level, {LevelId, LevelJsonObject} from "@/Plugins/ScoreRubric/Domain/Level";
import Choice, {ChoiceJsonObject} from "@/Plugins/ScoreRubric/Domain/Choice";
import Criterium, {CriteriumId} from "@/Plugins/ScoreRubric/Domain/Criterium";
import Container, {ContainerInterface} from "@/Plugins/ScoreRubric/Domain/Container";

export interface Element {
    parent: Container | null;
    title: string;

    getScore(): number;
}

export function isElement(object: any): object is Element {
    return 'parent' in object && 'title' in object;
}


export function isContainer(object: any): object is ContainerInterface {
    return 'children' in object && isElement(object);
}

export interface RubricChoiceJsonObject {
    "criteriumId": string,
    "levelId": string,
    "choice": ChoiceJsonObject
}

export interface RubricJsonObject {
    useScores: boolean,
    title: string,
    levels: LevelJsonObject[],
    clusters: ClusterJsonObject[],
    choices: RubricChoiceJsonObject[]
}

export default class Rubric extends Container {
    public useScores: boolean = true;
    public levels: Level[] = [];
    public choices: Map<CriteriumId, Map<LevelId, Choice>> = new Map<CriteriumId, Map<LevelId, Choice>>();

    get clusters():Cluster[] {
        return this.children as Cluster[]; //invariant garded at addChild
    }

    addCluster(cluster: Cluster) {
        this.addChild(cluster);
    }

    removeCluster(cluster: Cluster) {
        this.removeChild(cluster);
    }

    protected addChild(element: Element): void {
        super.addChild(element);
        this.notifyAddChild(element);
    }

    protected notifyAddChild(element: Element): void {
        if(element instanceof Criterium) {
            this.onCriteriumAdded(element);
        }

        else if(element instanceof Container) {
            let addedCriteria = this.getAllCriteria(element);
            addedCriteria.forEach(criterium => {
                this.levels.forEach(level =>
                {
                    let choice = this.findChoice(criterium, level);
                    if(!choice)
                        choice = new Choice(false, "");
                    this.addChoice(choice, criterium.id, level.id);
                })
            });
        }
        //no more bubbling
    }

    protected onCriteriumAdded(criterium:Criterium) {
        this.levels.forEach(level => {
            //choice already exists for criterium? Could be through json bootstrapping.
            let choice = this.findChoice(criterium, level);
            if(!choice)
                choice = new Choice(false, "");
            this.addChoice(choice, criterium.id, level.id);
        });
    }

    protected notifyRemoveChild(container: Container, element: Element): void {
        let criteriaToBeRemoved = this.getAllCriteria(container);
        criteriaToBeRemoved.forEach(criterium => this.removeChoicesByCriterium(criterium));
    }

    toJSON(): RubricJsonObject {
        return {
            useScores: this.useScores,
            title: this.title,
            levels: this.levels,
            clusters: this._children.map(cluster => (cluster as Cluster).toJSON()),
            choices: this.getChoicesJSON()
        }
    }

    protected getChoicesJSON(): RubricChoiceJsonObject[]{
        let choicesArray:RubricChoiceJsonObject[] = [];
        this.choices.forEach((levelMap, criteriumId) => {
            levelMap.forEach((choice, levelId) => {
                choicesArray.push(
                    {
                        "criteriumId": criteriumId,
                        "levelId": levelId,
                        "choice": choice.toJSON()
                    }
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

        let newRubric = new Rubric(rubricObject.title);
        newRubric.levels.push(...rubricObject.levels.map(level => Level.fromJSON(level)));

        rubricObject.choices.forEach(rubricChoiceJsonObject => {
            newRubric.addChoice(
                Choice.fromJSON(rubricChoiceJsonObject.choice),
                rubricChoiceJsonObject.criteriumId,
                rubricChoiceJsonObject.levelId,
                )
        });

        rubricObject.clusters
            .map(clusterJsonObject => Cluster.fromJSON(clusterJsonObject))
            .forEach(cluster => newRubric.addChild(cluster));

        newRubric.useScores = rubricObject.useScores;

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
        if(choice.hasFixedScore)
            return choice.fixedScore;

        return Math.round(criterium.weight * level.score) / 100;
    }

    public getScore() {
        return this._children
            .reduce((accumulator, currentContainer) => accumulator + currentContainer.getScore(), 0);
    }

    public getAllCriteria(container: Container = this) {
        const criteria: Criterium[] = [];
        this.getCriteriaRecursive(container, criteria);

        return criteria;
    }

    protected getCriteriaRecursive(container: Container, criteria: Criterium[]) {
        container.children.filter(child => (child instanceof Criterium)).forEach(
            criterium => criteria.push(criterium as Criterium)
        );

        container.children.filter(child => isContainer(child)).forEach(
            childContainer => this.getCriteriaRecursive(childContainer as Container, criteria)
        )
    }

}
