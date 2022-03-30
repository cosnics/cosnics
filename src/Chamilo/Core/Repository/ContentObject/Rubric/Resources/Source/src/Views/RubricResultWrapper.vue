<template>
    <div id="app" v-if="rubric">
        <rubric-result :rubric="rubric" :rubric-evaluation="rubricEvaluation" :rubric-entry-url="rubricEntryUrl"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import RubricResult from './RubricResult.vue';
    import {convertRubricData} from '../Util/util';

    @Component({
        components: {
            RubricResult
        }
    })
    export default class RubricResultWrapper extends Vue {
        private rubric: Rubric | undefined;
        private rubricEvaluation: RubricEvaluation | undefined;

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Array, required: true}) readonly rubricResults!: any[];
        @Prop({type: String, default: ''}) readonly rubricEntryUrl!: string;

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            this.rubricEvaluation = RubricEvaluation.fromRubricResults(this.rubric, this.rubricResults);
        }

        created() {
            this.initData();
        }
    }
</script>