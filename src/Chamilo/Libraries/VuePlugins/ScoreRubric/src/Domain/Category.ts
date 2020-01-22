import Criterium, {CriteriumJsonObject} from "./Criterium";
import {Element} from "./Rubric";
import Container from "./Container";

export interface CategoryJsonObject {
    title: string,
    color: string,
    criteria: CriteriumJsonObject[]
}
export default class Category extends Container {
    public color: string = 'blue';

    public getScore(): number {
        return 0;
    }

    addCriterium(criterium: Criterium): void {
        super.addChild(criterium);
    }

    removeCriterium(criterium: Criterium): void {
        super.removeChild(criterium);
    }

    get criteria():Criterium[] {
        return this.children.filter(child => (child instanceof Criterium)) as Criterium[];
    }

    toJSON():CategoryJsonObject {
        return {
            title: this.title,
            color: this.color,
            criteria: this._children as Criterium[]
        }
    }

    static fromJSON(category:string|CategoryJsonObject):Category {
        let categoryObject: CategoryJsonObject;
        if(typeof category === 'string') {
            categoryObject = JSON.parse(category);
        } else {
            categoryObject = category;
        }

        let newCategory = new Category(
            categoryObject.title
        );

        newCategory.color = categoryObject.color;
        categoryObject.criteria
            .map(criteriumJsonObject => Criterium.fromJSON(criteriumJsonObject))
            .forEach(criterium => newCategory.addChild(criterium));

        return newCategory;
    }
}
