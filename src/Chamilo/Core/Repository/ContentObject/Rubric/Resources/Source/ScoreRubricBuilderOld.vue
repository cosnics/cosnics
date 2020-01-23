import {Signal} from "@/Plugins/ScoreRubric/Domain/Level";
<template>
  <b-container fluid class="mt-5 ml-0 mr-0">
    <h1>Score Rubrik</h1>
      <div class="rubrik-container mb-0">
        <div class="table-container">
          <table class="table table-striped table-bordered">
            <thead>
            <tr>
              <th scope="col">

              </th>
              <th v-for="(level, levelIndex) in levels" :key="levelIndex"
                  scope="col">
                <div class="p-2">
                            <textarea class="form-control text-area-level font-weight-bold"
                                      v-model="levels[levelIndex].title"
                                      placeholder="Vul hier een niveau in"></textarea>

                  <div class="mt-1 mb-1">
                    <b-input-group prepend="Score: " append="Punten" class="weight-input-group">
                      <input type="number" name="Weight" class="form-control"
                             :id="levelIndex + '_score'"
                             placeholder="Gewicht %" min="0" max="100" maxlength="3"
                             v-model="level.score">
                    </b-input-group>
                  </div>
                  <b-form-select v-model="level.signal" :class="getSignalSelectClass(level)"
                                 :options="signalOptions"
                                 class="mt-1 mb-1">
                  </b-form-select>
                  <div class="score-and-buttons-container">
                    <div class="btn-group mt-1" role="group">
                      <button v-if="levelIndex > 0" class="btn btn-sm btn-secondary pb-1"
                              v-on:click="move(levels,levelIndex, levelIndex - 1)"><i
                              class="fa fa-arrow-left" aria-hidden="true"></i></button>
                      <button class="btn btn-sm btn-danger" v-on:click="removeLevel(levelIndex)">
                        <i
                                class="fa fa-minus-circle" aria-hidden="true"></i> Verwijder
                      </button>
                      <button v-if="levelIndex < levels.length - 1"
                              class="btn btn-sm btn-secondary"
                              v-on:click="move(levels,levelIndex, levelIndex + 1)"><i
                              class="fa fa-arrow-right" aria-hidden="true"></i></button>
                    </div>
                  </div>
                  <div class="score-and-buttons-container custom-control custom-radio cel-radio pt-1">
                    <div>
                      <input type="radio"
                             :id="'radio_' + levelIndex + '_default'"
                             name="defaultChoice" class="custom-control-input"
                             v-model="defaultLevelChoice"
                             :value="levelIndex"
                      >
                      <label class="custom-control-label" :for="'radio_' + levelIndex + '_default'">Standaardkeuze</label>
                    </div>
                  </div>
                </div>
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(criterium, criteriumIndex) in criteria">
              <th scope="row">
                <div class="p-2"
                >
                  <b-row>
                    <b-col>
                      <h4>{{criterium.title}}</h4>
                    </b-col>
                  </b-row>
                  <b-row class="ml-0">
                    <b-input-group prepend="Gewicht: " append="%" class="weight-input-group">
                      <input type="number" name="Score" class="form-control"
                             :id="criteriumIndex + '_weight'"
                             placeholder="Gewicht %" min="0" max="100" maxlength="3"
                             v-model="criterium.weight">
                    </b-input-group>
                  </b-row>
                  <b-row class="mt-2">
                    <b-col>
                      <button class="btn btn-sm btn-danger"
                              v-on:click="removeCriterium(criteriumIndex)">
                        <i
                                class="fa fa-minus-circle" aria-hidden="true"></i> Verwijder
                      </button>
                      <button v-if="criteriumIndex > 0" class="btn btn-sm btn-secondary ml-1"
                              v-on:click="move(criteria,criteriumIndex, criteriumIndex - 1)"><i
                              class="fa fa-arrow-up" aria-hidden="true"></i></button>
                      <button v-if="criteriumIndex < criteria.length - 1"
                              class="btn btn-sm btn-secondary ml-1"
                              v-on:click="move(criteria,criteriumIndex, criteriumIndex + 1)"><i
                              class="fa fa-arrow-down" aria-hidden="true"></i></button>

                    </b-col>
                  </b-row>
                </div>
              </th>
              <td v-for="(level, levelIndex) in levels" :key="levelIndex" scope="col"
                  :class="getCellSelectionClass(criterium, levelIndex)"

              >
                <div class="cell-selection-container p-1" :class="getCellChoiceClass(criterium, levelIndex)"
                     :tabindex="-1"
                     @click="selectCell(criterium, level)"
                     @keyup.up="navigateUp" @keyup.down="navigateDown" @keyup.left="navigateLeft" @keyup.right="navigateRight"
                     @keyup.enter="editFeedback(criteriumIndex, levelIndex)"
                     :ref="'cell_' + criteriumIndex + '_' + levelIndex">

                  <div class="cell-container">
                    <textarea
                              class="form-control text-area-feedback"
                              placeholder="Schrijf hier optioneel je feedback neer"
                              v-model="getCell(criterium, level).feedback"
                              :tabindex="currentView === 'view_quotate' ? -1 : 0"
                              @click.stop="selectCell(criterium, level, false)"
                              @keyup.left.stop @keyup.right.stop @keyup.down.stop @keyup.up.stop
                              @keydown.esc="focusOnSelectedCell"
                              :ref="'cell_feedback_' + criteriumIndex + '_' + levelIndex"
                    >
                                    </textarea>
                  </div>
                  <div class="score-and-buttons-container">
                    Score: {{getCellScore(criterium, level)}}
                  </div>
                </div>
              </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
              <td :colspan="levels.length + 1" style="text-align: left;">
                <div>
                  <b-button block variant="primary" v-b-modal.modal-criteria>Voeg criterium toe
                  </b-button>

                  <b-modal id="modal-criteria" size="xl" title="Voeg criterium toe">
                    <b-form-input placeholder="Zoek"></b-form-input>
                    <b-table striped
                             selectable
                             select-mode="multi"
                             selectedVariant="success"
                             @row-selected="modalCriteriaSelected"
                             :items="criteriaFromDatabase"
                    >
                    </b-table>
                    <textarea class="form-control" v-model="freeCriterium"
                              placeholder="Of maak een vrij criterium aan"> </textarea>
                    <template slot="modal-footer" slot-scope="{ ok }">
                      <b-button size="sm" variant="success" @click="modalAddCriteria()">
                        Voeg toe
                      </b-button>
                    </template>
                  </b-modal>
                </div>
              </td>
            </tr>
            </tfoot>
          </table>
        </div>
        <button class="btn btn-primary btn-lg ml-1 btn-add-level"
                v-on:click="addLevel()">Voeg Niveau toe
        </button>
      </div>
      <div  class="mr-5 ml-5 float-right">
        <h1>Maximum score: {{getMaximumScore()}}</h1>
      </div>
  </b-container>
