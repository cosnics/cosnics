<template>
    <div class="criterium-details" v-if="criterium !== null">
        <div v-if="criterium">
            <div class="criterium-details-header" style="">
                <div class="criterium-details-title">
                    <label for="title">Criterium: </label>
                    <input type="text" v-model="criterium.title" id="title" name="title" autocomplete="off" class="input-detail" />
                </div>
                <button class="btn-close" @click="$emit('close')"><i class="fa fa fa-close" /><span>Sluit</span></button>
            </div>
            <div class="criterium-path">{{ criterium.parent.parent.parent.title}} > {{ criterium.parent.parent.title}} <span v-if="criterium.parent.color !== ''"> > {{ criterium.parent.title }}</span></div>
            <div class="criterium-weight"><label for="weight">Gewicht:</label> <input type="number" id="weight" v-model="criterium.weight" class="input-detail"/> %</div>
            <ul class="criterium-levels">
                <li v-for="level in rubric.levels" :key="level.id" class="criterium-level">
                    <div class="criterium-level-title">{{ level.title }} <span v-if="level.description" class="fa fa-question-circle criterium-level-description" :title="level.description"></span></div>
                    <div class="criterium-level-input">
                        <textarea v-model="rubric.getChoice(criterium, level).feedback" class="criterium-level-feedback input-detail"
                                  placeholder="Geef feedback"
                                  @input="updateHeight"></textarea>
                        <div v-if="rubric.useScores" class="criterium-level-score">
                            <div v-if="hasFixedScore(criterium, level)" class="remove-fixed" @click="removeFixedScore(criterium, level)"><i class="fa fa-lock" /><i class="fa fa-unlock" /></div>
                            <input class="fixed-score input-detail" type="number" step="0.1" v-if="hasFixedScore(criterium, level)" v-model="rubric.getChoice(criterium, level).fixedScore"/>
                            <input type="number" class="input-detail" step="0.1" v-else v-model="rubric.getChoiceScore(criterium, level)" @input="changeChoiceScore($event, criterium, level)"/>
                        </div>
                    </div>
                </li>
            </ul>
            <a href="#" role="button" @click.prevent="$emit('close')" class="rubric-return"><i class="fa fa-arrow-left"/> Terug naar rubric</a>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop, Watch} from 'vue-property-decorator';
    import Rubric from '../../Domain/Rubric';
    import Level from '../../Domain/Level';
    import Criterium from '../../Domain/Criterium';
    import Choice from '../../Domain/Choice';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: {  }
    })
    export default class ScoreRubricView extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(Criterium) readonly criterium!: Criterium | null;

        updateHeight(e: InputEvent) {
            updateHeight(e.target as HTMLElement);
        }

        updated() {
            for (let elem of document.getElementsByClassName('criterium-level-feedback')) {
                updateHeight(elem as HTMLElement);
            }
        }

        hasFixedScore(criterium: Criterium, level: Level) : boolean {
            return this.rubric.getChoice(criterium, level).hasFixedScore;
        }

        removeFixedScore(criterium: Criterium, level: Level) {
            const choice = this.rubric.getChoice(criterium, level);
            choice.hasFixedScore = false;
            choice.fixedScore = Choice.FIXED_SCORE;
            this.$forceUpdate();
        }

        changeChoiceScore(event: any, criterium: Criterium, level: Level) {
            const value = parseFloat(event.target.value);
            if (!isNaN(value)) {
                const choice = this.rubric.getChoice(criterium, level);
                choice.hasFixedScore = true;
                choice.fixedScore = value;
                this.$forceUpdate();
            }
        }
    }
</script>

<style scoped>
     * {
        outline: none;
    }
</style>