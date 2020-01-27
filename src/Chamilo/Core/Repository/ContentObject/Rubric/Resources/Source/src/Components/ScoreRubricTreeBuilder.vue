<template>
    <div class="container">
        <div id="tree"></div>
    </div>
</template>

<script lang="ts">
    import $ from "jquery";
    import {Component, Vue} from "vue-property-decorator";
    import LevelsTable from "./LevelsTable.vue";
    import ScoreRubricStore from "../ScoreRubricStore";
    import {createTree} from 'jquery.fancytree';
    import Cluster from "../Domain/Cluster";
    import Criterium from "../Domain/Criterium";
    import Category from "../Domain/Category";

    @Component({
        name: 'score-rubric-tree-builder',
        components: {LevelsTable},
        filters: {
            capitalize(value: string) {
                if (!value) {
                    return "";
                }

                return value.toUpperCase();
            }
        }
    })
    export default class ScoreRubricTreeBuilder extends Vue {
        public store: ScoreRubricStore = this.$root.$data.store;

        getDefaultCluster() {
            let cluster = new Cluster("");
            cluster.addCategory(this.getDefaultCategory());

            return cluster;
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
            return this.store.rubric.clusters.map(
                cluster => {
                    return {
                        title: cluster.title,
                        key: cluster.title,
                        folder: true,
                        children: cluster.children.map(child => {
                            return {
                                title: child.title,
                                key: child.title,
                                folder: child.canHaveChildren(),
                                children: child.canHaveChildren() ? child.children.map(level2Child => {
                                    return {
                                        title: level2Child.title,
                                        key: level2Child.title,
                                        folder: level2Child.canHaveChildren()
                                    }
                                }) : []
                            }
                        })

                    }
                }
            )
        }

        mounted() {
            console.log($);
            let tree = createTree('#tree', {
                source: this.treeData
            });
        }
    }
    //todo replace border with padding
</script>

<style scoped>

</style>
