<i18n>
{
    "en": {
        "num-changes": "1 change | {count} changes",
        "all-saved": "All changes saved"
    },
    "fr": {
        "num-changes": "1 modification | {count} modifications",
        "all-saved": "Modifications enregistrées"
    },
    "nl": {
        "num-changes": "1 wijziging | {count} wijzigingen",
        "all-saved": "Wijzigingen opgeslagen"
    }
}
</i18n>

<template>
    <div class="save-state">
        <div v-if="showSaveState && dataConnector && dataConnector.isSaving">
            {{ $tc('num-changes', dataConnector.processingSize, { 'count': dataConnector.processingSize }) }}...
        </div>
        <div v-else-if="showSaveState && dataConnector" class="save-state-saved" aria-live="polite">
            {{ $t('all-saved') }}
        </div>
        <div v-if="error" class="block-ui"></div>
        <div v-if="error" class="save-error">
            <div class="save-error-msg">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import DataConnector from '../../Connector/DataConnector';

    @Component({
        name: 'save-area',
        components: {
        },
    })
    export default class SaveArea extends Vue {
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;
        @Prop({type: String, default: null}) readonly error!: string|null;
        @Prop({type: Boolean, default: true}) readonly showSaveState!: boolean;
    }
</script>
