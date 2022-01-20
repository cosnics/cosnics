<i18n>
{
    "en": {
        "date-time-at": "at",
        "level": "Level",
        "score-weight": "Score on grand total with the given weight"
    },
    "fr": {
        "date-time-at": "à",
        "level": "Niveau",
        "score-weight": "Score sur le total général avec le poids donné"
    },
    "nl": {
        "date-time-at": "om",
        "level": "Niveau",
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
                <span class="treenode-evaluator-score">{{ score|formatNum }}<template v-if="useRelativeWeights"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template></span>
                <span v-if="useRelativeWeights" :title="$t('score-weight')" class="treenode-evaluator-rel-score">{{ weightedScore|formatNum }}<template v-if="useRelativeWeights"><i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template></span>
            </template>
        </div>
        <template v-if="isCriterium">
            <span class="level-txt">{{ $t('level') }}:</span>
            <div class="treenode-evaluator-level-title" :class="{'mod-pointer': treeNodeLevelDescription}" @click.stop="descriptionVisible = !descriptionVisible">{{ level.title }}</div>
            <div v-if="descriptionVisible && treeNodeLevelDescription" class="treenode-evaluator-level-description">{{ treeNodeLevelDescription }}</div>
        </template>
        <div class="treenode-evaluator-feedback" v-if="feedback"><i class="fa fa-comment-o" aria-hidden="true"></i>{{ feedback }}</div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';

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
                if (v === null) { return '0'; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeEvaluatorResults extends Vue {
        private descriptionVisible = false;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster|Category|Criterium;
        @Prop({type: Object}) readonly evaluator!: any;
        @Prop({type: Number, default: null}) readonly score!: number|null;
        @Prop({type: Level}) readonly level!: Level;
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
            if (this.treeNode instanceof Criterium) {
                return this.rubric.getChoice(this.treeNode, this.level).feedback;
            }
            return '';
        }

        get weightedScore() {
            if (this.score === null) { return 0; }
            return this.rubric.getRelativeWeight(this.treeNode) * this.score / 100;
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

.treenode-evaluator-level-description {
    background: hsla(180, 45%, 98%, 1);
    border: 1px solid hsla(180, 38%, 94%, 1);
    margin: -.25rem -.5rem 1.2rem -.5rem;
    padding: 0 0.5rem;
}

.treenode-evaluator-feedback {
    margin-bottom: 10px;
    margin-top: -1rem;
    padding: .6rem 0;
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

.fa-comment-o {
    color: hsl(190, 33%, 85%);
    float: left;
    font-size: 1.8rem;
    margin-right: 1rem;
    margin-top: .25rem;
}
</style>