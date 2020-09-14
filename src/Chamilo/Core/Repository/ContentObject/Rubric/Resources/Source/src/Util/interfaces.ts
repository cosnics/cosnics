import TreeNode from '../Domain/TreeNode';
import Level from '../Domain/Level';

export interface TreeNodeEvaluation {
    treeNode: TreeNode;
    level: Level|null;
    score: number|null;
    feedback: string;
}

export interface TreeNodeExt {
    treeNode: TreeNode;
    choices: any[];
    showDefaultFeedback: false;
}

export interface EvaluatorEvaluation {
    evaluator: any;
    treeNodeEvaluation: TreeNodeEvaluation;
}

export interface TreeNodeResult {
    treeNode: TreeNode,
    evaluations: EvaluatorEvaluation[];
}
