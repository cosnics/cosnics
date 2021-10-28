<template>
    <div :class="{'u-flex u-gap-small-2x m-fill': useBuilder}">
        <div v-if="useBuilder" class="u-flex u-gap-small-2x u-flex-wrap shapes-mw">
            <verification-shape @click="shape = 'r'" :selected="isRect"><rect x="-25" y="-25" width="50" height="50" fill="var(--fillcolor)"></rect></verification-shape>
            <verification-shape @click="shape = 'c'" :selected="isCircle"><circle cx="0" cy="0" r="25" fill="var(--fillcolor)"></circle></verification-shape>
            <verification-shape @click="shape = 'p11'" :selected="shape === 'p11'"><polygon points="25,-25 25,25 -25,25" fill="var(--fillcolor)"></polygon></verification-shape>
            <verification-shape @click="shape = 'p01'" :selected="shape === 'p01'"><polygon points="-25,-25 25,25 -25,25" fill="var(--fillcolor)"></polygon></verification-shape>
            <verification-shape @click="shape = 'p10'" :selected="shape === 'p10'"><polygon points="25,-25 25,25 -25,-25" fill="var(--fillcolor)"></polygon></verification-shape>
            <verification-shape @click="shape = 'p00'" :selected="shape === 'p00'"><polygon points="25,-25 -25,25 -25,-25" fill="var(--fillcolor)"></polygon></verification-shape>
        </div>
        <div v-if="useBuilder" class="u-flex u-gap-small-2x u-flex-wrap u-justify-content-space-between colors-mw">
            <div @click="colorIndex = index" v-for="(color, index) in colors" class="verification-color u-flex u-align-items-center u-justify-content-center" :style="`--color: ${color}`" :key="`vc-${index}`">
                <span v-if="index === colorIndex" class="verification-color-check"><i class="fa fa-check"></i></span>
            </div><div v-if="colors.length % 2"></div>
            <div class="shapestyle" :class="{'is-selected': hasFill }" @click="stroked = false">
                <svg width="36" height="36" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="36" height="36" fill="white" stroke="#e6e6e6" stroke-width="1"></rect>
                    <g transform="translate(18, 18)">
                        <rect x="-12.5" y="-12.5" width="25" height="25" fill="var(--fillcolor)"></rect>
                    </g>
                </svg>
            </div>
            <div class="shapestyle" :class="{'is-selected': hasStroke }" @click="stroked = true">
                <svg width="36" height="36" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="36" height="36" fill="white" stroke="#e6e6e6" stroke-width="1"></rect>
                    <g transform="translate(18, 18)">
                        <rect x="-12.5" y="-12.5" width="25" height="25" fill="none" stroke-width="3" stroke="currentColor"></rect>
                    </g>
                </svg>
            </div>
        </div>
        <div :class="{'icon-ml': useBuilder }">
            <svg width="120" height="120" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="0" width="120" height="120" fill="white" stroke="currentColor" stroke-width="1"></rect>
                <g transform="translate(60, 60)">
                    <rect v-if="isRect && hasFill" x="-50" y="-50" width="100" height="100" :fill="hexColor"></rect>
                    <rect v-else-if="isRect && hasStroke" x="-50" y="-50" width="100" height="100" fill="white" :stroke="hexColor" stroke-width="5"></rect>
                    <circle v-else-if="isCircle && hasFill" cx="0" cy="0" r="50" :fill="hexColor"></circle>
                    <circle v-else-if="isCircle && hasStroke" cx="0" cy="0" r="50" fill="white" :stroke="hexColor" stroke-width="5"></circle>
                    <polygon v-else-if="isPolygon && hasFill" :points="points" :fill="hexColor"></polygon>
                    <polygon v-else-if="isPolygon && hasStroke" :points="points" fill="white" :stroke="hexColor" stroke-width="5"></polygon>
                </g>
            </svg>
        </div>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
