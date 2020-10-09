<template>
    <div class="onoffswitch">
        <input type="checkbox" v-model="mValue" class="onoffswitch-checkbox" :id="id">
        <label class="onoffswitch-label" :for="id">
            <span class="onoffswitch-inner">
                <span class="onoffswitch-inner-before">{{ onValue }}</span>
                <span class="onoffswitch-inner-after">{{ offValue }}</span>
            </span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from "vue-property-decorator";

    @Component({
        name: 'on-off-switch'
    })
    export default class OnOffSwitch extends Vue {
        @Prop({type: Boolean, required: true}) readonly value!: boolean;
        @Prop({type: String, default: 'myId'}) readonly id!: string;
        @Prop({type: String, default: 'ON'}) readonly onValue!: string;
        @Prop({type: String, default: 'OFF'}) readonly offValue!: string;

        get mValue() {
            return this.value;
        }

        set mValue(val: boolean) {
            this.$emit('input', val);
        }
    }
</script>

<style lang="scss">
    $switch-color: hsla(200, 25%, 60%, 1);

    .onoffswitch {
        position: relative;
    }

    .onoffswitch-checkbox {
        display: none;
    }

    .onoffswitch-label {
        border: 1px solid $switch-color;
        border-radius: 3px;
        cursor: pointer;
        display: block;
        overflow: hidden;
    }

    .onoffswitch-inner {
        display: block;
        margin-left: -100%;
        transition: margin .2s ease-in 0s;
        width: 200%;
    }

    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }

    .onoffswitch-inner-before, .onoffswitch-inner-after {
        box-sizing: border-box;
        color: #fff;
        display: block;
        float: left;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-weight: 500;
        height: 20px;
        line-height: 18px;
        padding: 0;
        width: 50%;
    }

    .onoffswitch-inner-before {
        background-color: $switch-color;
        color: #fff;
        padding-left: 5px;
    }

    .onoffswitch-inner-after {
        background-color: #fff;
        color: #919191;
        padding-left: 18px;
        /*text-align: right;*/
    }

    .onoffswitch-switch {
        background: #ffffff;
        border: 1px solid $switch-color;
        border-radius: 3px;
        bottom: 0;
        display: block;
        margin: 0;
        position: absolute;
        right: calc(100% - 12px);
        top: 0;
        transition: all .2s ease-in 0s;
        width: 12px;
    }

    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0;
    }
</style>
