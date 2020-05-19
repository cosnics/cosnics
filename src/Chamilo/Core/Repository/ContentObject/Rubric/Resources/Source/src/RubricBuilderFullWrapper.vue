<template>
    <div class="container-fluid">
        <rubric-builder-full :api-config="config" :rubric-data="rubricData" :version="version" @rubric-updated="$emit('rubric-updated', $event)"></rubric-builder-full>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import RubricBuilderFull from './RubricBuilderFull.vue';

    @Component({
        components: {
            RubricBuilderFull
        },
    })
    export default class RubricBuilderFullWrapper extends Vue {
        private config: any = {
            '...URL': 'https://test'
        };
        private version = 0;

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
    }
</script>
