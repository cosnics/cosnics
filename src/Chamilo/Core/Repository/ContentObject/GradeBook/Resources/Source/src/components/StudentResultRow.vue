<i18n>
{
    "en": {
        "not-synchronized": "Not synchronized",
        "not-yet-updated": "Final score not yet updated"
    },
    "nl": {
        "not-synchronized": "Niet gesynchronizeerd",
        "not-yet-updated": "Eindcijfer nog niet geüpdated"
    }
}
</i18n>

<template>
    <tr class="table-row table-body-row">
        <td class="col-sticky table-student">
            <a v-if="gradeBookRootUrl" :href="`${gradeBookRootUrl}&gradebook_display_action=UserScores&user_id=${userId}`">{{ lastName }}, {{ firstName }}</a>
            <template v-else>{{ lastName }}, {{ firstName }}</template>
        </td>
        <template v-if="isSynchronized">
            <template v-for="(column, index) in columns">
                <td v-if="column.isScoreColumn" :key="`col-${index}`" :class="{'unreleased-score-cell': !column.released, 'uncounted-score-cell': !column.countsForEndResult, 'u-relative': column.isEditing}">
                    <student-result v-if="column.hasResult && !column.isEditing" :id="`result-${column.id}-${userId}`" :result="column.result" :comment="column.comment"
                                    :is-standalone-score="column.isStandaloneScore" :use-overwritten-flag="true" :is-overwritten="column.isOverwrittenResult"
                                    @edit="$emit('edit-score', column.id)" @edit-comment="$emit('edit-comment', column.id)"
                                    class="u-flex u-align-items-center u-justify-content-end u-cursor-pointer" :class="{'uncounted-score': !column.countsForEndResult}" />
                    <score-input v-if="column.isEditing" :menu-tab="scoreMenuTab" :score="column.result" :comment="column.comment" :use-revert="column.isOverwrittenResult && !column.isStandaloneScore"
                                 @menu-tab-changed="$emit('menu-tab-changed', $event)" @cancel="$emit('edit-canceled')"
                                 @comment-updated="$emit('comment-updated', {columnId: column.id, comment: $event})"
                                 @ok="$emit('result-updated', {columnId: column.id, value: $event})" @revert="$emit('result-reverted', column.id)" />
                </td>
                <td v-else :key="`col-${index}`"></td>
            </template>
            <td class="col-sticky table-student-total u-text-end" :class="{'unreleased-score-cell': gradeBook.hasUnreleasedScores, 'mod-needs-update': totalNeedsUpdate}">
                <i v-if="totalNeedsUpdate" class="fa fa-exclamation-circle" :title="$t('not-yet-updated')" aria-hidden="true"></i><span v-if="totalNeedsUpdate" class="sr-only">{{ $t('not-yet-updated') }}</span>{{ endResult|formatNum2 }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span>
            </td>
        </template>
        <td v-else :colspan="gradeBook.gradeColumns.length + 1" class="table-student-unsychronized">
            <div class="u-flex u-align-items-center u-justify-content-center">{{ $t('not-synchronized') }}</div>
        </td>
    </tr>
</template>
<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook, {Category, ColumnId, ItemId, ResultType, User} from '../domain/GradeBook';
import ScoreInput from './ScoreInput.vue'
import StudentResult from './StudentResult.vue'

interface EmptyColumn {
    isScoreColumn: false;
}

interface ScoreColumn {
    id: ColumnId;
    isScoreColumn: true;
    released: boolean;
    isStandaloneScore: boolean;
    countsForEndResult: boolean;
    isEditing: boolean;
    hasResult: boolean;
    result: ResultType;
    isOverwrittenResult: boolean;
    comment: string|null;
}

type Column = EmptyColumn|ScoreColumn;

@Component({
    name: 'student-result-row',
    components: {ScoreInput, StudentResult},
    filters: {
        formatNum2: function (v: number | null) {
            if (v === null) {
                return '';
            }
            return v.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }
})
export default class StudentResultRow extends Vue {

    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;
    @Prop({type: Object, required: true}) readonly user!: User;
    @Prop({type: String, default: ''}) readonly gradeBookRootUrl!: string;
    @Prop({type: [String, Number], default: null}) readonly excludeColumnId!: ColumnId|null;
    @Prop({type: Number, default: null}) readonly editStudentScoreId!: number|null;
    @Prop({type: [String, Number], default: null}) readonly editScoreId!: ItemId|null;
    @Prop({type: String, default: 'score'}) readonly scoreMenuTab!: string;
    @Prop({type: Boolean, default: false}) readonly showNullCategory!: boolean;

    get userId() {
        return this.user.id;
    }

    get firstName() {
        return this.user.firstName;
    }

    get lastName() {
        return this.user.lastName.toUpperCase();
    }

    get isSynchronized() {
        return !(this.gradeBook.gradeColumns.filter(column => column.id !== this.excludeColumnId).some(column => !this.gradeBook.hasResult(column.id, this.userId)));
    }

    get totalNeedsUpdate() {
        return this.gradeBook.userTotalNeedsUpdating(this.user);
    }

    get endResult() {
        return this.gradeBook.getEndResult(this.userId);
    }

    get displayedCategories(): Category[] {
        if (this.showNullCategory) {
            return [...this.gradeBook.categories, this.gradeBook.nullCategory];
        }
        return this.gradeBook.categories;
    }

    getColumnData(columnId: ColumnId): ScoreColumn {
        const gradeBook = this.gradeBook;
        const userId = this.userId;
        const column = gradeBook.getGradeColumn(columnId);
        if (!column) { throw new Error(`GradeColumn with id ${columnId} not found.`); }

        return {
            id: columnId,
            isScoreColumn: true,
            released: column.released,
            isStandaloneScore: column.type === 'standalone',
            countsForEndResult: column.countForEndResult,
            isEditing: this.editStudentScoreId === userId && this.editScoreId === columnId,
            hasResult: gradeBook.hasResult(columnId, userId),
            result: gradeBook.getResult(columnId, userId),
            isOverwrittenResult: gradeBook.isOverwrittenResult(columnId, userId),
            comment: gradeBook.getResultComment(columnId, userId)
        };
    }

    get columns(): Column[] {
        return this.displayedCategories.reduce((columns: Column[], currentCategory) => {
            if (currentCategory.columnIds.length) {
                return [...columns, ...currentCategory.columnIds.map(columnId => this.getColumnData(columnId))];
            }
            return [...columns, {isScoreColumn: false}];
        }, []);
    }
}
</script>

<style lang="scss" scoped>
.table-body-row {
    td {
        background-color: #fff;

        &.table-student-unsychronized {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%;
            background-clip: padding-box;
            border-left-color: transparent;
            border-right-color: transparent;
            color: #8f0000;
            font-style: italic;
            font-weight: 500;
        }

        &.uncounted-score-cell {
            background-color: #f7f9fd;
        }

        &.unreleased-score-cell {
            background-color: #fafafa;
        }

        &.unreleased-score-cell.uncounted-score-cell {
            background-color: #f6f8fb;
        }
    }

    &:first-child td {
        background: linear-gradient(to bottom, #e3eaed 0, #fff 4px);
        border-top: none;

        &.table-student-unsychronized {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%, linear-gradient(to bottom, #e3eaed 0, #fff 4px);
        }

        &.uncounted-score-cell {
            background: linear-gradient(to bottom, #dde5e9 0, #f7f9fd 4px);
        }

        &.unreleased-score-cell {
            background: linear-gradient(to bottom, #dde5e9 0, #fafafa 4px);
        }

        &.unreleased-score-cell.uncounted-score-cell {
            background: linear-gradient(to bottom, #dde5e9 0, #f6f8fb 4px);
        }
    }

    &.table-row {
        .table-student, .table-student-total {
            background-color: #fff;
        }

        .table-student-total.unreleased-score-cell {
            background-color: #fafafa;
        }

        &:first-child .col-sticky {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%, linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%,linear-gradient(to bottom, #e3eaed 0, #fff 4px);
        }

        &:first-child .table-student-total.unreleased-score-cell {
            background: linear-gradient(#ebebeb, #ebebeb) no-repeat left/1px 100%, linear-gradient(#ebebeb, #ebebeb) no-repeat right/1px 100%,linear-gradient(to bottom, #e3eaed 0, #fafafa 4px);
        }
    }

    .table-student-total {
        white-space: nowrap;

        &.mod-needs-update {
            color: #758895;
            font-style: italic;
        }

        .fa-exclamation-circle {
            color: #6f95ae;
            margin-right: 5px;
        }
    }
}

.uncounted-score {
    color: #777;
    font-style: italic;
}

.fa-percent {
    font-size: 1.1rem;
    margin-left: .15rem;
    opacity: .8;
}
</style>