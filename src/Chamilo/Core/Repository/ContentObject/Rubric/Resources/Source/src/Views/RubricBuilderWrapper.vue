<template>
    <div class="container-fluid">
        <rubric-builder :api-config="apiConfig" :rubric-data="convertedRubricData" :version="version" :ui-state="uiState"></rubric-builder>
        <div id="innerhtml"></div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilder from './RubricBuilder.vue';
    import 'vue-swatches/dist/vue-swatches.css';

    function sortFn(v1: any, v2: any) {
        return (v1.sort > v2.sort) ? 1 : -1;
    }

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

        private convertData(d: any) {
            const data: any = {
                "id": String(d.root_node.id),
                "useScores": d.use_scores,
                "title": d.root_node.title,
                "choices": [],
                "criteria": []
            };
            d.levels.sort(sortFn);
            data.levels = d.levels.map((level: any) => ({
                "id": String(level.id),
                "title": level.title,
                "description": level.description || '',
                "score": level.score,
                "isDefault": level.is_default
            }));
            const clusters = (d.root_node.children || []).filter((v: any) => v.type === 'cluster');
            clusters.sort(sortFn);
            data.clusters = clusters.map((c: any) => {
                const cluster: any = {
                    "id": String(c.id),
                    "title": c.title,
                    "criteria": []
                };
                const categories = (c.children || []).filter((v: any) => v.type === 'category');
                categories.sort(sortFn);
                cluster.categories = categories.map((c: any) => {
                    const category: any = {
                        "id": String(c.id),
                        "title": c.title,
                        "color": c.color || ''
                    };
                    const criteria = (c.children || []).filter((v: any) => v.type === 'criterium');
                    criteria.sort(sortFn);
                    category.criteria = criteria.map((c: any) => {
                        const criterium = {
                            "id": String(c.id),
                            "title": c.title,
                            "weight": c.weight
                        };
                        const choices = c.choices || [];
                        choices.sort(sortFn);
                        choices.forEach((choice: any) => {
                            data.choices.push({
                                "criteriumId": criterium.id,
                                "levelId": String(choice.level.id),
                                "selected": choice.selected,
                                "feedback": choice.feedback || '',
                                "hasFixedScore": choice.has_fixed_score,
                                "fixedScore": choice.fixed_score
                            });
                        });
                        return criterium;
                    });
                    return category;
                });
                return cluster;
            });
            return data;
        }

        created() {
            this.convertedRubricData = this.convertData(this.rubricData);
        }
    }
</script>
<style lang="scss">
    @include loader();
    @include app();
    @include table-app();
</style>