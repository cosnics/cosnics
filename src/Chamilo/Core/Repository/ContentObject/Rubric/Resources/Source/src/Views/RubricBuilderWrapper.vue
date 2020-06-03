<template>
    <div class="container-fluid">
        <rubric-builder :api-config="config" :rubric-data="store.rubricData" :version="version" :ui-state="store.uiState.builder" @rubric-updated="onRubricUpdated"></rubric-builder>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilder from './RubricBuilder.vue';
    import store from '../store';

    @Component({
        components: {
            RubricBuilder
        },
    })
    export default class RubricBuilderWrapper extends Vue {
        private config: any = {
            'addLevelURL': 'https://test',
            'addTreeNodeURL': 'https://test',
            'deleteLevelURL': 'https://test',
            'deleteTreeNodeURL': 'https://test',
            'moveLevelURL': 'https://test',
            'moveTreeNodeURL': 'https://test',
            'updateChoiceURL': 'https://test',
            'updateLevelURL': 'https://test',
            'updateTreeNodeURL': 'https://test'
        };
        private version = 0;
        private store: any = store;

        onRubricUpdated(data: any) {
            for (let member in this.store.rubricData) {
                delete this.store.rubricData[member];
            }
            Object.assign(this.store.rubricData, data);
        }
    }
</script>