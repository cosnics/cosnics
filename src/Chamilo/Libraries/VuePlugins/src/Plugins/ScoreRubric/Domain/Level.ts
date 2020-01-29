import {RubricJsonObject} from "@/Plugins/ScoreRubric/Domain/Rubric";

export enum Signal {
    'GREEN',
    'ORANGE',
    'RED',
}

export type LevelId = string;

export interface LevelJsonObject {
    id: string,
    title: string,
    description: string,
    score: number,
    isDefault: boolean
}
export default class Level {
    public id: LevelId;
    public title: string;
    public description: string;
    public score: number;
    public signal: Signal;
    public isDefault: boolean;

    constructor(title: string, description: string = '', score: number = 10, signal: Signal = Signal.GREEN, isDefault: boolean = false, id?:LevelId) {
        this.title = title;
        this.description = description;
        this.score = score;
        this.signal = signal;
        this.isDefault = isDefault;
        if(!id)
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); // GUID
        else
            this.id = id;
    }

    public toString(): string {
        return `Level (id: ${this.id}, title: ${this.title})`;
    }

    toJSON():LevelJsonObject {
        return {
            id: this.id,
            title: this.title,
            description: this.description,
            score: this.score,
            isDefault: this.isDefault
        }
    }

    static fromJSON(level:string|LevelJsonObject):Level {
        let levelObject: LevelJsonObject;
        if(typeof level === 'string') {
            levelObject = JSON.parse(level);
        } else {
            levelObject = level;
        }

        let newLevel = new Level(
            levelObject.title,
            levelObject.description,
            levelObject.score,
            );
        newLevel.isDefault = levelObject.isDefault;
        newLevel.id = levelObject.id;

        return newLevel;
    }
}
