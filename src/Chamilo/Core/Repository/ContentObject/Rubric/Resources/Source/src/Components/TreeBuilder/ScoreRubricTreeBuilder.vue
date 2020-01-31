<template>
    <div class="container-fluid">
        <levels-table></levels-table>
        <div class="row">
            <div class="col-md-4">
                <div id="tree" class="rubric-tree"></div>
            </div>
            <div class="col-md-8">
                <criterium-node-builder v-if="selectedNodeIsCriterium" :criterium="selectedTreeNode"></criterium-node-builder>
                <category-node-builder v-if="selectedNodeIsCategory" :category="selectedTreeNode"></category-node-builder>
                <cluster-node-builder v-if="selectedNodeIsCluster" :cluster="selectedTreeNode"></cluster-node-builder>
                <rubric-node-builder v-if="selectedNodeIsRubric" :rubric="selectedTreeNode"></rubric-node-builder>
            </div>
        </div>

    </div>
</template>

<script lang="ts">
    import {Component, Vue, Watch} from "vue-property-decorator";
    import LevelsTable from "../LevelsTable.vue";
    import CriteriumNodeBuilder from "./CriteriumNodeBuilder.vue";
    import ScoreRubricStore from "../../ScoreRubricStore";
    import {createTree} from 'jquery.fancytree';
    import 'jquery.fancytree/dist/modules/jquery.fancytree.edit';
    import 'jquery.fancytree/dist/modules/jquery.fancytree.dnd';
    import Cluster from "../../Domain/Cluster";
    import Criterium from "../../Domain/Criterium";
    import Category from "../../Domain/Category";
    import Rubric from "../../Domain/Rubric";
    import TreeNode from "../../Domain/TreeNode";
    import CategoryNodeBuilder from "./CategoryNodeBuilder.vue";
    import ClusterNodeBuilder from "./ClusterNodeBuilder.vue";
    import RubricNodeBuilder from "./RubricNodeBuilder.vue";

    @Component({
        name: 'score-rubric-tree-builder',
        components: {RubricNodeBuilder, ClusterNodeBuilder, CategoryNodeBuilder, LevelsTable, CriteriumNodeBuilder},
    })
    export default class ScoreRubricTreeBuilder extends Vue {
        protected selectedTreeNode:TreeNode = this.store.rubric;

        getDefaultCluster() {
            let cluster = new Cluster("");
            cluster.addCategory(this.getDefaultCategory());

            return cluster;
        }

        get selectedNodeIsCriterium(){
            return this.selectedTreeNode instanceof Criterium;
        }
        get selectedNodeIsCategory(){
            return this.selectedTreeNode instanceof Category;
        }
        get selectedNodeIsCluster(){
            return this.selectedTreeNode instanceof Cluster;
        }
        get selectedNodeIsRubric(){
            return this.selectedTreeNode instanceof Rubric;
        }

        get store(){
            return this.$root.$data.store;
        }

        get rubric(){
            console.log("hiedr");
            return this.store.rubric;
        }

        getDefaultCategory() {
            let category = new Category("Categorie 1");
            category.color = "blue";
            category.addCriterium(this.getDefaultCriterium());

            return category;
        }

        getDefaultCriterium() {
            return new Criterium("");
        }


        get treeData() {
            let mapper = (treeNode: TreeNode):any => {
                return {
                    title: treeNode.title,
                    key: treeNode.id,
                    folder: !(treeNode instanceof Criterium),
                    children: treeNode.children.map(mapper),
                    data: {
                        treeNode: treeNode
                    }
                }
            };

            return [mapper(this.store.rubric)];
        }

        @Watch('store.rubric')
        onRubricChanged(){
            console.log("change");
        }


        mounted() {
            let tree = createTree('#tree', {
                extensions: ['dnd', 'edit'],
                source: this.treeData,
                checkbox: false,
                "activate": (event: any, data:any) => {
                    this.selectedTreeNode = data.node.data.treeNode;
                },
                dnd: {
                    // Available options with their default:
                    autoExpandMS: 250,   // Expand nodes after n milliseconds of hovering
                    draggable: null,      // Additional options passed to jQuery UI draggable
                    droppable: null,      // Additional options passed to jQuery UI droppable
                    dropMarkerOffsetX: -24,  // absolute position offset for .fancytree-drop-marker
                    // relatively to ..fancytree-title (icon/img near a node accepting drop)
                    dropMarkerInsertOffsetX: -16, // additional offset for drop-marker with hitMode = "before"/"after"
                    focusOnClick: false,  // Focus, although draggable cancels mousedown event (#270)
                    preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
                    preventVoidMoves: true,      // Prevent dropping nodes 'before self', etc.
                    smartRevert: true,    // set draggable.revert = true if drop was rejected

                    // Events that make tree nodes draggable
                    dragStart: function (node:any, data:any) {
                        return !node.getParent().isRoot();

                    },
                    initHelper: null,     // Callback(sourceNode, data)
                    updateHelper: null,   // Callback(sourceNode, data)

                    // Events that make tree nodes accept draggables
                    dragEnter: (node:any, data:any) => {
                        if(node.getParent().isRoot()) {
                            return [];
                        }

                        if(data.otherNode.data.treeNode instanceof Category){
                             if(node.data.treeNode instanceof Category)
                                 return ['before', 'after'];
                             if(node.getParent().data.treeNode instanceof Category)//drop check on criterium
                                 return false;
                        }

                        if(data.otherNode.data.treeNode instanceof Cluster){
                            if(node.data.treeNode instanceof Category)
                                return false;
                            if(node.data.treeNode instanceof Cluster)
                                return ['before', 'after'];
                            if(node.getParent().data.treeNode instanceof Category)//drop check on criterium
                                return false;
                            if(node.getParent().data.treeNode instanceof Cluster)//drop check on criterium
                                return false;
                        }

                        if (!node.isFolder()) {
                            return ["before", "after"];
                        }
                        return true;
                    },      // Callback(targetNode, data)
                    dragExpand: function(node:any, data:any) {
                        // return false to prevent auto-expanding data.node on hover
                    },
                    dragOver: function(node:any, data:any) {
                    },
                    dragLeave: function(node:any, data:any) {
                    },
                    dragStop: function(node:any, data:any) {
                    },
                    dragDrop: (node:any, data:any) => {
                        data.otherNode.moveTo(node, data.hitMode); //warning: this could be troublesome with reactivity!
                        let child:TreeNode = data.otherNode.data.treeNode;
                        if(child.parent === null)
                            return false;//cannot move root
                        child.parent.removeChild(child);
                        let newParent:TreeNode = data.otherNode.getParent().data.treeNode;
                        newParent.addChild(child, data.otherNode.getIndex() + 1)
                    }
                },
            });
            tree.expandAll();
        }
    }
    //todo replace border with padding
</script>

<style>

</style>

<style scoped>
    .rubric-tree {
        text-align: left;
    }
</style>
