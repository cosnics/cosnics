import {Element} from "./Rubric";
import {LevelJsonObject} from "./Level";
import Choice from "./Choice";
import Container from "./Container";

export type CriteriumId = string;

export interface CriteriumJsonObject {
    id: string,
    title: string,
    weight: number
}

export default class Criterium implements Element{
    public id: CriteriumId;
    public title: string;
    public weight: number = 100;
    public selectedLevelIndex: number = 0;
    public parent:Container|null = null;
    public readonly children:Element[] = [];
    public choices: Choice[] = [];

    constructor(title: string, id?:string) {
        if(!id)
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
        else this.id = id;
        this.title = title;
    }

    public weightToString(): string {
        return String(this.weight * 100);
    }

    public getScore(): number {
        return 0;
    }
    public toString(): string {
        return `Criterium (id: ${this.id}, title: ${this.title})`;
    }

    toJSON(): CriteriumJsonObject {
        return {
            id: this.id,
            title: this.title,
            weight: this.weight
        }
    }

    static fromJSON(criterium:string|CriteriumJsonObject):Criterium {
        let criteriumObject: CriteriumJsonObject;
        if(typeof criterium === 'string') {
            criteriumObject = JSON.parse(criterium);
        } else {
            criteriumObject = criterium;
        }

        let newCriterium = new Criterium(
            criteriumObject.title,
            criteriumObject.id
        );

        newCriterium.weight = criteriumObject.weight;

        return newCriterium;
    }}
