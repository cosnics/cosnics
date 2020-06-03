<template>
    <div class="container-fluid">
        <rubric-entry :api-config="config" :version="version" :rubric-data="store.rubricData" :rubric-results="store.rubricResults" :ui-state="store.uiState.entry"></rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricEntry from './RubricEntry.vue';
    import store from '../store';

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricEntryWrapper extends Vue {
        private config: any = {
            '...URL': 'https://test'
        };
        private version = 0;
        private store: any = store;
    }
</script>
