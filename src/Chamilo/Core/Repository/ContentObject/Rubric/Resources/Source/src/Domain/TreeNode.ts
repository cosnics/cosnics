export interface TreeNodeInterface {
    readonly children: ReadonlyArray<TreeNode>;
    parent: TreeNode | null;
    title: string;
    getScore(): number;
    hasChildren(): boolean;
    canHaveChildren(): boolean;
}

export interface TreeNodeInterface {
    readonly children: ReadonlyArray<TreeNode>;
    parent: TreeNode | null;
    title: string;
    isRoot: boolean;
    getScore(): number;
    hasChildren(): boolean;
}

export default abstract class TreeNode implements TreeNodeInterface {
    public parent: TreeNode|null;
    public title: string = '';
    public id: string;
    protected _children:TreeNode[] = [];
    public isRoot:boolean = false;

    constructor(title: string = '', id?:string) {
        this.parent = null;
        if(!id)
            this.id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15); //GUID
        else this.id = id;
        this.title = title;
    }

    abstract getScore(): number;

    canHaveChildren(): boolean {
        return true;
    }

    hasChildren(): boolean {
        return this.children.length > 0;
    }

    get children():ReadonlyArray<TreeNode> {
        return this._children.slice();
    }

    toString() {
        return this.title;
    }

    protected addChild(treeNode: TreeNode): void {
        treeNode.parent = this;
        this._children.push(treeNode);
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

    protected removeChild(treeNode: TreeNode): void {
        if(treeNode.parent !== this) {
            throw new Error("treeNode: " + treeNode.title + " not part of treeNode: " + this.title);
        }
        const index = this._children.indexOf(treeNode);
        this._children.splice(index, 1);
        if(this.parent)
            this.parent.notifyRemoveChild(this, treeNode);
        treeNode.parent = null;
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
