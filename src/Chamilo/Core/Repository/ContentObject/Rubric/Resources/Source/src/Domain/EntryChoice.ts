import Level from './Level';
import Choice from './Choice';

export interface EntryChoice {
    readonly item: Level|Choice;
    readonly level: Level;
    readonly title: string;
    readonly description: string;
    readonly score: number;
    readonly useRangeScore: boolean;
    readonly minimumScore: number|null;
    readonly markdown: string;
    readonly isSelected: boolean;
}

export class LevelEntryChoice implements EntryChoice {
    public readonly level: Level;
    private readonly chosenLevel: Level|null;

    constructor(level: Level, chosenLevel: Level|null) {
        this.level = level;
        this.chosenLevel = chosenLevel;
    }

    get item(): Level {
        return this.level;
    }

    get title(): string {
        return this.level.title;
    }

    get description(): string {
        return this.level.description;
    }

    get score(): number {
        return this.level.score;
    }

    get useRangeScore(): boolean {
        return this.level.useRangeScore;
    }

    get minimumScore(): number|null {
        return this.level.minimumScore;
    }

    get markdown(): string {
        return this.level.toMarkdown();
    }

    get isSelected(): boolean {
        return this.level === this.chosenLevel;
    }
}

export class ChoiceEntryChoice implements EntryChoice {
    private wChoice: any;
    private readonly chosenLevel: Level|null;
    private readonly useChoiceScore: boolean;

    constructor(wChoice: any, chosenLevel: Level|null, useChoiceScore: boolean) {
        this.wChoice = wChoice;
        this.chosenLevel = chosenLevel;
        this.useChoiceScore = useChoiceScore;
    }

    get item(): Choice {
        return this.wChoice.choice;
    }

    get level(): Level {
        return this.wChoice.level;
    }

    get title(): string {
        return this.level.title;
    }

    get description(): string {
        return this.wChoice.choice.description;
    }

    get score(): number {
        return this.useChoiceScore ? this.wChoice.score : this.level.score;
    }

    get useRangeScore(): boolean {
        return this.level.useRangeScore;
    }

    get minimumScore(): number|null {
        return this.level.minimumScore;
    }

    get markdown(): string {
        return this.wChoice.choice.toMarkdown();
    }

    get isSelected(): boolean {
        return this.level === this.chosenLevel;
    }
}