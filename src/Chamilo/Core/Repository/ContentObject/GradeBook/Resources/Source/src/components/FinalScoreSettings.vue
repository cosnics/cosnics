<i18n>
{
    "en": {
        "close": "Close",
        "display-total": "Max score",
        "final-score": "Final score",
        "final-score-settings": "Final score settings",
        "settings": "Settings"
    },
    "nl": {
        "close": "Sluiten",
        "display-total": "Max score",
        "final-score": "Eindcijfer",
        "final-score-settings": "Eindscore-instellingen",
        "settings": "Instellingen"
    }
}
</i18n>

<template>
    <div class="modal-wrapper" role="dialog" aria-modal="true" :aria-label="$t('final-score-settings')">
        <div class="modal-content">
            <div class="u-flex u-align-items-baseline u-justify-content-between modal-header">
                <h4 style="font-size: 20px;margin-block: 0">{{ $t('final-score') }}</h4>
                <button class="btn-close u-ml-auto" @click="$emit('close')" :title="$t('close')"><i class="fa fa-times" aria-hidden="true"></i><span class="sr-only">{{ $t('close') }}</span></button>
            </div>
            <div class="modal-body mb-5">
                <div class="mb-10">
                    <label for="display-total" class="settings-label u-block">{{ $t('display-total') }}:</label>
                    <div class="number-input u-relative">
                        <input type="number" placeholder="100" step="1" id="display-total" :value="gradeBook.displayTotal" @input="setDisplayTotal" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-overlay" @click="$emit('close')"></div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';
import GradeBook from '../domain/GradeBook';

@Component({
    components: { }
})
export default class FinalScoreSettings extends Vue {
    @Prop({type: GradeBook, required: true}) readonly gradeBook!: GradeBook;

    setDisplayTotal(event: Event) {
        const displayTotal = parseFloat((event!.target as HTMLInputElement).value);
        this.gradeBook.displayTotal = isNaN(displayTotal) ? null : displayTotal;
        this.$emit('change-display-total');
    }
}
</script>

<style lang="scss" scoped>
.mb-5 {
    margin-bottom: 5px;
}

.modal-wrapper {
    inset: 0;
    position: fixed;
    z-index: 2;
}

.modal-content {
    background-color: white;
    border-radius: 3px;
    box-shadow: 0 6px 12px #999;
    margin: 20px auto;
    max-width: 800px;
}

.modal-overlay {
    background: #000;
    height: 100vh;
    left: 0;
    position:fixed;
    opacity: .2;
    top: 0;
    width: 100vw;
    z-index:-1;
}

.btn-close {
    background: none;
    border: none;
    font-size: 2rem;
}

h5 {
    border-bottom: 1px dotted #c1d1cf;
    color:#487771;
    font-size: 20px;
    font-variant: all-small-caps;
    padding-bottom: .5rem;
}

input[type="text"], input[type="number"] {
    border: 1px solid #ced4da;
    border-radius: .2rem;
    color: #333;
    min-height: 24px;
    padding: 4px 18px 4px 4px;
}

input[type="text"] {
    font-size: 20px;
}

input[type="number"] {
    -moz-appearance: textfield;
    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
}

input:focus {
    outline: 0;
    border: 1px solid #6ac;
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
}

label {
    font-size: 1.4rem;
}

.settings-label {
    color: #245c55;
}
</style>