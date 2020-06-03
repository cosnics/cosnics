<template>
    <div class="container-fluid">
        <rubric-result :api-config="config" :version="version" :rubric-data="store.rubricData" :rubric-results="store.rubricResults"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricResult from './RubricResult.vue';
    import store from '../store';

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
        private store: any = store;
    }
</script>
