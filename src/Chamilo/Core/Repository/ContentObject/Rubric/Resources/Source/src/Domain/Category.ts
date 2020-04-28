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

    rgbColor(opacity: number = 1){
        console.log(this.color);
        // note: hexStr should be #rrggbb
        var hex = parseInt(this.color.substring(1), 16);
        var r = (hex & 0xff0000) >> 16;
        var g = (hex & 0x00ff00) >> 8;
        var b = hex & 0x0000ff;
        return [r, g, b, opacity].join(',');
    }

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

        if (typeof category === 'string') {
            categoryObject = JSON.parse(category);
        } else {
            categoryObject = category;
        }

        const newCategory = new Category(
            categoryObject.title,
            categoryObject.id
        );

        newCategory.color = categoryObject.color;
        newCategory.id = categoryObject.id;

        const criteria = categoryObject.criteria
            .map(criteriumJsonObject => Criterium.fromJSON(criteriumJsonObject)) as TreeNode[];
        criteria.forEach(criterium => criterium.parent = newCategory);

        // Note: Setting children directly loses the notifyAddChild behavior.
        newCategory._children = criteria;

        return newCategory;
    }
}
