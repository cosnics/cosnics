<template>
    <li class="list-group-item tree-list-group-item w-100" :id="treeNode.id" :ref="treeNode.id"
        :style="treeListGroupItemStyle">
        <div class="tree-list-group-item-content">
            <div class="spacer" v-if="treeNode.canHaveChildren()">
                <i v-if="!collapsed && treeNode.canHaveChildren()" class="fa fa-caret-down pull-left caret-toggle"
                   v-on:click="collapsed = !collapsed"></i>
                <i v-if="collapsed && treeNode.canHaveChildren()" class="fa fa-caret-right pull-left caret-toggle"
                   v-on:click="collapsed = !collapsed"></i>
            </div>
            <div class="w-100">
                <button type="button" class="list-group-item list-group-item-button w-100" :style="categoryHeaderColor"
                        v-on:click="store.selectedTreeNode = treeNode">
                    <i class="fa fa-1x fa-bars handle-icon handle"></i>
                    <i v-if="treeNode.canHaveChildren() && treeNode" :class="folderIcon" class="fa folder-icon"></i>
                    {{treeNode.title}}
                </button>
            </div>
        </div>
        <ul class="list-group tree-list-group" v-if="!collapsed && treeNode.canHaveChildren()"
            :style="categoryContainerColor">
            <draggable handle=".handle" v-model="children" group="tree" :move="checkMove"
                       :animation="250" ghost-class="ghost" :filter="'.action-list-group-item'" :invertSwap="true"
                       @change="onChange"
            >
                <tree-node-view
                        v-for="child in treeNode.children"
                        :treeNode="child"
                        :level="level + 1"
                        v-bind:key="child.id"
                />
                <li v-if="treeNode.canHaveChildren() && !treeNode.hasChildren()"
                    class="list-group-item tree-list-group-item empty-list-group-item">
                    Geen items
                </li>
            </draggable>
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
            let children = this.treeNode.children.slice();  //we give a copy to the tree, because we want to manipulate the array and save changes in backend. See onChange event
            if (children.length > 0) {
                return children;
            }

            return [
                {
                    'fakeNode': true,
                    'parent': this.treeNode,
                }
            ]
        }

        set children(value: any) {
            let fakeElementIndex = value.findIndex((child: any) => child.fakeNode);
            if (fakeElementIndex > -1) {
                value.splice(fakeElementIndex, 1);
            }

            //setting children is handled in onchange event.
        }

        onChange(evt: any) {
            if (evt.added && evt.added.element) {
                /*
                Todo: encapsulate
                 */
                (evt.added.element as TreeNode).parent!.removeChild(evt.added.element);//move
                this.treeNode.addChild(evt.added.element, evt.added.newIndex); //move
                this.store.moveChild(evt.added.element, this.treeNode, evt.added.newIndex);
                this.children = this.treeNode.children.slice();

            } else if (evt.removed) { //remove is called as part of a move, so we handle it in the add part. This way we can encapsulate a move.
                //do nothing because it is handled in 'added'
            } else if (evt.moved) { //moved in same list
                this.treeNode.moveChild(evt.moved.element, evt.moved.newIndex, evt.moved.oldIndex);
                this.store.moveChild(evt.moved.element, this.treeNode, evt.moved.newIndex);
                this.children = this.treeNode.children.slice();
            } else {
                console.log("unsupported action");
            }
        }

        checkMove(evt: any) {
            if (!evt.relatedContext.element) {
                return false;
            }

            let draggedTreeNode: TreeNode = evt.draggedContext.element;
            let parentTreeNode: TreeNode = evt.relatedContext.element.parent;

            if (parentTreeNode instanceof Rubric) {
                return true;
            } else if (parentTreeNode instanceof Cluster) {
                return draggedTreeNode instanceof Criterium || draggedTreeNode instanceof Category;
            } else if (parentTreeNode instanceof Category) {
                return draggedTreeNode instanceof Criterium;
            }
            return false;
        }

        get folderIcon() {
            return {
                'fa-folder-o': this.treeNode instanceof Category,
                'fa-map-o': this.treeNode instanceof Cluster,
                'fa-institution': this.treeNode instanceof Rubric
            };
        }

        get categoryHeaderColor() {
            if (this.treeNode instanceof Category)
                return {
                    'background-color': 'rgba(' + this.treeNode.rgbColor(0.7) + ')',
                    color: 'white',
                    'border-bottom-left-radius': this.collapsed ? 'inherit': '0px',
                    'border-bottom-right-radius': this.collapsed ? 'inherit': '0px'
                }
            else {
                return {};
            }
        }

        get treeListGroupItemStyle() {
            if (this.treeNode instanceof Criterium && this.treeNode.parent instanceof Category)
                return {
                    'padding-left': '0px'
                }
            else return {};
        }

        get categoryContainerColor() {
            if (this.treeNode instanceof Category) {
                return {
                    'margin-left': '12px',
                    'border': '1px solid',
                    'border-color': 'rgba(' + this.treeNode.rgbColor(0.7) + ')',
                    'border-bottom-left-radius': '5px',
                    'border-bottom-right-radius': '5px',
                }
            } else {
                return {};
            }
        }

    }
</script>

<style scoped>
    .list-group-item {
        border: none;
    }

    .tree-list-group {
        margin-bottom: 2px;
        border: none;
    }

    .tree-list-group-item {
        margin-top: 2px;
        margin-bottom: 2px;
        padding-top: 0px;
        padding-bottom: 0px;
        padding-right: 0px;
        border: none;
    }

    .empty-list-group-item {
        margin-left: 40px;
        padding-left: 5px;
        padding-top: 4px;
        padding-bottom: 4px;
        color: #cacaca;
    }

    .tree-list-group-item-content {
        display: flex;
    }

    .handle {
        cursor: grab;
        opacity: 0.3;
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
        opacity: 0.7;
        border-left: solid 5px #c8ebfb;
    }

    .list-group-item-button {
        padding-top: 5px;
        padding-bottom: 5px;
        border: none;
        color: #333;
        background-color: #fff;
        text-align: left;
        padding-left: 10px;
    }

    .w-100 {
        width: 100%;
    }
</style>
