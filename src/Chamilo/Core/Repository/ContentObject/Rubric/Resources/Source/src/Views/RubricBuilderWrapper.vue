<template>
    <div class="container-fluid">
        <rubric-builder :api-config="apiConfig" :rubric-data="convertedRubricData" :version="version" :ui-state="uiState"></rubric-builder>
    </div>
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

        created() {
            this.convertedRubricData = convertRubricData(this.rubricData);
        }
    }
</script>
<style lang="scss">
    @include loader();
    @include app();
    @include table-app();
</style>