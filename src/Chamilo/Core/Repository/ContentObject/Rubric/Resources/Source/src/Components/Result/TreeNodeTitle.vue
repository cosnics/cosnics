<template>
    <div class="treenode-title-header-wrap" :class="{'rb-col-start-1': isCluster, 'mod-category': isCategory, 'has-category': (isCategory && !!treeNode.title) || (isCriterium && !!treeNode.parent.title), 'is-selected': options.selectedTreeNode === treeNode, 'is-highlighted': options.highlightedTreeNode === treeNode}"
         @click.stop="options.selectedTreeNode = treeNode" @mouseover="options.highlightedTreeNode = treeNode" @mouseout="options.highlightedTreeNode = null"
         :style="color">
        <div class="treenode-title-header">
            <div v-if="nodeType !== 'cluster'" class="treenode-title-header-pre" :class="[`mod-${nodeType}`, {'mod-no-color': isCategory && !treeNode.color}]"></div>
            <h3 v-if="isCriterium" class="treenode-title criterium-title u-markdown-criterium" :class="{'mod-no-category': !hasCategoryTitle}" v-html="treeNode.toMarkdown()"></h3>
            <component v-else :is="headerTag" class="treenode-title" :class="[`${nodeType}-title`]">{{ treeNode.title }}</component>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../../Domain/Rubric';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';

    @Component({})
    export default class TreeNodeTitle extends Vue {
        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster | Category | Criterium;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;

        get nodeType() {
            return this.treeNode.getType();
        }

        get isCluster() {
            return this.nodeType === 'cluster';
        }

        get isCategory() {
            return this.nodeType === 'category';
        }

        get isCriterium() {
            return this.nodeType === 'criterium';
        }

        get hasCategoryTitle() {
            if (this.isCriterium) {
                const parentNode = this.treeNode.parent;
                if (parentNode instanceof Category) {
                    return !!parentNode.title;
                }
            }
            return false;
        }

        get color() {
            let category;
            if (this.nodeType === 'category') {
                category = this.treeNode;
            } else if (this.nodeType === 'criterium') {
                category = this.treeNode.parent;
            }
            if (!(category instanceof Category)) { return ''; }
            return `--category-color: ${ category.title && category.color ? category.color : '#999' }`;
        }

        get headerTag() {
            switch (this.nodeType) {
                case 'cluster':
                    return 'h1';
                case 'category':
                    return 'h2';
                case 'criterium':
                    return 'h3;'
                default:
                    return '';
            }
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-title-header-wrap {
        align-items: center;
        display: flex;
        min-height: 1.6875rem;
        position: relative;

        @include hover-style();
        @include category-style();

        &.is-highlighted::before {
            background: hsla(230, 15%, 97%, 1);
        }

        &.is-selected::before {
            background: hsla(230, 15%, 94%, 1);
        }
    }

    .treenode-title-header {
        flex: 1;
    }

    .treenode-title {
        max-width: 13.875rem;
        z-index: 1;
    }

    .criterium-title.mod-no-category {
        margin-left: .15625rem;
    }
</style>