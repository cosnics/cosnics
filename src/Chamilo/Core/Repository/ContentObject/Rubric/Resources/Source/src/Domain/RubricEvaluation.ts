import TreeNode from './TreeNode';
import Rubric from './Rubric';
import Cluster from './Cluster';
import Category from './Category';
import Criterium from './Criterium';
import {EvaluatorEvaluation, TreeNodeEvaluation, TreeNodeResult} from '../Util/interfaces';

function add(v1: number, v2: number) {
    return v1 + v2;
}

enum EvaluationMode {
    Entry,
    Result
}

export default class RubricEvaluation {
    private mode: EvaluationMode;
    private rubric: Rubric;
    private evaluators: any[] = [];
    private treeNodeResults: TreeNodeResult[] = [];
    private treeNodeEvaluations: TreeNodeEvaluation[] = [];

    constructor(mode: EvaluationMode, rubric: Rubric, options: any = {}) {
        this.mode = mode;
        this.rubric = rubric;
        if (mode === EvaluationMode.Entry) {
            if (options.treeNodeEvaluations) { this.treeNodeEvaluations = options.treeNodeEvaluations; }
        } else if (mode === EvaluationMode.Result) {
            if (options.evaluators) { this.evaluators = options.evaluators; }
            if (options.treeNodeResults) { this.treeNodeResults = options.treeNodeResults; }
        }
    }

    static fromEntry(rubric: Rubric, treeNodeEvaluations: TreeNodeEvaluation[]) : RubricEvaluation {
        return new RubricEvaluation(EvaluationMode.Entry, rubric, {treeNodeEvaluations});
    }

    static fromResults(rubric: Rubric, evaluators: any[], treeNodeResults: TreeNodeResult[]) : RubricEvaluation {
        return new RubricEvaluation(EvaluationMode.Result, rubric, {evaluators, treeNodeResults});
    }

    static fromRubricResults(rubric: Rubric, rubricResults: any) : RubricEvaluation {
        const evaluators = rubricResults.map((res: any) =>
            ({userId: res.user.id, name: res.user.name, role: res.user.role, date: res.date})
        );

        const r_evaluations = rubricResults.map((res: any) => res.results);
        const treeNodeResults = rubric.getAllTreeNodes().map(treeNode => {
            const defaultEvaluation: TreeNodeEvaluation = {treeNode, level: null, score: 0, feedback: ''};
            const evaluations = evaluators.map((evaluator: any, index: number) => {
                const treeNodeEvaluation: TreeNodeEvaluation = {...defaultEvaluation};
                const evaluations = r_evaluations[index];
                const treeNodeEvaluationInput = evaluations.find((o: any) => String(o['tree_node_id']) === treeNode.id);
                if (treeNodeEvaluationInput) {
                    const chosenLevel = rubric.levels.find(level => level.id === String(treeNodeEvaluationInput['level_id']));
                    treeNodeEvaluation.level = chosenLevel || null;
                    treeNodeEvaluation.score = treeNodeEvaluationInput.score;
                    treeNodeEvaluation.feedback = treeNodeEvaluationInput.comment;
                }
                return {evaluator, treeNodeEvaluation};
            });
            return {treeNode, evaluations};
        });

        return this.fromResults(rubric, evaluators, treeNodeResults);
    }

    getEvaluators() {
        return this.evaluators;
    }

    getCriteriumScore(criterium: Criterium, evaluator: any|undefined = undefined) : number {
        return this.getTreeNodeEvaluation(criterium, evaluator)?.score || 0;
    }

    getCategoryScore(category: Category, evaluation: any|undefined = undefined) : number {
        if (this.mode === EvaluationMode.Result && typeof evaluation?.score === 'number') {
            return evaluation.score;
        } else {
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium, evaluation?.evaluator)).reduce(add, 0);
        }
    }

    getClusterScore(cluster: Cluster, evaluation: any|undefined = undefined) : number {
        if (this.mode === EvaluationMode.Result && typeof evaluation?.score === 'number') {
            return evaluation.score;
        } else {
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium, evaluation?.evaluator)).reduce(add, 0);
        }
    }

    getRubricScore(evaluator: any|undefined = undefined) : number {
        const treeNodeScore = this.getTreeNodeEvaluation(this.rubric, evaluator)?.score;
        if (this.mode === EvaluationMode.Result && typeof treeNodeScore === 'number') {
            return treeNodeScore;
        } else {
            return this.rubric.getAllCriteria().map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }
    }

    private getTreeNodeResult(treeNode: TreeNode) : TreeNodeResult {
        const treeNodeResult = this.treeNodeResults.find((_ : TreeNodeResult) => _.treeNode === treeNode);
        if (!treeNodeResult) { throw new Error(`No data found for: ${treeNode}`); }
        return treeNodeResult;
    }

    getTreeNodeEvaluation(treeNode: TreeNode, evaluator: any|undefined = undefined) : TreeNodeEvaluation | null {
        if (this.mode === EvaluationMode.Entry) {
            return this.treeNodeEvaluations.find((_ : TreeNodeEvaluation) => _.treeNode === treeNode) || null;
        } else { // this.mode === EvaluationMode.Result
            const evaluatorEvaluation = this.getTreeNodeResult(treeNode).evaluations.find((_ : EvaluatorEvaluation) => _.evaluator === evaluator);
            if (!evaluatorEvaluation) { throw new Error(`No evaluation found for: ${treeNode} and evaluator: ${evaluator && evaluator.name}`); }
            return evaluatorEvaluation.treeNodeEvaluation;
        }
    }

    getEvaluations(treeNode: TreeNode): any[] {
        return this.getTreeNodeResult(treeNode).evaluations.map(_ => ({evaluator: _.evaluator, ..._.treeNodeEvaluation}));
    }
}
