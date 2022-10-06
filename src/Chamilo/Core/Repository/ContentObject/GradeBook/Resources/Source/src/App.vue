<template>
  <div id="app">
    <Main v-if="gradeBook" :grade-book="gradeBook" :connector="connector"></Main>
    <div id="server-response"></div>
  </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Main from './components/Main.vue';
    import GradeBook, {GradeScore, ResultsData} from './domain/GradeBook';
    import APIConfig from './connector/APIConfig';
    import Connector from './connector/Connector';

    @Component({
        components: { Main }
    })
    export default class App extends Vue {
        private gradeBook: GradeBook|null = null;
        private connector: Connector|null = null;

        @Prop({type: Object, default: () => null}) readonly apiConfig!: APIConfig;

        async load(): Promise<void> {
            const allData: any = await Connector.loadGradeBookData(this.apiConfig.loadGradeBookDataURL, this.apiConfig.csrfToken);
            //console.log(allData);
            if (allData) {
                this.gradeBook = GradeBook.from(allData.gradebook);
                this.gradeBook.users = allData.users;
                this.connector = new Connector(this.apiConfig, this.gradeBook.dataId, this.gradeBook.currentVersion);
                const resultsData: ResultsData = {'totals': {}};
                allData.scores.forEach((score: GradeScore) => {
                    if (score.isTotal) {
                        resultsData['totals'][score.targetUserId] = score;
                        return;
                    }
                    if (!resultsData[score.columnId]) {
                        resultsData[score.columnId] = {};
                    }
                    resultsData[score.columnId][score.targetUserId] = score;
                });
                this.gradeBook.resultsData = resultsData;
            }
            console.log(this.gradeBook);
        }

        mounted() {
            this.load();
        }
    }
</script>