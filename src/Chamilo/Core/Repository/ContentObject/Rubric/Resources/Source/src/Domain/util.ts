import Criterium from './Criterium';
import Level from './Level';

interface CriteriumEvaluation {
    criterium: Criterium;
    level: Level|null;
    score: number;
    feedback: string;
}

interface CriteriumExt {
    criterium: Criterium;
    choices: any[];
    showDefaultFeedback: false;
}

export { CriteriumEvaluation, CriteriumExt }