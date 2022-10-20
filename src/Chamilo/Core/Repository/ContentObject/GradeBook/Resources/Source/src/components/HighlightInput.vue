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
    <div class="highlight-input">
        <div class="highlight-overlay"></div>
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
.highlight-input {
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
}

.highlight-overlay {
    background-color: rgba(0, 0, 0, .2);
    inset: 0;
    position: fixed;
}

.highlight-content {
    background-color: white;
    border-radius: 3px;
    left: -2%;
    max-width: 260px;
    min-width: 170px;
    padding: 8px 6px;
    position: absolute;
    /*top: -4px;*/
    top: 0;
    width: 104%;
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