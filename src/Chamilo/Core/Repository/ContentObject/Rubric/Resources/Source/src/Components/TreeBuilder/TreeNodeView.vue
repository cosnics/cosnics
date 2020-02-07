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
                    <i class="fa fa-align-justify handle"></i>
                    <i v-if="treeNode.canHaveChildren()" class="fa fa-folder-o folder-icon"></i>
                    {{treeNode.title}}
                </button>
            </div>
        </div>
        <ul class="list-group tree-list-group" v-if="!collapsed">
            <draggable handle=".handle" v-model="treeNode.children" group="tree" :move="checkMove" :group="getGroup()"
                       :animation="250" :invertSwap="true" :component-data="getComponentData()">
                <tree-node-view
                        v-for="child in treeNode.children"
                        :treeNode="child"
                        :level="level + 1"
                        v-bind:key="child.id"
                />
            </draggable>
            <draggable group="action" :filter="'.action-list-group-item'">

                <li v-if="treeNode.constructor.name !== 'Criterium'"
                    class="list-group-item tree-list-group-item action-list-group-item">
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

        checkMove(evt: any) {
            return true;
        }

        getComponentData() {
            console.log("hier");
            return this.treeNode;
        }

        getGroup(to: any) {
            if (this.treeNode.parent instanceof Rubric) {
                return {
                    name: "rubric-child",
                    put: (to: any, from: any, element: any) => {
                        console.log(from.options.group.name);
                        console.log(to.options.group.name);
                        console.log(this.treeNode.title);
                        console.log(element.id);
                        console.log(this.$refs);
                        return true;
                    }

                }
            } else if (this.treeNode.parent instanceof Category) {
                return {
                    name: "category-child",
                    put: (to: any, meh: any, bleh: any, freh: any) => {
                        console.log(to.options.group.name);
                        console.log(this.treeNode.title);

                        return false;
                    }
                }
            } else if (this.treeNode.parent instanceof Cluster) {
                return {
                    name: "cluster-child",
                    put: (to: any, meh: any, bleh: any, freh: any) => {
                        console.log(to.options.group.name);
                        console.log(this.treeNode.title);
                        return true;
                    }
                }
            } else {
                return {
                    name: "root-child",
                    put: (to: any, meh: any, bleh: any, freh: any) => {
                        console.log(to.options.group.name);
                        console.log(this.treeNode.title);
                        return true;
                    }
                }
            }
        }
    }
</script>

<style scoped>
    .list-group-item {
        border: none !important;
    }

    .tree-list-group {
        margin-bottom: 5px !important;
        border: none !important;
    }

    .tree-list-group-item {
        padding-top: 0px;
        padding-bottom: 0px;
        padding-right: 0px;
        border: none !important;
    }

    .action-list-group-item {
        margin-left: 40px;
        padding-left: 0px;
    }

    .tree-list-group-item-content {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .handle {
        cursor: move;
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
</style>
