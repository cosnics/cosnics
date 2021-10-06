<template>
    <div v-if="isEditable" role="button" tabindex="0" @keyup.enter="$emit('select')" @click="$emit('select')">
        <slot></slot>
    </div>
    <div v-else style="font-weight: 400; text-align: center">
        <slot></slot>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'dynamic-field-key'
})
export default class SearchBar extends Vue {
    @Prop({type: Boolean}) readonly isEditable!: boolean;
}
</script>