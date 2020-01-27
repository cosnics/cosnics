<template>
  <div id="app">
    <div class="container-fluid">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <score-rubric-tree-builder>

    </score-rubric-tree-builder>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import ScoreRubricTreeBuilder from "./Components/ScoreRubricTreeBuilder.vue";

@Component({
  components: {
    ScoreRubricTreeBuilder
  },
})
export default class App extends Vue {}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
