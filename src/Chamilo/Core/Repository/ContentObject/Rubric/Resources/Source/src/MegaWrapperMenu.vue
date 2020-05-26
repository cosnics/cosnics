<template>
    <ul class="rubrics-menu">
        <li id="--rubric-home" class="rubrics-menu-item" :class="{ selected: content === 'home' }"><a @click.prevent="$emit('onPage', 'home')" href="#"><span tabindex="-1">Home</span></a></li>
        <li id="--rubric-builder" class="rubrics-menu-item" :class="{ selected: content === 'builder'}"><a @click.prevent="$emit('onPage', 'builder')" href="#"><span tabindex="-1">Builder</span></a></li>
        <li id="--rubric-entry" class="rubrics-menu-item" :class="{ selected: content === 'entry' }"><a @click.prevent="$emit('onPage', 'entry')" href="#"><span tabindex="-1">Entry</span></a></li>
        <li id="--rubric-results" class="rubrics-menu-item" :class="{ selected: content === 'results' }"><a @click.prevent="$emit('onPage', 'results')" href="#"><span tabindex="-1">Results</span></a></li>
    </ul>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';

    @Component({
        components: {
        }
    })
    export default class MegaWrapperMenu extends Vue {
        @Prop({type: String, required: true}) readonly content!: string;
    }
</script>
