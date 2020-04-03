<template>
    <div class="container criterium" v-if="criterium !== null">
        <div v-if="criterium">
            <i class="fa fa fa-close" @click="$emit('close')"/>
            <h2>
                <label for="title" style="display:table-cell; width:1px">Criterium: </label>
                <input type="text" v-model="criterium.title" id="title" name="title" style="display: table-cell; width: 100%"/>
            </h2>
            <div class="criterium-path">{{ criterium.parent.parent.parent.title}} > {{ criterium.parent.parent.title}} > {{ criterium.parent.title }}</div>
            <div class="criterium-weight"><label for="weight">Gewicht:</label> <input type="number" id="weight" v-model="criterium.weight"/> %</div>
            <ul>
                <li v-for="level in store.rubric.levels" :key="level.id">
                    <div class="level-title">{{ level.title }} <span v-if="level.description" class="fa fa-question-circle description" :title="level.description"></span></div>
                    <div class="level-input">
                        <textarea v-model="store.rubric.getChoice(criterium, level).feedback" class="ta-feedback"
                                  placeholder="Geef feedback"
                                  @input="updateHeight"></textarea>
                        <div v-if="store.rubric.useScores" class="level-score">
                            <input type="number" disabled v-model="store.rubric.getChoiceScore(criterium, level)" />
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Vue, Prop, Watch} from "vue-property-decorator";
    import Criterium from "../../Domain/Criterium";

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    @Component({
        name: 'criterium-details-view',
        components: {  }
    })
    export default class ScoreRubricView extends Vue {
        @Prop(Criterium) readonly criterium!: Criterium | null;

        /*get criterium() {
            return this.selectedCriterium;
        }*/
        get store() {
            return this.$root.$data.store;
        }
        updateHeight(e: InputEvent) {
            updateHeight(e.target as HTMLElement);
        }
        updated() {
            for (let elem of document.getElementsByClassName('ta-feedback')) {
                updateHeight(elem as HTMLElement);
            }
        }
    }

</script>

<style scoped>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        outline-width: thin;
    }
    .container.criterium {
        position: relative;
        min-width: 28%;
        /*height: 100vh;*/
        max-width: 28%;
        padding-bottom: 10px;
        /*background: whitesmoke;*/
        background: hsla(203, 13%, 88%, 1);
        border-left: 1px solid hsla(200, 50%, 50%, 0.4);
        overflow-y: auto;
        padding:20px;
        color: #333;
    }
    h2 {
        display:table;
        border-collapse:separate;
        font-size: 16px;
        margin-top: 2px;
        margin-bottom: 10px;
        width: 100%;
        max-width: 100%;
        border-spacing: 3px;
        margin-left: -3px;
    }
    .container.criterium > div > input {
        /*width: 100%;*/
    }
    h2 label, h2 input {
        font-weight: 500;
    }
    h2 input {
        height: 30px;
        padding-left: 4px;
        /*background: #eee;*/
        background: hsla(200, 5%, 90%, 1);
        background: transparent;
        border: 1px solid transparent;
        border-radius: 3px;
    }
    h2 input:focus {
        background: white;
    }
    ul {
        list-style: none;
        margin-top: 20px;
    }
    li { margin-bottom: 20px;}
    .level-title { font-weight: bold; }
    .level-input { display: flex; flex-direction: row;}
    .level-score { width: 50px; margin-left: 8px;}
    .level-score input[type="number"] {
        font-size: 18px;
        width: 55px;
        text-align: right;
        padding-right: 16px;
        background: hsla(200, 5%, 90%, 1);
        /*border: 1px solid hsla(190, 50%, 50%, 0.2);*/
        border: 1px solid transparent;
        border-radius: 3px;
    }
    .level-score input[type="number"]:focus {
        background: white;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { position: absolute; right: 0; }

    textarea {
        width: 100%;
        padding: 4px;
    }
    .criterium-path {
        font-size: 12px;
    }
    .criterium-weight {
        margin-top: 10px;
        font-size: 14px;
    }
    .criterium-weight label {
        font-weight: normal;
    }
    .criterium-weight input {
        width: 40px;
        padding: 0 4px;
        background: hsla(200, 5%, 90%, 1);
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    .criterium-weight input:focus {
        background-color: white;
    }
    .ta-feedback {
        margin-left: -4px;
        background: hsla(200, 5%, 90%, 1);
        background: transparent;
        resize: none;
        border: 1px solid transparent;
        overflow: hidden;
        border-radius: 3px;
    }
    .ta-feedback:focus {
        background: white;
    }
    i.fa-close {
        display: block;
        position: absolute;
        right: 10px;
        top: 10px;
        color: #888;
        /*border: 1px solid transparent;*/
        padding: 4px;
        transition: background-color 0.1s linear, color 0.1s linear;
    }
    i.fa-close:hover {
        /*background-color: hsla(200, 50%, 30%, 0.3);*/
        background-color: #4f8be8;
        /*border: 1px solid #ccc;*/
        border-radius: 3px;
        color: white;
    }
    h2 input:hover, .level-score input[type="number"]:not(:disabled):hover, .criterium-weight:hover input, .ta-feedback:hover {
        border: 1px solid hsla(200, 50%, 50%, 0.5);
    }

    .description {
        color: #999;
        cursor: pointer;
        transition: color 200ms;
    }
    .description:hover {
        color: #666;
    }
</style>