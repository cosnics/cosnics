<template>
    <div id="app" style="margin-top: -20px">
        <rubric-entry v-if="rubric" :rubric="rubric" :preview="true" :ui-state="store.uiState.preview" :options="{ isPreviewDemo: true }"></rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricEntry from './RubricEntry.vue';
    import store from '../store';

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricPreviewDemoWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;

        created() {
            this.rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
        }
    }
</script>
