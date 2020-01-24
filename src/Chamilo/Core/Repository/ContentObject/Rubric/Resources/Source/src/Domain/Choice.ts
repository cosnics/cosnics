export interface ChoiceJsonObject {
    selected: boolean,
    feedback: string,
    hasFixedScore: boolean,
    fixedScore: number,
    criteriumId: string,
    levelId: string
}

export default class Choice {
    public selected: boolean;
    public feedback: string;
    public hasFixedScore: boolean = false;
    public fixedScore: number = 10;

    constructor(selected: boolean = false, feedback: string = '') {
        this.selected = selected;
        this.feedback = feedback;
    }

    toJSON(criteriumId: string, levelId: string): ChoiceJsonObject {
        return {
            selected: this.selected,
            feedback: this.feedback,
            hasFixedScore: this.hasFixedScore,
            fixedScore: this.fixedScore,
            criteriumId: criteriumId,
            levelId: levelId
        }
    }

    static fromJSON(choice:string|ChoiceJsonObject):Choice {
        let choiceObject: ChoiceJsonObject;
        if (typeof choice === 'string') {
            choiceObject = JSON.parse(choice);
        } else {
            choiceObject = choice;
        }

        let newChoice = new Choice(
            choiceObject.selected,
            choiceObject.feedback
        );
        newChoice.hasFixedScore = choiceObject.hasFixedScore;
        newChoice.fixedScore = choiceObject.fixedScore;

        return newChoice;
    }}
