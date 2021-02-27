<template>
    <rubric-entry v-if="rubric" :rubric="rubric" :preview="true" :ui-state="uiState"></rubric-entry>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import Rubric from '../Domain/Rubric';
import RubricEntry from './RubricEntry.vue';

@Component({
    components: {
        RubricEntry
    },
})
export default class RubricBuilderPreview extends Vue {
    @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
    @Prop({type: Object}) readonly uiState!: any;
}
</script>