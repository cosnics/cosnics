import Rubric from "./Rubric";
import Criterium, {CriteriumId} from "./Criterium";
import Container from "./TreeNode";

export default class CriteriumSequence {
    /*protected criteriumCounter = 0;
    protected criteriumSequenceNumberMap: Map<CriteriumId, number> = new Map<CriteriumId, number>();
    protected criteriumSequence: Criterium[] = [];

    public buildSequence(rubric: Rubric) {
        this.criteriumCounter = 0;
        this.criteriumSequenceNumberMap = new Map<CriteriumId, number>();
        this.criteriumSequence = [];
        this.buildSequenceRecursive(rubric);
    }

    protected buildSequenceRecursive(container: TreeNode) {
        if ( container instanceof Criterium) {
            this.criteriumSequence.push(container);
            this.criteriumSequenceNumberMap.set(container.id, this.criteriumCounter);
            this.criteriumCounter++;
        }
        container.getChildren().forEach(this.buildSequenceRecursive);
    }

    public getNextCriterium(criterium: Criterium) {
        const sequenceNumber = this.criteriumSequenceNumberMap.get(criterium.id);
        if ( sequenceNumber === undefined) {
            throw new Error('Unknown criterium: ' + criterium);
        }

        return this.criteriumSequence[sequenceNumber + 1];
    }

    public getPreviousCriterium(criterium: Criterium) {
        const sequenceNumber = this.criteriumSequenceNumberMap.get(criterium.id);
        if ( sequenceNumber === undefined) {
            throw new Error('Unknown criterium: ' + criterium);
        }

        return this.criteriumSequence[sequenceNumber + 1];
    }*/
}
