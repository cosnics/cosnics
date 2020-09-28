<template>
    <div style="margin: -20px 0">
        <rubric-builder :api-config="config" :version="null" :rubric-data="store.rubricData"  :ui-state="store.uiState.builder" @rubric-updated="onRubricUpdated"></rubric-builder>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import RubricBuilder from './RubricBuilder.vue';
    import store from '../store';
    //import store from '../store2';

    @Component({
        components: {
            RubricBuilder
        },
    })
    export default class RubricBuilderDemoWrapper extends Vue {
        private config: any = {
            'addLevelURL': 'https://test',
            'addTreeNodeURL': 'https://test',
            'deleteLevelURL': 'https://test',
            'deleteTreeNodeURL': 'https://test',
            'moveLevelURL': 'https://test',
            'moveTreeNodeURL': 'https://test',
            'updateChoiceURL': 'https://test',
            'updateLevelURL': 'https://test',
            'updateTreeNodeURL': 'https://test',
            'updateRubricURL': 'https://test'
        };
        private store: any = store;

        onRubricUpdated(rubric: Rubric) {
            for (let member in this.store.rubricData) {
                delete this.store.rubricData[member];
            }
            Object.assign(this.store.rubricData, rubric.toJSON());
        }
    }
</script>