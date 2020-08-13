import Criterium from '../Domain/Criterium';
import Level from '../Domain/Level';

export interface CriteriumEvaluation {
    criterium: Criterium;
    level: Level|null;
    score: number;
    feedback: string;
}

export interface CriteriumExt {
    criterium: Criterium;
    choices: any[];
    showDefaultFeedback: false;
}

export interface CriteriumResult {
    criterium: Criterium,
    evaluations: any;
}
