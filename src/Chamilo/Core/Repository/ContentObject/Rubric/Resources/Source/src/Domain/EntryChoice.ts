import Level from './Level';
import Rubric from './Rubric';

export interface EntryChoice {
    readonly title: string;
    readonly description: string;
    readonly score: number;
    readonly markdown: string;
    readonly level: Level;
    readonly isSelected: boolean;
}

export class LevelEntryChoice implements EntryChoice {
    private rubric: Rubric;
    public readonly level: Level;
    private readonly chosenLevel: Level|null;

    constructor(rubric: Rubric, level: Level, chosenLevel: Level|null) {
        this.rubric = rubric;
        this.level = level;
        this.chosenLevel = chosenLevel;
    }

    get isSelected(): boolean {
        if (!this.chosenLevel) {
            return this.level.isDefault;
        }
        return this.level === this.chosenLevel;
    }

    get description(): string {
        return this.level.description;
    }

    get markdown(): string {
        return this.level.toMarkdown();
    }

    get score(): number {
        return this.level.score;
    }

    get title(): string {
        return this.level.title;
    }
}

export class ChoiceEntryChoice implements EntryChoice {
    private rubric: Rubric;
    private wChoice: any;
    private readonly chosenLevel: Level|null;

    constructor(rubric: Rubric, wChoice: any, chosenLevel: Level|null) {
        this.rubric = rubric;
        this.wChoice = wChoice;
        this.chosenLevel = chosenLevel;
    }

    get description(): string {
        return this.wChoice.choice.description;
    }

    get markdown(): string {
        return this.wChoice.choice.toMarkdown();
    }

    get score(): number {
        return this.rubric.useRelativeWeights ? this.wChoice.level.score : this.wChoice.score;
    }

    get title(): string {
        return this.wChoice.level.title;
    }

    get level(): Level {
        return this.wChoice.level;
    }

    get isSelected(): boolean {
        if (!this.chosenLevel) {
            return this.wChoice.level.isDefault;
        }

        return this.wChoice.level === this.chosenLevel;
    }
}