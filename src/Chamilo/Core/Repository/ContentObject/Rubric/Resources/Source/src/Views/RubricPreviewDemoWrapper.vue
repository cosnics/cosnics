<template>
    <div class="container-fluid">
        <rubric-preview :rubric-data="store.rubricData" :ui-state="store.uiState.preview"></rubric-preview>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import RubricPreview from './RubricPreview.vue';
    import store from '../store';

    @Component({
        components: {
            RubricPreview
        },
    })
    export default class RubricPreviewDemoWrapper extends Vue {
        private store: any = store;
    }
</script>
