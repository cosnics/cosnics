<template>
    <div class="container-fluid">
        <rubric-result :rubric-data="store.rubricData" :rubric-results="store.rubricResults"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import RubricResult from './RubricResult.vue';
    import store from '../store';

    @Component({
        components: {
            RubricResult
        },
    })
    export default class RubricResultDemoWrapper extends Vue {
        private store: any = store;
    }
</script>
