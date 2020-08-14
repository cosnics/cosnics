<template>
  <div class="container-fluid">
      <rubric-result v-if="rubric" :rubric="rubric" :evaluators="[]" :criterium-results="[]"></rubric-result>
  </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import TreeNode from '../Domain/TreeNode';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import RubricResult from './RubricResult.vue';
    import {convertRubricData} from '../Util/util';
    import {CriteriumResult} from '../Util/interfaces';

    @Component({
        components: {
            RubricResult
        }
    })
    export default class RubricResultWrapper extends Vue {
        private rubric: Rubric | undefined;
        private criteriumResults: CriteriumResult[] = [];

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Object, required: true}) readonly rubricResults!: object;

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            const rubric = this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            const treeNodes = rubric.getAllTreeNodes();
            /*const criteria = treeNodes.filter(treeNode => treeNode.getType() === 'criterium') as Criterium[];
            criteria.map(criterium => {
                return criterium;
            });
            console.log('results', this.rubricResults);
            console.log(rubric); */
        }

        created() {
            this.initData();
        }
    }
</script>