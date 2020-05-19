<template>
    <div class="container-fluid">
        <rubric-builder :api-config="config" :rubric-data="rubricData" :version="version" @rubric-updated="$emit('rubric-updated', $event)"></rubric-builder>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilder from './RubricBuilder.vue';

    @Component({
        components: {
            RubricBuilder
        },
    })
    export default class RubricBuilderWrapper extends Vue {
        private config: any = {
            'addLevelURL': 'https://test',
            'addTreeNodeURL': 'https://test',
            'deleteLevelURL': 'https://test',
            'deleteTreeNodeURL': 'https://test',
            'moveLevelURL': 'https://test',
            'moveTreeNodeURL': 'https://test',
            'updateChoiceURL': 'https://test',
            'updateLevelURL': 'https://test',
            'updateTreeNodeURL': 'https://test'
        };
        private version = 0;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
    }
</script>