</template>

<script lang="ts">
  import {Component, Prop, Vue, Watch} from "vue-property-decorator";
  import Criterium from "@/ScoreRubric/src/Domain/Criterium";
  import Choice from "./src/Domain/Choice";
  import Level, {Signal} from "@/ScoreRubric/src/Domain/Level";

  @Component
export default class ScoreRubricBuilder extends Vue {
  @Prop() private message!: string;
  protected levels: Level[] = [];
  protected defaultLevelChoice= undefined;
  protected selectedCell?: Choice = undefined;
  protected criteria:Criterium[] = [];
  protected cells:Choice[] = [];
  protected criteriaFromDatabase:Criterium[] = [
    new Criterium("Volledigheid antwoorden"),
    new Criterium("Onderbouwde mening"),
    new Criterium("Kan de juiste controlestructuur kiezen"),
    new Criterium("Kan een correcte controlestructuur schrijven in Java")
  ];
  protected criteriaToAdd:Criterium[] = [];
  protected freeCriterium = '';
  protected signalOptions = [
    {text: 'Groen signaal', value: '1'},
    {text: 'Oranje signaal', value: '2'},
    {html: '<span class="signal-green">Rood signaal</span>', value: '3'}
  ];

  @Watch('defaultLevelChoice')
  protected defaultLevelChoiceChanged(val:number) {
    this.criteria.forEach(criterium => criterium.selectedLevelIndex = val);
  };

  public mounted() {
    let level1 = this.addLevel("Overstijgt de verwachtingen", 1, 5);
    let level2 = this.addLevel("Voldoet aan de verwachtingen", 1, 4);
    this.addLevel("Voldoet bijna aan de verwachtingen", 2, 3);
    let level4 = this.addLevel("Voldoet niet aan de verwachtingen", 3, 1);
    let criterium1 = this.addCriterium("Volledigheid antwoorden");
    let criterium2 = this.addCriterium("Onderbouwde mening");

    this.getCell(criterium1, level1).feedback = 'Student geeft steeds volledige en betrouwbare Informatie. Alle informatie is opgenomen in de antwoorden';
    this.getCell(criterium1, level2).feedback = 'Student geeft vaker wel dan niet volledige en betrouwbare informatie. Bijna alle informatie is opgenomen in de antwoorden';
    this.getCell(criterium1, level4).feedback = 'Student geeft zo goed als altijd onvolledige en twijfelachtige informatie die vragen oproept. De antwoorden bevatten amper informatie';

    this.getCell(criterium2, level1).feedback = 'Student geeft zijn mening onderbouwd en overtuigend';
    this.getCell(criterium2, level2).feedback = 'Student geeft zijn mening, maar onderbouwt deze niet altijd even goed';
    this.getCell(criterium2, level4).feedback = 'student geeft geen eigen mening';
  }

  protected getMaximumScore() {
    let maxLevel = this.levels[0];
    for (let levelIndex in this.levels) {
      if (Number(maxLevel.score) < Number(this.levels[levelIndex].score)) {
        maxLevel = this.levels[levelIndex];
      }
    }

    return this.criteria
            .map(criterium => Math.round(criterium.weight * maxLevel.score) / 100)
            .reduce((sum, current) => sum + current, 0);
  }

