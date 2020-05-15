<template>
    <ul class="builder">
        <li id="--rubric-home" class="builder-list-item" :class="{ selected: content === 'home' }" @click="$emit('onPage', 'home')"><a>Home</a></li>
        <li id="--rubric-builder" class="builder-list-item" :class="{ selected: content === 'builder' }"  @click="$emit('onPage', 'builder')"><a>Builder</a></li>
        <li id="--rubric-builder-full" class="builder-list-item" :class="{ selected: content === 'builder-full' }"  @click="$emit('onPage', 'builder-full')"><a>Builder Full</a></li>
        <li id="--rubric-entry" class="builder-list-item" :class="{ selected: content === 'entry' }"  @click="$emit('onPage', 'entry')"><a>Entry</a></li>
        <li id="--rubric-results" class="builder-list-item" :class="{ selected: content === 'results' }"  @click="$emit('onPage', 'results')"><a>Results</a></li>
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
<style>

</style>