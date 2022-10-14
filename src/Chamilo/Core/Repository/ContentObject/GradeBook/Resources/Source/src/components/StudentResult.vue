<template>
    <div @dblclick="$emit('edit')" style="">
        <div style="display: flex;gap: 6px;align-items:center;">
            <template v-if="comment">
                <a :id="`result-comment-${id}`" class="comment" @click.stop="$emit('edit-comment')"><i class="fa fa-comment-o" aria-hidden="true"></i></a>
                <b-popover :target="`result-comment-${id}`" triggers="hover" placement="right">
                    <div style="font-size: 13px;padding: 6px 8px;width: 200px;max-width: 200px;">
                        {{ comment }}
                    </div>
                </b-popover>
            </template>
            <div v-else aria-hidden="true" style="width: 14px"></div>
            <div style="display: flex;align-items: center;" :style="typeof result === 'number' ? 'justify-content:flex-end;' : 'justify-content: center'">
                <div v-if="result === 'afw'" class="color-code deep-orange-500" title="Afwezig"><span>afw</span></div>
                <div v-else-if="result === 'gafw'" class="color-code amber-700" title="Gewettigd afwezig"><span>gafw</span></div>
                <div v-else-if="result === null" class="color-code mod-none" title="Geen score gevonden"><span class="sr-only">Geen score gevonden</span></div>
                <div v-else style="">{{ result }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
            <a v-if="isOverwritten" style="color:#7ea2b9;text-shadow:1px 1px #e2eaee;" @click.stop="$emit('revert')" title="Breng terug naar bronresultaat"><i class="fa fa-undo" aria-hidden="true"></i><span class="sr-only">Breng terug naar bronresultaat</span></a>
            <div v-else aria-hidden="true" style="width: 14px"></div>
        </div>
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
    width: 40px;
}

.color-code > span {
    font-size: 14px;
    font-variant: all-small-caps;
    font-weight: 900;
    line-height: 12px;
}

.color-code.mod-none {
    /*--color: #d8e9db;
    background: transparent linear-gradient(135deg,var(--color) 10%,transparent 0,transparent 50%,var(--color) 0,var(--color) 60%,transparent 0,transparent) 0 0/7px 7px;*/
    background: transparent;
    /*border-radius: 5px;*/
    min-width: 40px;
    width: 100%;
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
    font-size:12px;
}
</style>
