<i18n>
{
    "en": {
        "cancel": "Cancel",
        "edit": "Edit"
    },
    "nl": {
        "cancel": "Annuleren",
        "edit": "Wijzigen"
    }
}
</i18n>
<template>
    <div>
        <slot name="menu"></slot>
        <div class="highlight-content">
            <slot name="content"></slot>
            <div class="u-flex name-input-actions">
                <button class="btn btn-primary btn-sm" @click="$emit('edit')">{{ $t('edit') }}</button>
                <button class="btn btn-default btn-sm" @click="$emit('cancel')">{{ $t('cancel') }}</button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Vue} from 'vue-property-decorator';

@Component({
    name: 'highlight-input'
})
export default class HighlightInput extends Vue {
}
</script>

<style lang="scss" scoped>
.highlight-content {
    border-radius: 3px;
    max-width: 160px;
    min-width: 70px;
    padding: 8px 6px;
}

.name-input-actions {
    gap: .4em;
    margin-top: .4em;

    .btn {
        padding: 2px 4px;
    }

    .btn-primary {
        color: white;
    }
}
</style>