<template>
    <div class="container-fluid">
        <levels-table></levels-table>
        <div class="row">
            <div class="col-sm-4 col-md-3 tree-menu-column">
                <div class="rubric-tree">
                    <ul class="list-group rubric-list-group">
                        <tree-node-view :tree-node="store.rubric"></tree-node-view>
                    </ul>
                </div>
            </div>
            <div class="col-sm-8 col-md-9 content-column">
                <criterium-node-builder v-if="selectedNodeIsCriterium" :criterium="store.selectedTreeNode"></criterium-node-builder>
                <category-node-builder v-if="selectedNodeIsCategory" :category="store.selectedTreeNode"></category-node-builder>
                <cluster-node-builder v-if="selectedNodeIsCluster" :cluster="store.selectedTreeNode"></cluster-node-builder>
                <rubric-node-builder v-if="selectedNodeIsRubric" :rubric="store.selectedTreeNode"></rubric-node-builder>
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
    import TreeNodeView from "./TreeNodeView.vue";

    @Component({
        name: 'score-rubric-tree-builder',
        components: {
            TreeNodeView,
            RubricNodeBuilder, ClusterNodeBuilder, CategoryNodeBuilder, LevelsTable, CriteriumNodeBuilder},
    })
    export default class ScoreRubricTreeBuilder extends Vue {

        getDefaultCluster() {
            let cluster = new Cluster("");
            cluster.addCategory(this.getDefaultCategory());

            return cluster;
        }

        get selectedNodeIsCriterium(){
            return this.store.selectedTreeNode instanceof Criterium;
        }
        get selectedNodeIsCategory(){
            return this.store.selectedTreeNode instanceof Category;
        }
        get selectedNodeIsCluster(){
            return this.store.selectedTreeNode instanceof Cluster;
        }
        get selectedNodeIsRubric(){
            return this.store.selectedTreeNode instanceof Rubric;
        }

        get store(){
            return this.$root.$data.store;
        }

        get rubric(){
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
        }
    }
    //todo replace border with padding
</script>

<style>

</style>

<style scoped>
    .rubric-tree {
        text-align: left;
        border-right: 1px solid #f5f7fb;
    }

    .rubric-list-group {
        margin-right: 10px;
    }
    .tree-menu-column {
        padding-right: 5px;
    }
    .content-column {
        padding-left: 20px;
    }
</style>
