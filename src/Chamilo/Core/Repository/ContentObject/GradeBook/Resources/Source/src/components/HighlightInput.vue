<template>
    <div style="position: absolute; top: 0; left: 0; width: 100%" >
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, .2)"></div>
        <div style="position: absolute; top: -4px; left: -2%; width: 104%; min-width: 170px; max-width: 260px; padding: 8px 6px; background:white; border-radius: 3px">
            <slot></slot>
            <div class="name-input-actions" style="margin-top: .4em;display: flex;gap: .4em;">
                <button class="btn btn-primary btn-sm" style="padding: 2px 4px;color:white" @click="$emit('edit')">Wijzig</button>
                <button class="btn btn-default btn-sm" style="padding: 2px 4px;" @click="$emit('cancel')">Annuleer</button>
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