  protected getSignalSelectClass(cell:Choice) {
    if (cell.level.signal == Signal.GREEN)
      return "signal-green";
    if (cell.level.signal == Signal.ORANGE)
      return "signal-orange";
    if (cell.level.signal == Signal.RED)
      return "signal-red";
  }

  protected getCellChoiceClass(criterium: Criterium) {
    /*if (this.currentView !== 'view_create' && criterium.selectedLevelIndex === levelIndex)
        return "bg-success";
    else*/ if(this.criteria.indexOf(criterium) % 2 === 0) {
      return "bg-gray";
    } else
      return "bg-white";
  }

  /*protected modalCriteriaSelected(items) {
    this.criteriaToAdd = items.map(item => item.BestaandeCriteria)
  }*/

  protected select(signal: Signal, level: Level) {
    level.signal = signal;
  }

  protected addLevel(title = "Pas titel aan", signal = Signal.GREEN, score = 5) {
    let level = new Level(title, score, signal);
    this.levels.push(level);

    this.criteria.forEach(criterium => {
      this.cells.push(new Choice(criterium, level, false));
    });

    return level;
  }

  protected removeLevel(index) {
    if (confirm("Niveau verwijderen?") === false) {
      return;
    }
    let levelToRemove = this.levels[index];
    this.levels.splice(index, 1);

    this.cells = this.cells.filter(function (cell) {
      return cell.level !== levelToRemove;
    });
  }

  protected modalAddCriteria() {
    this.criteriaToAdd.forEach(criterium => {
      this.addCriterium(criterium)
    });
    this.criteriaToAdd = '';
    if (this.freeCriterium !== '') {
      this.addCriterium(this.freeCriterium);
      this.freeCriterium = '';
    }

    this.$bvModal.hide('modal-criteria');
  }

  protected addCriterium(title = "") {
    let newCriterium = new Criterium(title);
    this.criteria.push(newCriterium);
    this.levels.forEach(level => {
      this.cells.push(new Choice(newCriterium, level))
    });

    return newCriterium;
  }

  protected removeCriterium(index) {
    if (confirm("Criterium verwijderen?") === false) {
      return;
    }
    let criteriumToRemove = this.criteria[index];
    this.criteria.splice(index, 1);
    this.cells = this.cells.filter(function (cell) {
      return cell.criterium !== criteriumToRemove;
    });
  }

  protected move(arr, from, to) {
    arr.splice(to, 0, arr.splice(from, 1)[0]);
  }

  protected getCell(criterium: Criterium, level: Level):Choice {
    return this.cells.find(cell => cell.criterium === criterium && cell.level === level);
  }

  protected getCellScore(criterium, level) {
    return Math.round(criterium.weight * level.score) / 100;
  }

  protected getScore() {
    let score = 0;
    for (let criteriaIndex in this.criteria) {
      let criterium = this.criteria[criteriaIndex];
      if (criterium.selectedLevelIndex !== null)
        score += this.getCellScore(criterium, this.levels[criterium.selectedLevelIndex]);
    }

    return Math.round(score / this.getMaximumScore() * 100);
  }

}
</script>

<style scoped>
  .bg-selected{
    background: #007bff;
  }

  .bg-gray {
    background:#f2f2f2;
  }

  .bg-white {
    background: white;
  }

  .table th, .table td {
    padding: 0.25rem;
  }
  .text-area-level {
    font-size: 22px;
  }

  .weight-input-group {
    width: 200px;
  }

  .signal-green {
    color: #28a745;
  }

  .signal-red {
    color: red;
  }

  .signal-orange {
    color: orange;
  }

  .cel-radio {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .check {
    font-size: 35px;
    color: white;
  }

  .cell-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
  }

  .warning-lights {
    display: flex;
    flex-direction: row;
    justify-content: center;
  }

  .circle-bordered {
    border: solid 5px;
  }


  .circle-red {
    cursor: pointer;
    background: red;
    width: 50px;
    height: 50px;
    border-radius: 50%;
  }

  .circle-orange {
    cursor: pointer;
    background: orange;
    width: 50px;
    height: 50px;
    border-radius: 50%;
  }

  .circle-green {
    cursor: pointer;
    background: #52dd37;
    width: 50px;
    height: 50px;
    border-radius: 50%;

  }

  .img-badge {
    width: 100px;
  }

  .badge-container {
    text-align: center;
  }

  .btn-scale {
    float: right;
    margin-bottom: 130px;
    display: block;
  }

  .rubrik-container {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
  }

  .level-title {
    text-align: center;
  }

  .score-input {
    width: 55px;
    display: block;
    margin-top: 5px;
  }

  .score-and-buttons-container {
    text-align: center;
  }

  .score-container {
    display: flex;
    flex-direction: row;
    justify-content: center;
  }

  .table-container {
    width: 100%;
  }


  .text-area-feedback {
    height: 120px;
  }

  .btn-add-level {
    width: 100px;
    margin-bottom: 80px;
  }

  .cell-selection-container {
    border-radius: 5px;
  }

</style>
