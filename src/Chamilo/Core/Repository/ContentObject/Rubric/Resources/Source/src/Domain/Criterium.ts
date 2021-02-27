import TreeNode from './TreeNode';

export type CriteriumId = string;

export interface CriteriumJsonObject {
    id: string,
    title: string,
    weight: number
}

export default class Criterium extends TreeNode {
    public weight: number = 100;

    public weightToString(): string {
        return String(this.weight * 100);
    }

    public getType(): string {
        return 'criterium';
    }

    public getScore(): number {
        return 0;
    }

    public toString(): string {
        return `Criterium (id: ${this.id}, title: ${this.title})`;
    }

    canHaveChildren(): boolean {
        return false;
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

        if (typeof criterium === 'string') {
            criteriumObject = JSON.parse(criterium);
        } else {
            criteriumObject = criterium;
        }

        const newCriterium = new Criterium(
            criteriumObject.title,
            criteriumObject.id
        );

        newCriterium.weight = criteriumObject.weight;

        return newCriterium;
    }}
