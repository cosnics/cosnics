<template>
    <rubric-entry v-if="rubric" :rubric="rubric" :preview="true" :ui-state="uiState"></rubric-entry>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricEntry from './RubricEntry.vue';
    import {convertRubricData} from '../Util/util';

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricPreviewWrapper extends Vue {
        private rubric: Rubric | undefined;
        private uiState = {
            showDefaultFeedbackFields: false
        };

        @Prop({type: Object, required: true}) readonly rubricData!: object;

        created() {
            const convertedRubricData = convertRubricData(this.rubricData);
            this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
        }
    }
</script>