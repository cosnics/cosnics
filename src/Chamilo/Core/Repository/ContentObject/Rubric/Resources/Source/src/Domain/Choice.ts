import {toMarkdown} from '../Util/util';

export interface ChoiceJsonObject {
    selected: boolean,
    feedback: string,
    has_fixed_score: boolean,
    fixed_score: number,
    criterium_id: string,
    level_id: string
}

export default class Choice {
    public selected: boolean;
    public feedback: string;
    public hasFixedScore: boolean = false;
    private fixedScore_: number = 10;
    public static readonly FIXED_SCORE = 10;

    constructor(selected: boolean = false, feedback: string = '') {
        this.selected = selected;
        this.feedback = feedback;
    }

    // @ts-ignore
    get fixedScore() : number {
        return this.fixedScore_;
    }

    // @ts-ignore
    set fixedScore(v: number|string) {
        if (typeof v === 'number') {
            this.fixedScore_ = v;
        } else {
            this.fixedScore_ = parseFloat(v);
        }
    }

    toJSON(criteriumId: string, levelId: string): ChoiceJsonObject {
        return {
            selected: this.selected,
            feedback: this.feedback,
            has_fixed_score: this.hasFixedScore,
            fixed_score: this.fixedScore,
            criterium_id: criteriumId,
            level_id: levelId
        }
    }

    toMarkdown(): string {
        return toMarkdown(this.feedback);
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
        newChoice.hasFixedScore = choiceObject.has_fixed_score;
        newChoice.fixedScore = choiceObject.fixed_score;

        return newChoice;
    }}
