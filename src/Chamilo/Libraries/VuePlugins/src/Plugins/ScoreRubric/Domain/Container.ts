import {Element} from "@/Plugins/ScoreRubric/Domain/Rubric";

export interface ContainerInterface extends Element {
    readonly children: ReadonlyArray<Element>;
}

export default abstract class Container implements ContainerInterface {
    public parent: Container|null;
    public title: string = '';
    protected _children:Element[] = [];

    constructor(title: string = '') {
        this.parent = null;
        this.title = title;
    }

    abstract getScore(): number;

    get children():ReadonlyArray<Element> {
        return this._children.slice();
    }

    protected addChild(element: Element): void {
        element.parent = this;
        this._children.push(element);
        if(this.parent)
            this.parent.notifyAddChild(element);
    }

    protected notifyAddChild(element: Element): void {
        if(this.parent) {//bubble up the change
            this.parent.notifyAddChild(element);
        }
    }
    protected notifyRemoveChild(container: Container, element: Element): void{
        if(this.parent) //bubble up the chain
            this.parent.notifyRemoveChild(container, element);
    }

    protected removeChild(element: Element): void {
        if(element.parent !== this) {
            throw new Error("element: " + element.title + " not part of container: " + this.title);
        }
        const index = this._children.indexOf(element);
        this._children.splice(index, 1);
        if(this.parent)
            this.parent.notifyRemoveChild(this, element);
        element.parent = null;
    }

    /*static moveElementToContainerAtIndex(element: Element, container: Container, index:number ) {
        ContainerManager.removeElementFromContainer(element, container);
    }*/


    protected moveItemInArray(array: any[], from: number, to: number) {
        if (to >= array.length || from >= array.length)
            return;
        if (to < 0 || from < 0)
            return;

        array.splice(to, 0, array.splice(from, 1)[0]);
    }
}
