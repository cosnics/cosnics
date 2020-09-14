<template>
    <div v-if="debugServerResponse">
        <rubric-builder :api-config="apiConfig" :rubric-data="convertedRubricData" :version="version" :ui-state="uiState"></rubric-builder>
        <div id="server-response"></div>
    </div>
    <rubric-builder v-else :api-config="apiConfig" :rubric-data="convertedRubricData" :version="version" :ui-state="uiState"></rubric-builder>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilder from './RubricBuilder.vue';
    import {convertRubricData} from '../Util/util';
    import 'vue-swatches/dist/vue-swatches.css';

    @Component({
        components: {
            RubricBuilder
        },
    })
    export default class RubricBuilderWrapper extends Vue {
        private uiState = {
            showSplitView: false,
            selectedCriterium: '',
            selectedClusterView1: '',
            selectedClusterView2: ''
        };
        private convertedRubricData: any;

        @Prop({type: Object, required: true}) readonly apiConfig!: object;
        @Prop({type: Number, required: true}) readonly version!: number;
        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Boolean, default: false}) readonly debugServerResponse!: boolean;

        created() {
            this.convertedRubricData = convertRubricData(this.rubricData);
        }
    }
</script>
