<template>
    <div @dblclick="$emit('edit')">
        <div v-if="result === 'afw'" class="color-code deep-orange-500" title="Afwezig"><span>afw</span></div>
        <div v-else-if="result === 'gafw'" class="color-code amber-700" title="Gewettigd afwezig"><span>gafw</span></div>
        <div v-else-if="result === null" class="color-code mod-none"><span></span></div>
        <template v-else>{{ result }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {ResultType} from '../domain/GradeBook';

@Component({
    name: 'student-result'
})
export default class StudentResult extends Vue {
    @Prop({type: [Number, String], default: null}) readonly result!: ResultType;
}
</script>

<style scoped lang="scss">
.fa-percent {
    font-size: 1.1rem;
    margin-left: .15rem;
    opacity: .8;
}

.color-code {
    background-color: var(--color);
    border: 1px solid transparent;
    border-radius: 3px;
    color: var(--text-color);
    display: flex;
    height: 20px;
    justify-content: center;
    padding: 2px 4px;
    transition: background 75ms linear,color 75ms linear,opacity 75ms linear;
    width: fit-content;
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 12px;
}

.color-code.mod-none {
    --color: #d8e9db;
    background: transparent linear-gradient(135deg,var(--color) 10%,transparent 0,transparent 50%,var(--color) 0,var(--color) 60%,transparent 0,transparent) 0 0/7px 7px;
    border-radius: 5px;
    min-width: 40px;
}

.deep-orange-500 {
    --color: #ff5722;
    --selected-color: #d53300;
    --text-color: white;
}

.amber-700 {
    --color: #ffa000;
    --selected-color: #db8a00;
    --text-color: white;
}
</style>