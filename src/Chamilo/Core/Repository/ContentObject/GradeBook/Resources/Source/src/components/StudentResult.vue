<i18n>
{
    "en": {
        "aabs": "aabs",
        "auth-absent": "Authorized absent",
        "edit-comment": "Edit comments",
        "no-score": "No score",
        "no-score-abbr": "n/a",
        "no-score-found": "No score found"
    },
    "nl": {
        "aabs": "gafw",
        "auth-absent": "Gewettigd afwezig",
        "edit-comment": "Wijzig opmerkingen",
        "no-score": "Geen score",
        "no-score-abbr": "n.b.",
        "no-score-found": "Geen score gevonden"
    }
}
</i18n>

<template>
    <div @dblclick="$emit('edit')">
        <div class="u-flex u-align-items-center u-gap-small">
            <template v-if="comment">
                <a :id="`result-comment-${id}`" class="fa fa-comment-o" @click.stop="$emit('edit-comment')" :title="$t('edit-comment')"><span class="sr-only">{{ $t('edit-comment') }}</span></a>
                <b-popover custom-class="gradebook-comment-popover" :target="`result-comment-${id}`" triggers="hover" placement="top">
                    <div class="comment">
                        <div class="comment-header">Feedback:</div>
                        {{ comment }}
                    </div>
                </b-popover>
            </template>
            <div class="result u-flex u-align-items-center u-justify-content-end" :class="{'overwritten-score': !isStandaloneScore && useOverwrittenFlag && isOverwritten, 'mod-aabs': result === 'aabs'}">
                <div v-if="result === 'aabs'" class="color-code amber-700" :title="$t('auth-absent')"><span>{{ $t('aabs') }}</span></div>
                <div v-else-if="result === null" class="color-code mod-none" :title="$t('no-score-found')"><i aria-hidden="true" class="fa fa-question" :class="{'mod-none': isStandaloneScore || !useOverwrittenFlag || !isOverwritten}"></i><span class="sr-only">{{ $t('no-score-found') }}</span></div>
                <div v-else>{{ result }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
            </div>
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
    @Prop({type: Boolean, default: false}) readonly useOverwrittenFlag!: boolean;
    @Prop({type: Boolean, default: false}) readonly isOverwritten!: boolean;
    @Prop({type: Boolean, default: false}) readonly isStandaloneScore!: boolean;
    @Prop({type: String, default: ''}) readonly comment!: string;
}
</script>

<style scoped lang="scss">
a {
    text-decoration: none;

    &.fa-undo {
        opacity: 0.5;

        &:hover, &:focus {
            color: #5e8ba6;
            opacity: 1;
        }
    }

    &.fa-comment-o {
        &:hover, &:focus {
            color: #476c85;
        }
    }
}

.result {
    width: 43px;
}

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
    justify-content: flex-end;
    width: 100%;

    .fa-question.mod-none {
        color: #777;
    }

    > span {
        font-weight: 500;
    }
}

.deep-orange-500 {
    --color: #ff5722;
    --text-color: white;
}

.amber-700 {
    --color: #ffa000;
    --text-color: white;
}

/*.mr-19 {
    margin-right: 19px;
}*/

.overwritten-score:not(.mod-aabs) {
    background-color: #f8fbfb;
    border: 1px solid #e6ecef;
    border-radius: 3px;
    color: #4086b5;
}

.comment {
    font-size: 13px;
    max-width: 200px;
    padding: 6px 8px;
    width: 200px;
}

.comment-header {
    color: #5885a3;
    font-size: 10px;
    margin-bottom: 2px;
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
<style>
.gradebook-comment-popover {
    border-color: #ebebeb;
    box-shadow: 0 3px 10px rgb(0 0 0 / 20%);
}

.gradebook-comment-popover.bs-popover-left {
    left: -5px!important;
}

.gradebook-comment-popover.bs-popover-right {
    left: 5px!important;
}

.gradebook-comment-popover.bs-popover-top {
    top: -10px!important;
}

.gradebook-comment-popover.bs-popover-bottom {
    top: 5px!important;
}
</style>