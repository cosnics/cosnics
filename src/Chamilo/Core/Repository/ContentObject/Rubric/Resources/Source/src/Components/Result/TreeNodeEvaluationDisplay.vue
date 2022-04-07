<i18n>
{
    "en": {
        "na": "n/a"
    },
    "fr": {
        "na": "n/a"
    },
    "nl": {
        "na": "n.b."
    }
}
</i18n>

<template>
    <div v-if="showMax" class="treenode-evaluation" :class="['mod-' + nodeType + '-max']">
        <score-display :score="score" />
    </div>
    <div v-else class="treenode-evaluation" :class="['mod-' + nodeType, {'mod-incomplete': isIncomplete, 'mod-grades': isGradeMode, 'mod-feedback': !!feedback}]">
        <i v-if="feedback" :title="titleOverlay" class="treenode-feedback-icon fa fa-info" :class="{'mod-cluster': isCluster && !isGradeMode && !isIncomplete}" />
        <span v-if="isNA" class="m-not-scored">{{ $t('na') }}</span>
        <score-display v-if="showScore" :score="score" :percent="rubric.useRelativeWeights" />
        <template v-else-if="isCriterium">{{ levelTitle }}</template>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import ScoreDisplay from './ScoreDisplay.vue';

    @Component({
        components: {ScoreDisplay}
    })
    export default class TreeNodeEvaluationDisplay extends Vue {

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Rubric, Cluster, Category, Criterium]}) readonly treeNode!: Rubric|Cluster|Category|Criterium;
        @Prop({type: Number, default: null}) readonly score!: number|null;
        @Prop({type: Boolean, default: true}) readonly showScores!: boolean;
        @Prop({type: String, default: ''}) readonly feedback!: string;
        @Prop({type: String, default: ''}) readonly titleOverlay!: string;
        @Prop({type: Level, default: null}) readonly level!: Level|null;
        @Prop({type: Boolean, default: false}) readonly showMax!: boolean;

        get nodeType() {
            return this.treeNode.getType();
        }

        get isRubric() {
            return this.nodeType === 'rubric';
        }

        get isCluster() {
            return this.nodeType === 'cluster';
        }

        get isCategory() {
            return this.nodeType === 'category';
        }

        get isCriterium() {
            return this.nodeType === 'criterium';
        }

        get useScores() {
            return this.rubric.useScores;
        }

        get useAbsoluteScores() {
            return this.rubric.useScores && !this.rubric.useRelativeWeights;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        get isGradeMode() {
            return this.useGrades || (this.useScores && this.rubric.useRelativeWeights && !this.showScores);
        }

        get showScore() {
            return this.useScores && (!this.rubric.useRelativeWeights || this.showScores)
        }

        get isNA() {
            switch (this.nodeType) {
                case 'cluster':
                case 'category':
                    return this.showScore && this.score === null;
                case 'criterium':
                    return this.useScores ? this.score === null : this.level === null;
                case 'rubric':
                    return this.score === null;
            }
        }

        get isIncomplete() {
            return this.score === null && !this.isGradeMode;
        }

        get levelTitle() {
            return this.level?.title || '';
        }


    }
</script>

<style lang="scss" scoped>
    .m-not-scored {
        color: hsl(190, 33%, 50%);
        font-size: 1.2rem;
        font-style: oblique;
    }

    >>> .fa-percent {
        font-size: 1rem;
        opacity: .75;
    }

    span {
        white-space: nowrap;
    }

    .treenode-evaluation {
        border-radius: $border-radius;
        color: #666;
        flex: 1;
        font-size: 1.6rem;
        padding: .2rem .7rem;
        text-align: right;

        &.mod-grades {
            font-size: 1.2rem;
            overflow: hidden;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;

            &::after {
                content: '';
                display: inline-block;
            }

            &:not(.mod-feedback) {
                &.mod-cluster, &.mod-category {
                    opacity: 0;
                }
            }
        }

        &.mod-rubric {
            background: $score-darker;
            color: #fff;
        }

        &.mod-cluster.mod-grades {
            background: #fff;
            border: 1px solid hsla(190, 30%, 95%, 1);
        }

        &.mod-cluster:not(.mod-grades) {
            background: $score-dark;
            color: #fff;
        }

        &.mod-category.mod-grades {
            background: #fff;
            border: 1px solid hsla(190, 30%, 95%, 1);
        }

        &.mod-category:not(.mod-grades) {
            background: $score-light;
        }

        &.mod-criterium {
            background-color: #fafafa;
            border: 1px solid #deebee;
        }

        &.mod-rubric, &.mod-cluster, &.mod-category, &.mod-criterium {
            &.mod-incomplete:not(.mod-grades), &.mod-incomplete.mod-grades {
                background: none;
                border: 1px dashed hsla(190, 33%, 59%, .47);
                padding: 0.05rem 0.7rem 0;
            }
        }

        &.mod-rubric-max {
            background: hsla(207, 40%, 35%, 1);
            color: #fff;
        }

        &.mod-cluster-max {
            background: hsla(203, 33%, 60%, 1);
            color: #fff;
        }

        &.mod-category-max {
            background: hsla(203, 32%, 83%, 1);
        }

        &.mod-criterium-max {
            background: hsla(213, 30%, 93%, 1);
        }
    }
</style>