<template>
    <div class="save-state">
        <div v-if="dataConnector && dataConnector.isSaving" class="saving">
            {{ dataConnector.processingSize }} wijziging{{ dataConnector.processingSize === 1 ? '' : 'en' }}...
        </div>
        <div v-else-if="dataConnector" class="saved" aria-live="polite">
            Wijzigingen opgeslagen
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import DataConnector from '../Connector/DataConnector';

    @Component({
        name: 'save-area',
        components: {
        },
    })
    export default class SaveArea extends Vue {
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;
    }
</script>