import VerificationShape from './VerificationShape.vue';

@Component({
    name: 'verification-icon',
    components: {
        VerificationShape
    },
})
export default class VerificationIcon extends Vue {
    readonly colors = ['#000000', '#ff0000', '#ffed00', '#306eff', '#ff69b4', '#228b22', '#fbb117', '#00ff00', '#d462ff'];

    position = 'q0';
    shapeType = 'r';
    shapeMeta = '00';
    colorIndex = 0;
    stroked = false;

    @Prop({type: Object, default: () => null}) readonly iconData!: any|null;
    @Prop({type: Boolean, default: false}) readonly useBuilder!: boolean;

    get verificationIconCode() {
        const colorMeta = this.padIndex(this.colorIndex);;
        return `${this.position}${this.shapeType}${this.shapeMeta}${this.hasFill ? `f${colorMeta}` : 'fxx'}${this.hasStroke ? `s${colorMeta}` : 'sxx'}`;
    }

    get isRect() {
        return this.shapeType === 'r';
    }

    get isCircle() {
        return this.shapeType === 'c';
    }

    get isPolygon() {
        return this.shapeType === 'p';
    }

    get shape() {
        return `${this.shapeType}${this.shapeMeta}`;
    }

    set shape(s: string) {
        this.shapeType = s[0];
        this.shapeMeta = s.slice(1, 3) || '00';
    }

    get points() {
        if (this.shapeType !== 'p') { return ''; }
        switch (this.shapeMeta) {
            case '00':
                return '50,-50 -50,50 -50,-50';
            case '01':
                return '-50,-50 50,50 -50,50';
            case '10':
                return '50,-50 50,50 -50,-50';
            case '11':
                return '50,-50 50,50 -50,50';
            default:
                return '';
        }
    }

    padIndex(index: number) {
        return index < 10 ? `0${index}` : String(index);
    }

    get hexColor() {
        return this.colors[this.colorIndex];
    }

    get hasFill() {
        return !this.stroked;
    }

    get hasStroke() {
        return this.stroked;
    }

    mounted() {
        this.parseIconData();
    }

    @Watch('iconData')
    parseIconData() {
        if (this.iconData) {
            const result = this.iconData.result;
            if (result) {
                this.position = result.slice(0, 2);
                this.shape = result.slice(2, 5);
                if (result.slice(5,8) !== 'fxx') {
                    this.stroked = false;
                    this.colorIndex = parseInt(result.slice(6,8));
                } else {
                    this.stroked = true;
                    this.colorIndex = parseInt(result.slice(9,11));
                }
                return;
            }
        }
        this.position = 'q0';
        this.shape = 'r00';
        this.colorIndex = 0;
        this.stroked = false;
    }
}
</script>

<style scoped>
.m-fill {
    --fillcolor: hsl(180, 15%, 87%);
}
.shapes-mw {
    max-width: 130px;
}
.colors-mw {
    max-width: 82px;
}
.icon-ml {
    margin-left: 30px;
}
.shapestyle {
    color: #d6dee0;
    cursor: pointer;
    height: 36px;
    transition: color 150ms ease;
}
.shapestyle:hover {
    color: #acb9b9;
    --fillcolor: #acb9b9;
}
.shapestyle.is-selected {
    color: #869898;
    --fillcolor: #869898;
}
.verification-color {
    background-color: var(--color);
    border-radius: 3px;
    cursor: pointer;
    height: 22px;
    width: 36px;
}
.verification-color:hover {
    box-shadow: 1px 1px 2px -1px #673ab7;
}
.verification-color-check {
    background: hsla(0, 0%, 0%, 0.18);
    border-radius: 50%;
    display: inline-block;
    height: 14px;
    position: relative;
    width: 14px;
}
.verification-color-check .fa-check {
    color: white;
    position: absolute;
    top: 1px;
}
rect, circle, polygon { transition: fill 150ms ease; }
</style>
