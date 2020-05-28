<template>
    <div class="criterium-details" v-if="criterium !== null">
        <div v-if="criterium">
            <div class="criterium-details-header" style="">
                <div class="criterium-details-title">
                    <label for="title">Criterium: </label>
                    <input type="text" v-model="criterium.title" id="title" name="title" autocomplete="off" class="input-detail" @input="onCriteriumChange"/>
                </div>
                <button class="btn-close" @click="$emit('close')"><i class="fa fa fa-close" /><span>Sluit</span></button>
            </div>
            <div class="criterium-path">{{ criterium.parent.parent.parent.title}} > {{ criterium.parent.parent.title}} <span v-if="criterium.parent.color !== ''"> > {{ criterium.parent.title }}</span></div>
            <div class="criterium-weight"><label for="weight">Gewicht:</label> <input type="number" id="weight" v-model="criterium.weight" class="input-detail" @input="onCriteriumChange"/> %</div>
            <ul class="criterium-levels">
                <li v-for="level in rubric.levels" :key="level.id" class="rb-criterium-level">
                    <div class="rb-criterium-level-title">{{ level.title }} <span v-if="level.description" class="fa fa-question-circle criterium-level-description" :title="level.description"></span></div>
                    <criterium-level-view :rubric="rubric" :criterium="criterium" :level="level" @input="updateHeight" @change="onChoiceChange"></criterium-level-view>
                </li>
            </ul>
            <a href="#" role="button" @click.prevent="$emit('close')" class="rubric-return"><i class="fa fa-arrow-left"/> Terug naar rubric</a>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import debounce from 'debounce';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import Choice from '../Domain/Choice';
    import CriteriumLevelView from './CriteriumLevelView.vue';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: { CriteriumLevelView }
    })
    export default class ScoreRubricView extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(Criterium) readonly criterium!: Criterium | null;

        constructor() {
            super();
            this.onCriteriumChange = debounce(this.onCriteriumChange, 750);
        }

        updateHeight(e: InputEvent) {
            updateHeight(e.target as HTMLElement);
        }

        updateHeightAll() {
            for (let elem of document.getElementsByClassName('criterium-level-feedback')) {
                updateHeight(elem as HTMLElement);
            }
        }

        updated() {
            this.updateHeightAll();
        }

        mounted() {
            this.updateHeightAll();
        }

        onCriteriumChange() {
            this.$emit('change-criterium', this.criterium);
        }

        onChoiceChange(choice: Choice) {
            this.$emit('change-choice', choice);
        }
    }
</script>

<style scoped>
     * {
        outline: none;
    }
</style>