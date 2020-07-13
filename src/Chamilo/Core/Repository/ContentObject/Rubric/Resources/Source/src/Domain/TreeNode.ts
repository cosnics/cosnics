import {logMethod} from "../Logger";

export interface TreeNodeInterface {
    readonly children: ReadonlyArray<TreeNode>;
    parent: TreeNode | null;
    title: string;
    isRoot: boolean;
    getType(): string;
    getScore(): number;
    hasChildren(): boolean;
    canHaveChildren(): boolean;
}

export default abstract class TreeNode implements TreeNodeInterface {
    public parent: TreeNode|null;
    public title: string = '';
    public id: string;
    protected _children: TreeNode[] = [];
    public isRoot: boolean = false;

    constructor(title: string = '', id?:string) {
        this.parent = null;
        if(!id)
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
        else this.id = id;
        this.title = title;
    }

    abstract getType(): string;

    abstract getScore(): number;

    canHaveChildren(): boolean {
        return true;
    }

    hasChildren(): boolean {
        return this.children.length > 0;
    }

    get children():Array<TreeNode> {
        return this._children;
    }

    set children(children:TreeNode[]) {
        this._children = children;
    }

    toString() {
        return this.title;
    }

    addChild(treeNode: TreeNode, index:number = 0): void {
        treeNode.parent = this;
        this._children.splice(index, 0, treeNode);

        if(this.parent)
            this.parent.notifyAddChild(treeNode);

        if(this.isRoot)
            this.notifyAddChild(treeNode);
    }

    protected notifyAddChild(treeNode: TreeNode): void {
        if(this.parent) {//bubble up the change
            this.parent.notifyAddChild(treeNode);
        }
    }
    protected notifyRemoveChild(treeNodeContainer: TreeNode, treeNode: TreeNode): void{
        if(this.parent) //bubble up the chain
            this.parent.notifyRemoveChild(treeNodeContainer, treeNode);
    }

    removeChild(treeNode: TreeNode, notify= true): void {
        if(treeNode.parent !== this) {
            throw new Error("treeNode: " + treeNode.title + " not part of treeNode: " + this.title);
        }
        const index = this._children.indexOf(treeNode);
        this._children.splice(index, 1);
        if (notify && this.parent) {
            this.parent.notifyRemoveChild(this, treeNode);
        }
        treeNode.parent = null;
    }

    moveChild(child: TreeNode, newIndex:number, oldIndex?:number) {
        if(child.parent !== this) {
            throw new Error("treeNode: " + child.title + " not part of treeNode: " + this.title);
        }

        if(!oldIndex)
            oldIndex = this.children.indexOf(child);

        this.moveItemInArray(this.children, oldIndex, newIndex);
    }


    /*static moveTreeNodeToContainerAtIndex(treeNode: TreeNode, container: TreeNode, index:number ) {
        ContainerManager.removeTreeNodeFromContainer(treeNode, container);
    }*/


    protected moveItemInArray(array: any[], from: number, to: number) {
        if (to >= array.length || from >= array.length)
            return;
        if (to < 0 || from < 0)
            return;

        array.splice(to, 0, array.splice(from, 1)[0]);
    }
}
