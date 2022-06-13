<template>
    <div class="highlight-input">
        <div class="highlight-overlay"></div>
        <div class="highlight-content">
            <slot></slot>
            <div class="name-input-actions">
                <button class="btn btn-primary btn-sm" @click="$emit('edit')">Wijzig</button>
                <button class="btn btn-default btn-sm" @click="$emit('cancel')">Annuleer</button>
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
    top: -4px;
    width: 104%;
}

.name-input-actions {
    gap: .4em;
    display: flex;
    margin-top: .4em;

    .btn {
        padding: 2px 4px;
    }

    .btn-primary {
        color: white;
    }
}
</style>