<i18n>
{
    "en": {
        "aabs": "aabs",
        "abs": "abs",
        "absent": "Absent",
        "auth-absent": "Authorized absent",
        "bring-to-source-result": "Bring back to source result",
        "edit-comment": "Edit comments",
        "no-score-found": "No score found"
    },
    "nl": {
        "aabs": "gafw",
        "abs": "afw",
        "absent": "Afwezig",
        "auth-absent": "Gewettigd afwezig",
        "bring-to-source-result": "Breng terug naar bronresultaat",
        "edit-comment": "Wijzig opmerkingen",
        "no-score-found": "Geen score gevonden"
    }
}
</i18n>

<template>
    <div @dblclick="$emit('edit')">
        <div class="u-flex u-align-items-center u-gap-small">
            <template v-if="comment">
                <a :id="`result-comment-${id}`" class="fa fa-comment-o" @click.stop="$emit('edit-comment')" :title="$t('edit-comment')"><span class="sr-only">{{ $t('edit-comment') }}</span></a>
                <b-popover :target="`result-comment-${id}`" triggers="hover" placement="right">
                    <div class="comment">{{ comment }}</div>
                </b-popover>
            </template>
            <div class="u-flex u-align-items-center" :class="[typeof result === 'number' ? 'u-justify-content-end' : 'u-justify-content-center', {'mr-19': !isOverwritten}]">
                <div v-if="result === 'afw'" class="color-code deep-orange-500" :title="$t('absent')"><span>{{ $t('abs') }}</span></div>
                <div v-else-if="result === 'gafw'" class="color-code amber-700" :title="$t('auth-absent')"><span>{{ $t('aabs') }}</span></div>
                <div v-else-if="result === null" class="color-code mod-none" :title="$t('no-score-found')"><span class="sr-only">{{ $t('no-score-found') }}</span></div>
                <div v-else>{{ result }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
            <a v-if="isOverwritten" class="fa fa-undo" @click.stop="$emit('revert')" :title="$t('bring-to-source-result')"><span class="sr-only">{{ $t('bring-to-source-result') }}</span></a>
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
    background: transparent;
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

.mr-19 {
    margin-right: 19px;
}

.comment {
    font-size: 13px;
    max-width: 200px;
    padding: 6px 8px;
    width: 200px;
}

.fa-comment-o {
    color: #5885a2;
    font-size: 12px;
}

.fa-undo {
    color: #7ea2b9;
}

.fa-comment-o, .fa-undo {
    text-shadow: 1px 1px #e2eaee;
}
</style>
