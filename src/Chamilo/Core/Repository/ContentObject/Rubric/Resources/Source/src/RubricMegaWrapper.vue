<template>
    <rubric-builder-wrapper v-if="content === 'builder'"></rubric-builder-wrapper>
    <rubric-builder-full-wrapper v-else-if="content === 'builder-full'"></rubric-builder-full-wrapper>
    <rubric-entry-wrapper v-else-if="content === 'entry'"></rubric-entry-wrapper>
    <rubric-result-wrapper v-else-if="content === 'results'"></rubric-result-wrapper>
    <div v-else class="demo-page">Rubric Demo Page</div>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilderWrapper from './RubricBuilderWrapper.vue';
    import RubricBuilderFullWrapper from './RubricBuilderFullWrapper.vue';
    import RubricEntryWrapper from './RubricEntryWrapper.vue';
    import RubricResultWrapper from './RubricResultWrapper.vue';

    @Component({
        components: {
            RubricBuilderWrapper, RubricBuilderFullWrapper, RubricEntryWrapper, RubricResultWrapper
        },
    })
    export default class RubricMegaWrapper extends Vue {
        @Prop({type: String, required: true}) readonly content!: string;
    }
</script>
<style lang="scss">
    .demo-page {
        font-size: 1.8rem;
        margin-left: 1em;
    }

    .chamilo-header {
        .rubrics-menu {
            display: flex;
            margin-top: 40px;
            list-style: none;
            cursor: pointer;
        }

        .rubrics-menu-item {
            margin-right: 10px;

            &.selected {
                margin-right: 10px;
            }

            &:not(:first-child) {
                padding-left: 10px;
            }

            a {
                padding-bottom: 4px;
                text-decoration: none;
                color: white;
            }

            &.selected a {
                color: white;
                border-bottom: 1px solid white;
            }

            &:hover a {
                border-bottom: 1px solid yellowgreen;
            }
        }
    }
</style>