import Category, {CategoryJsonObject} from "./Category";
import Criterium, {CriteriumJsonObject} from "./Criterium";
import TreeNode from "./TreeNode";

export interface ClusterJsonObject {
    id: string,
    title: string;
    categories: CategoryJsonObject[];
    criteria: CriteriumJsonObject[];
}

export default class Cluster extends TreeNode {
    public collapsed: boolean = false;

    public getType(): string {
        return 'cluster';
    }

    public getScore(): number {
        return 0;
    }

    public toggleCollapsed() { //todo view state?
        this.collapsed = !this.collapsed;
    }

    public addCategory(category: Category): void {
        super.addChild(category);
    }

    public addCriterium(criterium: Criterium): void {
        super.addChild(criterium);
    }

    public removeCriterium(criterium: Criterium): void {
        super.removeChild(criterium);
    }

    public removeCategory(category:Category) {
        super.removeChild(category);
    }

    get criteria():Criterium[] {
        return this.children.filter(child => (child instanceof Criterium)) as Criterium[];
    }

    get categories():Category[] {
        return this.children.filter(child => (child instanceof Category)) as Category[];
    }

    get clusters():Cluster[] {
        return this.children as Cluster[]; //invariant garded at addChild
    }

    toJSON(): ClusterJsonObject {
        return {
            id: this.id,
            title: this.title,
            categories: this.children.filter(child => (child instanceof Category)).map((category) => (category as Category).toJSON()), //todo typeguards?
            criteria: this.children.filter(child => (child instanceof Criterium)).map((criterium) => (criterium as Criterium).toJSON())
        }
    }

    static fromJSON(cluster: string | ClusterJsonObject): Cluster {
        let clusterObject: ClusterJsonObject;

        if (typeof cluster === 'string') {
            clusterObject = JSON.parse(cluster);
        } else {
            clusterObject = cluster;
        }

        const newCluster = new Cluster(
            clusterObject.title,
            clusterObject.id
        );

        const categories = clusterObject.categories
            .map(categoryJsonObject => Category.fromJSON(categoryJsonObject)) as TreeNode[];
        const criteria = clusterObject.criteria
            .map(criteriumObject => Criterium.fromJSON(criteriumObject)) as TreeNode[];

        const children = categories.concat(criteria);
        children.forEach(child => child.parent = newCluster);

        // Note: Setting children directly loses the notifyAddChild behavior.
        newCluster._children = children;

        return newCluster;
    }
}
