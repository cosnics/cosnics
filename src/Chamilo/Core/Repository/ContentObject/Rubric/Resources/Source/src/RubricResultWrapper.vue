<template>
    <div class="container-fluid">
        <rubric-result :api-config="config" :version="version" :rubric-data="rubricData" :rubric-results="rubricResults"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricResult from './RubricResult.vue';

    @Component({
        components: {
            RubricResult
        },
    })
    export default class RubricResultWrapper extends Vue {
        private config: any = {
            '...URL': 'https://test'
        };
        private version = 0;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly rubricResults!: object|null;
    }
</script>
