<template>
    <div @dblclick="$emit('edit')">
        <a v-if="isOverwritten" style="margin-right: 4px;color:#5885a2;text-shadow:1px 1px #e2eaee;" @click.stop="$emit('revert')"><i class="fa fa-times-circle" aria-hidden="true"></i></a>
        <div v-if="result === 'afw'" class="color-code deep-orange-500" title="Afwezig"><span>afw</span></div>
        <div v-else-if="result === 'gafw'" class="color-code amber-700" title="Gewettigd afwezig"><span>gafw</span></div>
        <div v-else-if="result === null" class="color-code mod-none"><span></span></div>
        <template v-else>{{ result }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></template>
        <template v-if="comment">
            <a :id="`result-comment-${id}`" class="comment" @click.stop="$emit('edit-comment')"><i class="fa fa-comment" aria-hidden="true"></i></a>
            <b-popover :target="`result-comment-${id}`" triggers="hover" placement="right">
                <div style="font-size: 13px;padding: 6px 8px;width: 200px;max-width: 200px;">
                    {{ comment }}
                </div>
            </b-popover>
        </template>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import {ResultType} from '../domain/GradeBook';

@Component({
    name: 'student-result'
})
export default class StudentResult extends Vue {
    @Prop({type: String, default: ''}) readonly id!: string;
    @Prop({type: [Number, String], default: null}) readonly result!: ResultType;
    @Prop({type: Boolean, default: false}) readonly isOverwritten!: boolean;
    @Prop({type: String, default: ''}) readonly comment!: string;
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

.comment {
        color: #5885a2;
    /*color: #c4cbcf;*/
    text-shadow:1px 1px #e2eaee;
    margin-left: auto;
    font-size:12px;
}
</style>
