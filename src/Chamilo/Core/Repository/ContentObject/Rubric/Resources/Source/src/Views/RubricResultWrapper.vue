<template>
    <rubric-result v-if="rubric" :rubric="rubric" :evaluators="evaluators" :tree-node-results="treeNodeResults"></rubric-result>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricResult from './RubricResult.vue';
    import {convertRubricData} from '../Util/util';
    import {TreeNodeEvaluation, TreeNodeResult} from '../Util/interfaces';

    @Component({
        components: {
            RubricResult
        }
    })
    export default class RubricResultWrapper extends Vue {
        private rubric: Rubric | undefined;
        private treeNodeResults: TreeNodeResult[] = [];
        private evaluators: any[] = [];

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Array, required: true}) readonly rubricResults!: any[];

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            const rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            const evaluators = this.rubricResults.map((res : any) =>
                ({ userId: res.user.id, name: res.user.name, role: res.user.role, targetUserId: res['target_user'].id, targetName: res['target_user'].name, date: res.date })
            );
            const r_evaluations = this.rubricResults.map((res : any) => res.results);

            this.treeNodeResults = rubric.getAllTreeNodes().map(treeNode => {
                const defaultEvaluation: TreeNodeEvaluation = { treeNode, level: null, score: 0, feedback: '' };
                const evaluations = evaluators.map((evaluator : any, index: number) => {
                    const treeNodeEvaluation: TreeNodeEvaluation = {...defaultEvaluation};
                    const evaluations = r_evaluations[index];
                    const treeNodeEvaluationInput = evaluations.find((o: any) => String(o['tree_node_id']) === treeNode.id);
                    if (treeNodeEvaluationInput) {
                        const chosenLevel = rubric.levels.find(level => level.id === String(treeNodeEvaluationInput['level_id']));
                        treeNodeEvaluation.level = chosenLevel || null;
                        treeNodeEvaluation.score = treeNodeEvaluationInput.score;
                        treeNodeEvaluation.feedback = treeNodeEvaluationInput.comment;
                    }
                    return { evaluator, treeNodeEvaluation };
                });
                return { treeNode, evaluations };
            });
            this.evaluators = evaluators;
            this.rubric = rubric;
        }

        created() {
            this.initData();
        }
    }
</script>