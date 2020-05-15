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
<style>
    .demo-page {
        font-size: 1.8rem;
        margin-left: 1em;
    }

    .chamilo-header .builder {
        display: flex;
        margin-top: 40px;
        list-style: none;
        cursor: pointer;
    }

    .chamilo-header .builder-list-item {
        margin-right: 10px;
    }

    .chamilo-header .builder-list-item.selected {
        margin-right: 10px;
    }

    .chamilo-header .builder-list-item:not(:first-child) {
        padding-left: 10px;
    }

    .chamilo-header .builder-list-item a {
        padding-bottom: 4px;
        text-decoration: none;
        color: white;
    }

    .chamilo-header .builder-list-item.selected a {
        color: white;
        border-bottom: 1px solid white;
    }

    .chamilo-header .builder-list-item:hover a {
        border-bottom: 1px solid yellowgreen;
    }

    .chamilo-header {
        min-height: 78px;
        display: flex;
        flex-direction: row;
        overflow: hidden;
    }
    .chamilo-header .start {
        background-image: url("/images/chamilo-start.png");
        width: 168px;
    }
    .chamilo-header .middle {
        background-image: url("/images/chamilo-middle.png");
        flex: 1;
    }
    .chamilo-header .end {
        background-image: url("/images/chamilo-end.png");
        width: 713px;
    }
    @media (max-width: 900px) {
        .chamilo-header {
            min-height: 51px;
        }
        .chamilo-header .start {
            background-image: url("/images/chamilo-start-alt.png");
            width: 176px;
        }
        .chamilo-header .middle {
            background-image: url("/images/chamilo-middle-alt.png");
        }
        .chamilo-header .end {
            background-image: url("/images/chamilo-end-alt.png");
            width: 70px;
        }
    }
    .container-breadcrumb {
        background-color: #f5f5f5;
        margin-bottom: 20px;
    }
    .container-breadcrumb ol.breadcrumb {
        margin-bottom: 0;
        padding-left: 0;
        padding-right: 0;
    }
    footer.chamilo-footer {
        margin-top: 20px;
        padding: 15px 0;
        font-size: 90%;
        text-align: right;
        border-top: 1px solid #dddddd;
    }
</style>