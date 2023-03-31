<i18n>
{
    "en": {
        "date-time-at": "at",
        "level": "Level",
        "no-level": "No level given",
        "no-score": "No score",
        "score-weight": "Score on grand total with the given weight"
    },
    "fr": {
        "date-time-at": "à",
        "level": "Niveau",
        "no-level": "No level given",
        "no-score": "No score",
        "score-weight": "Score sur le total général avec le poids donné"
    },
    "nl": {
        "date-time-at": "om",
        "level": "Niveau",
        "no-level": "geen niveau opgegeven",
        "no-score": "geen score",
        "score-weight": "Score op eindtotaal met het gegeven gewicht"
    }
}
</i18n>

<template>
    <div class="treenode-evaluator-results">
        <div class="treenode-evaluator-meta" :class="{'mod-btm': !isCriterium}">
            <div class="m-flex-1">
                <span class="treenode-evaluator-name">{{ evaluator.name|capitalize }}</span>
                <time class="treenode-evaluator-date">{{ evaluator.date|formatDate($t('date-time-at')) }}</time>
            </div>
            <template v-if="useScores">
                <template v-if="score !== null">
                    <span class="treenode-evaluator-score">{{ score|formatNum }}<template v-if="useRelativeWeights"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template></span>
                    <span v-if="useRelativeWeights" :title="$t('score-weight')" class="treenode-evaluator-rel-score">{{ weightedScore|formatNum }}<template v-if="useRelativeWeights"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template></span>
                </template>
                <span v-else class="m-no-score">{{ $t('no-score') }}</span>
            </template>
        </div>
        <template v-if="isCriterium">
            <template v-if="level">
                <div class="treenode-evaluator-level-title">
                    {{ level.title }}
                    <span v-if="useScores && level.useRangeScore" :title="`${level.minimumScore} > ${level.score}`" style="color: #5a92b5"><i class="fa fa-info-circle"></i></span>
                </div>
                <!--<div v-if="level.useRangeScore" class="treenode-evaluator-score-range"><span>{{ level.minimumScore}} <i class="fa fa-caret-right"></i> {{ level.score }}<template v-if="useRelativeWeights"><span class="sr-only">%</span><i aria-hidden="true" class="fa fa-percent"></i></template></span></div>-->
                <div v-if="treeNodeLevelDescription" class="treenode-evaluator-level-description" v-html="toMarkDown(treeNodeLevelDescription)"></div>
            </template>
            <span v-else class="m-no-score">{{ $t('no-level') }}</span>
        </template>
        <div class="treenode-evaluator-feedback" v-if="feedback"><i class="fa fa-comment" aria-hidden="true"></i><i class="fa fa-comment-o" aria-hidden="true"></i>{{ feedback }}</div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../../Domain/Rubric';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';
    import Level from '../../Domain/Level';
    import {toMarkdown} from '../../Util/util';

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            formatDate: function (s: string, atStr: string = '') {
                const date = new Date(s);
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear().toString().substr(-2)} ${atStr} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            },
            formatNum: function (v: number|null) {
                if (v === null) { return ''; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeEvaluatorResults extends Vue {

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster|Category|Criterium;
        @Prop({type: Object}) readonly evaluator!: any;
        @Prop({type: Number, default: null}) readonly score!: number|null;
        @Prop({type: Level}) readonly level!: Level|null;
        @Prop({type: String, default: ''}) readonly feedback!: string;

        get useScores() {
            return this.rubric.useScores;
        }

        get useRelativeWeights() {
            return this.rubric.useRelativeWeights;
        }

        get isCriterium() {
            return this.treeNode instanceof Criterium;
        }

        get treeNodeLevelDescription() {
            if (this.level !== null && this.treeNode instanceof Criterium) {
                if (this.level.criteriumId === this.treeNode.id) {
                    return this.level.description;
                }
                return this.rubric.getChoice(this.treeNode, this.level).feedback;
            }
            return '';
        }

        get weightedScore() {
            if (this.score === null) { return 0; }
            return this.rubric.getRelativeWeight(this.treeNode) * this.score / 100;
        }

        toMarkDown(s: string) {
            return toMarkdown(s);
        }
    }
</script>

<style lang="scss">
.treenode-evaluator-results {
    border-radius: 3px;
    display: flex;
    flex-direction: column;
    padding: 1rem 1rem 0;
}

.treenode-evaluator-meta {
    align-items: baseline;
    display: flex;
    gap: 1rem;
    justify-content: space-between;

    &.mod-btm {
        margin-bottom: 1rem;
    }
}

.treenode-evaluator-name {
    color: #486d84;
    display: block;
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    padding: .2rem 0 0;
}

.treenode-evaluator-date {
    color: #51788c;
    font-size: 1.2rem;
    font-weight: 400;
}

.treenode-evaluator-score {
    font-size: 1.55rem;
    white-space: nowrap;
}

.treenode-evaluator-rel-score {
    border-bottom: 1px dotted #999;
    line-height: 1.2;
    margin-left: .5rem;
    pointer-events: all;
    white-space: nowrap;
}

.treenode-evaluator-level-title {
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 1rem;
    pointer-events: all;

    &.mod-pointer {
        cursor: pointer;
    }
}

.treenode-evaluator-score-range {
    margin-top: -1rem;
    margin-bottom: 1rem;

    .fa-caret-right {
        color: #666;
        margin: 0 -.25rem;
    }
}

.treenode-evaluator-level-description {
/*    background: hsla(180, 45%, 98%, 1);
    border: 1px solid hsla(180, 38%, 94%, 1);
    margin: -.25rem -.5rem 1.2rem -.5rem;
    padding: 0 0.5rem;*/
}

.treenode-evaluator-feedback {
    border-top: 1px dotted #bdd7db;
    margin-bottom: 10px;
    margin-top: 0;
    padding: .6rem 1.3rem;
    position: relative;
}
</style>

<style scoped>
.m-flex-1 {
    flex: 1;
}

.level-txt {
    color: #808080;
    font-weight: 700;
    margin-top: .25rem;
}

.fa-percent {
    font-size: 1.1rem;
    color: #666;
}

.fa-comment {
    color: #f6f7f8;
    font-size: 24px;
    left: -1.7rem;
    position: absolute;
    top: 4px;
}

.fa-comment-o {
    color: #c9dbde;
    font-size: 24px;
    left: -1.7rem;
    position: absolute;
    top: 4px;
}

.m-no-score {
    font-style: oblique;
    color: hsl(190, 32%, 39%);
}
</style>