<template>
    <div class="container-fluid">
        <rubric-entry :api-config="config" :version="version" :rubric-data="rubricData" :rubric-results="rubricResults"></rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricEntry from './RubricEntry.vue';

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

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly rubricResults!: object|null;
    }
</script>
