import {CriteriumId} from './Criterium';
import {toMarkdown} from '../Util/util';

export type LevelId = string;

export interface LevelJsonObject {
    id: string,
    title: string,
    description: string,
    score: number,
    use_range_score: boolean,
    minimum_score: number|null,
    is_default: boolean,
    criterium_id: string
}

export default class Level {
    public id: LevelId;
    public title: string;
    public description: string;
    public score: number;
    public useRangeScore: boolean;
    public minimumScore: number|null;
    public isDefault: boolean;
    public criteriumId: CriteriumId = '';

    constructor(title: string, description: string = '', score: number = 10, useRangeScore: boolean = false, minimumScore: number|null = null, isDefault: boolean = false, id?: LevelId) {
        this.title = title;
        this.description = description;
        this.score = score;
        this.useRangeScore = useRangeScore;
        this.minimumScore = minimumScore;
        this.isDefault = isDefault;
        if (!id) {
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); // GUID
        } else {
            this.id = id;
        }
    }

    get use_range_score() {
        return this.useRangeScore;
    }

    set use_range_score(useRangeScore: boolean) {
        this.useRangeScore = useRangeScore;
        if (!useRangeScore) {
            this.minimumScore = null;
        } else {
            this.minimumScore = 0;
        }
    }

    get minimum_score() {
        return this.minimumScore;
    }

    set minimum_score(score: number|null) {
        if (typeof score === 'number') {
            this.minimumScore = score;
        } else {
            this.minimumScore = null;
        }
    }

    get is_default() {
        return this.isDefault;
    }

    get criterium_id() {
        return this.criteriumId;
    }

    public toString(): string {
        return `Level (id: ${this.id}, title: ${this.title})`;
    }

    toJSON(): LevelJsonObject {
        return {
            id: this.id,
            title: this.title,
            description: this.description,
            score: this.score,
            use_range_score: this.useRangeScore,
            minimum_score: this.minimumScore,
            is_default: this.isDefault,
            criterium_id: this.criteriumId
        }
    }

    toMarkdown(): string {
        return toMarkdown(this.description);
    }

    static fromJSON(level: string|LevelJsonObject): Level {
        let levelObject: LevelJsonObject;

        if (typeof level === 'string') {
            levelObject = JSON.parse(level);
        } else {
            levelObject = level;
        }

        const newLevel = new Level(
            levelObject.title,
            levelObject.description,
            levelObject.score,
            levelObject.use_range_score || false,
            typeof levelObject.minimum_score === 'number' ? levelObject.minimum_score : null,
            levelObject.is_default,
            levelObject.id
        );
        newLevel.criteriumId = levelObject.criterium_id;
        return newLevel;
    }
}
