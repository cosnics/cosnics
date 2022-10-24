<template>
    <div id="app">
        <user-scores v-if="gradeBook" :grade-book="gradeBook"></user-scores>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import UserScores from './components/UserScores.vue';
import GradeBook, {GradeScore, ResultsData} from './domain/GradeBook';

@Component({
    components: { UserScores }
})
export default class UserScoresApp extends Vue {
    private gradeBook: GradeBook|null = null;

    @Prop({type: Object, required: true}) readonly gradeBookData!: any;
    @Prop({type: Array, required: true}) readonly users!: any[];
    @Prop({type: Array, required: true}) readonly scores!: any[];

    mounted() {
        this.gradeBook = GradeBook.from(this.gradeBookData);
        const resultsData: ResultsData = {'totals': {}};
        this.gradeBook.users = this.users;
        this.scores.forEach((score: GradeScore) => {
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
}
</script>
