import Criterium, {CriteriumJsonObject} from "./Criterium";
import TreeNode from "./TreeNode";

export interface CategoryJsonObject {
    id: string;
    title: string,
    color: string,
    criteria: CriteriumJsonObject[]
}
export default class Category extends TreeNode {
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
            id: this.id,
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
            categoryObject.title,
            categoryObject.id
        );

        newCategory.color = categoryObject.color;
        newCategory.id = categoryObject.id;
        categoryObject.criteria
            .map(criteriumJsonObject => Criterium.fromJSON(criteriumJsonObject))
            .forEach(criterium => newCategory.addChild(criterium));

        return newCategory;
    }
}
