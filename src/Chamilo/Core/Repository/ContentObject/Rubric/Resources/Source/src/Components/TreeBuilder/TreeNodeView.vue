<template>
    <li class="list-group-item tree-list-group-item" :id="treeNode.id" :ref="treeNode.id">
        <div class="tree-list-group-item-content">
            <div class="spacer">
                <i v-if="!collapsed && treeNode.canHaveChildren()" class="fa fa-caret-down pull-left caret-toggle"
                   v-on:click="collapsed = !collapsed"></i>
                <i v-if="collapsed && treeNode.canHaveChildren()" class="fa fa-caret-right pull-left caret-toggle"
                   v-on:click="collapsed = !collapsed"></i>
            </div>
            <div>
                <button type="button" class="list-group-item" v-on:click="store.selectedTreeNode = treeNode">
                    <i class="fa fa-bars handle-icon handle"></i>
                    <i v-if="treeNode.canHaveChildren()" class="fa fa-folder-o folder-icon"></i>
                    {{treeNode.title}}
                </button>
            </div>
        </div>
        <ul class="list-group tree-list-group" v-if="!collapsed && treeNode.canHaveChildren()">
            <draggable handle=".handle" v-model="children" group="tree" :move="checkMove"
                       :animation="250" ghost-class="ghost" :filter="'.action-list-group-item'" :invertSwap="true">
                <tree-node-view
                        v-for="child in treeNode.children"
                        :treeNode="child"
                        :level="level + 1"
                        v-bind:key="child.id"
                />
                <li v-if="treeNode.canHaveChildren() && !treeNode.hasChildren()"
                    class="list-group-item tree-list-group-item action-list-group-item">
                    Geen items
                </li>
            </draggable>
            <!--draggable v-if="treeNode.canHaveChildren() && !treeNode.hasChildren()" group="action" :filter="'.action-list-group-item'">

                <li v-if="treeNode.constructor.name !== 'Criterium'"
                    class="list-group-item tree-list-group-item action-list-group-item">
                    Geen items
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                        <button type="button" class="btn btn-default"
                                v-on:click="store.selectedTreeNode = treeNode">
                            <i class="fa fa-plus"></i>
                            Voeg Criterium Toe
                        </button>
                        <button v-if="this.treeNode.constructor.name !== 'Category'" type="button"
                                class="btn btn-default"
                                v-on:click="store.selectedTreeNode = treeNode">
                            <i class="fa fa-plus"></i>
                            Voeg Categorie Toe
                        </button>
                        <button v-if="this.treeNode.constructor.name !== 'Category' && this.treeNode.constructor.name !== 'Cluster'"
                                type="button" class="btn btn-default"
                                v-on:click="store.selectedTreeNode = treeNode">
                            <i class="fa fa-plus"></i>
                            Voeg Cluster Toe
                        </button>
                    </div>
                </li>
            </draggable!-->
        </ul>
    </li>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import draggable from 'vuedraggable'
    import "vue-swatches/dist/vue-swatches.min.css"
    import TreeNode from "../../Domain/TreeNode";
    import {logMethod} from "../../Logger";
    import Rubric from "../../Domain/Rubric";
    import Category from "../../Domain/Category";
    import Cluster from "../../Domain/Cluster";
    import Criterium from "../../Domain/Criterium";

    @Component({
        components: {draggable}
    })
    export default class TreeNodeView extends Vue {
        @Prop()
        treeNode!: TreeNode;
        @Prop({
            default: 0
        })
        level!: number;

        collapsed: boolean = false;

        get store(): ScoreRubricStore {
            return this.$root.$data.store;
        }

        get children() {
            let children = this.treeNode.children;
            if(children.length > 0) {
                return children;
            }

            return [
                {
                    'fakeNode': true,
                    'parent': this.treeNode,
                }
            ]
        }

        set children(value: any){

            let fakeElementIndex = value.findIndex((child: any) => child.fakeNode);
            if(fakeElementIndex > -1)
            {
                value.splice(fakeElementIndex, 1);
            }

            this.treeNode.children = value;
            this.treeNode.children.forEach(child => child.parent = this.treeNode);
        }

        placeHolder(parentNode: TreeNode) {
            return {
                'parent': parentNode,
            }
        }

        checkMove(evt: any) {
            if(!evt.relatedContext.element) {
                console.log(evt);
                return false;
            }

            let draggedTreeNode:TreeNode = evt.draggedContext.element;
            let parentTreeNode:TreeNode = evt.relatedContext.element.parent;

            if (parentTreeNode instanceof Rubric) {
                return true;
            } else if (parentTreeNode instanceof Cluster) {
                return draggedTreeNode instanceof Criterium || draggedTreeNode instanceof Category;
            } else if (parentTreeNode instanceof Category) {
                return draggedTreeNode instanceof Criterium;
            }
            return false;
        }

        getGroup(to: any) {
            if (this.treeNode.parent instanceof Rubric) {
                return {
                    name: "rubric-child",
                    put: (toGroup: any, fromGroup: any, element: any, meh: any, bleh: any) => {
                        // let draggedTreeNode = element._underlying_vm_;
                        // if (this.treeNode instanceof Criterium)
                        //     return false;
                        // if (this.treeNode instanceof Category) {
                        //     if (!(draggedTreeNode instanceof Criterium))
                        //         return false;
                        //     else return true;
                        // }
                        // if (this.treeNode instanceof Cluster) {
                        //     if (!(draggedTreeNode instanceof Category || draggedTreeNode instanceof Criterium))
                        //         return false;
                        //     else
                        //         return true;
                        // }
                        //
                        // return true;
                    }

                }
            } else if (this.treeNode.parent instanceof Category) {
                return {
                    name: "category-child",
                    put: (toGroup: any, fromGroup: any, element: any, meh: any, bleh: any) => {
                        // return false;
                    }
                }
            } else if (this.treeNode.parent instanceof Cluster) {
                return {
                    name: "cluster-child",
                    put: (toGroup: any, fromGroup: any, element: any, meh: any, bleh: any) => {
                        // let draggedTreeNode = element._underlying_vm_;
                        // if (this.treeNode instanceof Criterium)
                        //     return false;
                        // else if (draggedTreeNode instanceof Category || draggedTreeNode instanceof Cluster)
                        //     return false;
                        //
                        // return true;
                    }
                }
            } else {
                return {
                    name: "root-child",
                    put: (toGroup: any, fromGroup: any, element: any, meh: any, bleh: any) => {
                        // return true
                    }
                }
            }
        }
    }
</script>

<style scoped>
    .list-group-item {
        border: none;
    }

    .tree-list-group {
        margin-bottom: 5px;
        border: none;
    }

    .tree-list-group-item {
        padding-top: 0px;
        padding-bottom: 0px;
        padding-right: 0px;
        border: none;
    }

    .action-list-group-item {
        margin-left: 40px;
        padding-left: 0px;
        border: slategray dotted 1px;
    }

    .tree-list-group-item-content {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .handle {
        cursor: grab;
    }
    .handle:active {
        cursor: grabbing;
    }


    .caret-toggle {
        padding-top: 10px;
        cursor: pointer;
    }

    .spacer {
        min-width: 10px;
    }

    .folder-icon {
        margin-left: 3px;
    }

    .handle-icon {
        margin-right: 5px;
    }
    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }
</style>
