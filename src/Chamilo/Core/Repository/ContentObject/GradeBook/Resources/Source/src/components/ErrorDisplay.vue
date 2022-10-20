<template>
    <div class="u-contents">
        <div class="modal-overlay"></div>
        <div class="save-error u-flex u-justify-content-center">
            <div class="save-error-inner">
                <div class="errors-important u-flex u-align-items-baseline">
                    <i class="fa fa-exclamation-circle mod-icon"></i>
                    <div>
                        <slot></slot>
                        <div class="u-text-center mt-5"><button class="btn btn-success btn-sm" @click="$emit('close')">OK</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Vue} from 'vue-property-decorator';

@Component({
    name: 'error-display'
})
export default class ErrorDisplay extends Vue {
}
</script>

<style scoped>
.modal-overlay {
    background: rgba(55, 55, 55, 0.23);
    bottom: 0;
    left: 0;
    position: fixed;
    right: 0;
    top: 0;
    z-index: 1000;
}

.save-error {
    left: 0;
    position: fixed;
    right: 0;
    top: 75px;
    z-index: 1010;
}

.save-error-inner {
    background: #fdfbfb;
    border-radius: 3px;
    padding: 3px;
}

.errors-important {
    background-color: #fcf8f8;
    border: 1px solid #f2dede;
    border-left: 5px solid #d9534f;
    border-radius: 3px;
    color: #6e2c2b;
    gap: 7px;
    max-width: 75ch;
    padding: 10px;
}
.errors-important > .fa.mod-icon {
    color: #d9534f;
}

.mt-5 {
    margin-top: 5px;
}

.btn-success {
    padding: 3px 10px;
}
</style>