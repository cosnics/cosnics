<template>
    <button title="Split View" class="btn-show-split-view" aria-hidden="true" :class="{ checked: showSplitView }" @click.prevent="$emit('changed', !showSplitView)">
        <svg tabindex="-1" class="svg show-split-view" viewBox="-50 -50 100 100">
            <path id="path-1" d="M-40 -39 L40 -39 L40 -9 L-40 -9Z"></path>
            <path id="path-2" d="M-40 9L40 9L40 39L-40 39Z"></path>
        </svg>
    </button>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';

    @Component({
        name: 'split-view-button'
    })
    export default class SplitViewButton extends Vue {
        @Prop({type: Boolean, required: true}) readonly showSplitView!: boolean;
    }
</script>
<style lang="scss">
    .btn-show-split-view {
        display: none;
        margin-left: 1em;
        margin-right: -.5em;
        background: none;
        border: 1px solid lighten($cluster-color-border, 7%);
        border-radius: $border-radius;
        padding: 2px 8px;
        /*border-color: transparent;*/

        &:focus {
            outline: none;
            border-color: $input-color-focus;
        }

        &:hover {
            border-color: $cluster-color-border;
        }
    }
    @media only screen and (min-width: 900px) {
        .btn-show-split-view {
            display: block;
        }
    }
    .svg.show-split-view {
        outline: none;
        width: 1.3rem;
        height: 1.3rem;
        transform: translate(.1rem, .2rem)scale(2, 1.1);
        margin-right: .2rem;
    }
    #path-1 {
        fill: $cluster-color-selected;
        transition: fill 200ms;
    }
    #path-2 {
        fill: $cluster-color-selected;
        transition: fill 200ms;
    }
    .btn-show-split-view.checked {
        #path-2 {
            fill: $cluster-color;
        }
    }
    /*.btn-show-split-view:not(.checked):hover {
        #path-2 {
            fill: darkgrey;
        }
    }
    .btn-show-split-view.checked:hover {
        #path-2 {
            fill: lightgrey;
        }
    }*/

</style>