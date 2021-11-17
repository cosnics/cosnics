<i18n>
{
    "en": {
        "save": "Save",
        "cancel": "Cancel"
    },
    "nl": {
        "save": "Opslaan",
        "cancel": "Annuleren"
    }
}
</i18n>

<template>
    <div>
        <button class="btn btn-default btn-sm mod-status-action" :title="$t('save')" @click.stop="$emit('save')"
                :disabled="isSavingDisabled">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            <span class="sr-only">{{ $t('save') }}</span>
        </button>
        <button class="btn btn-default btn-sm mod-status-action mod-cancel" :title="$t('cancel')"
                @click.stop="$emit('cancel')">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
            <span class="sr-only">{{ $t('cancel') }}</span>
        </button>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'new-status-controls'
})
export default class NewStatusControls extends Vue {
    @Prop({type: Boolean, default: false}) readonly isSavingDisabled!: boolean;
}
</script>
