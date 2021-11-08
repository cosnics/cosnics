<template>
    <div class="shape" :class="{'is-selected': selected}" @click="$emit('click')">
        <svg width="60" height="60" xmlns="http://www.w3.org/2000/svg">
            <rect x="0" y="0" width="60" height="60" fill="white" stroke="#e6e6e6" stroke-width="1"></rect>
            <g transform="translate(30, 30)"><slot></slot></g>
        </svg>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';

@Component({
    name: 'verification-shape',
})
export default class VerificationShape extends Vue {
    @Prop({type: Boolean, default: false}) readonly selected!: boolean;
}
</script>

<style scoped>
.shape {
    color: #d6dee0;
    cursor: pointer;
    height: 60px;
    transition: color 150ms ease;
}
.shape:hover {
    color: #acb9b9;
    --fillcolor: #acb9b9;
}
.shape.is-selected {
    color: #869898;
    --fillcolor: #869898;
}
rect, circle, g polygon { transition: fill 150ms ease; }
</style>
