<i18n>
{
    "en": {
        "move-up": "Move up",
        "move-down": "Move down",
        "remove": "Remove"
    },
    "nl": {
        "move-up": "Verplaats naar boven",
        "move-down": "Verplaats naar beneden",
        "remove": "Verwijder"
    }
}
</i18n>

<template>
    <div>
        <button :id="`btn-up-${id}`" :title="$t('move-up')" :disabled="isUpDisabled" class="btn btn-default btn-sm mod-level-action"
                @click.stop="$emit('move-up')" @focus="$emit('select')">
            <i class="fa fa-arrow-up" aria-hidden="true"></i>
            <span class="sr-only">{{ $t('move-up') }}</span>
        </button>
        <button :id="`btn-down-${id}`" :title="$t('move-down')" :disabled="isDownDisabled" class="btn btn-default btn-sm mod-level-action"
                @click.stop="$emit('move-down')" @focus="$emit('select')">
            <i class="fa fa-arrow-down" aria-hidden="true"></i>
            <span class="sr-only">{{ $t('move-down') }}</span>
        </button>
        <button :title="$t('remove')" :disabled="isRemoveDisabled" class="btn btn-default btn-sm mod-level-action mod-remove"
                @click.stop="$emit('remove')" @focus="$emit('select')">
            <i class="fa fa-minus-circle" aria-hidden="true"></i>
            <span class="sr-only">{{ $t('remove') }}</span>
        </button>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'selection-controls'
})
export default class SelectionControls extends Vue {
    @Prop({type: String, default: ''}) readonly id!: string;
    @Prop({type: Boolean, default: false}) readonly isUpDisabled!: boolean;
    @Prop({type: Boolean, default: false}) readonly isDownDisabled!: boolean;
    @Prop({type: Boolean, default: false}) readonly isRemoveDisabled!: boolean;
}
</script>

<style>
.btn.btn-default.mod-level-action {
    color: #4d88b3;
    font-size: 14px;
    padding: 2px 5px;
    width: 25px;
}

.btn.mod-level-action.mod-remove:not(:disabled),
.btn.mod-level-action.mod-cancel {
    color: red;
}

.level-row:not(.is-selected) .btn.mod-level-action {
    opacity: 0;
}
</style>