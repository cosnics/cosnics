<template>
    <div class="treenode-weight" :class="{'is-selected': options.selectedTreeNode === treeNode, 'is-highlighted': options.highlightedTreeNode === treeNode}" v-if="useScores && rubric.useRelativeWeights && showScores" @click.stop="options.selectedTreeNode = treeNode" @mouseover="options.highlightedTreeNode = treeNode" @mouseout="options.highlightedTreeNode = null">
        <span>{{ rubric.getRelativeWeight(treeNode)|formatNum }}</span><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../../Domain/Rubric';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';

    @Component({
        filters: {
            formatNum: function (v: number|null) {
                if (v === null) { return ''; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeRubricResults extends Vue {

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster | Category | Criterium;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: true}) readonly showScores!: boolean;

        get useScores() {
            return this.rubric.useScores;
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-weight {
        cursor: default;
        padding-top: .15625rem;
        position: relative;
        text-align: center;
        z-index: 0;

        &::before {
            bottom: -.3125rem;
            content: '';
            left: 0;
            position: absolute;
            right: -.4375rem;
            top: -.3125rem;
            z-index: -1;
        }

        &.is-highlighted::before {
            background: hsla(230, 15%, 97%, 1);
        }

        &.is-selected::before {
            background: hsla(230, 15%, 94%, 1);
        }
    }
</style>