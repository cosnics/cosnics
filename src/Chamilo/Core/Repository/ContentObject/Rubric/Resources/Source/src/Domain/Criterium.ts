import TreeNode from './TreeNode';

export type CriteriumId = string;

export interface CriteriumJsonObject {
    id: string,
    title: string,
    weight: number,
    rel_weight: number|null
}

export default class Criterium extends TreeNode {
    public weight: number = 100;
    public rel_weight: number|null = null;

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
            weight: this.weight,
            rel_weight: this.rel_weight
        }
    }

    static fromJSON(criterium: string|CriteriumJsonObject): Criterium {
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
        newCriterium.rel_weight = typeof criteriumObject.rel_weight === 'number' ? criteriumObject.rel_weight : null;

        return newCriterium;
    }
}
