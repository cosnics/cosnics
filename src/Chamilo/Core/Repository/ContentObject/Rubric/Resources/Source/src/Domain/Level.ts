import {CriteriumId} from './Criterium';
import {toMarkdown} from '../Util/util';

export type LevelId = string;

export interface LevelJsonObject {
    id: string,
    title: string,
    description: string,
    score: number,
    is_default: boolean,
    criterium_id: string
}

export default class Level {
    public id: LevelId;
    public title: string;
    public description: string;
    public score: number;
    public isDefault: boolean;
    public criteriumId: CriteriumId = '';

    constructor(title: string, description: string = '', score: number = 10, isDefault: boolean = false, id?: LevelId) {
        this.title = title;
        this.description = description;
        this.score = score;
        this.isDefault = isDefault;
        if (!id) {
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); // GUID
        } else {
            this.id = id;
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
            levelObject.is_default,
            levelObject.id
        );
        newLevel.criteriumId = levelObject.criterium_id;
        return newLevel;
    }
}
