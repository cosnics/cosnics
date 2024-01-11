<template>
    <nav role="navigation">
        <ul class="rubrics-menu">
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Home' }"><router-link :to="{ name: 'Home' }"><span tabindex="-1">Home</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.path.startsWith('/builder') }"><router-link :to="{ name: 'Builder' }"><span tabindex="-1">Builder</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Preview' }"><router-link :to="{ name: 'Preview' }"><span tabindex="-1">Preview</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Entry' }"><router-link :to="{ name: 'Entry' }"><span tabindex="-1">Entry</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Results' }"><router-link :to="{ name: 'Results' }"><span tabindex="-1">Results</span></router-link></li>
        </ul>
    </nav>
</template>
<style lang="scss">
    html {
        font-size: 13px;
    }

    .chamilo-header {
        min-height: 78px;
        display: flex;
        flex-direction: row;
        overflow: hidden;

        .start {
            background-image: url("../../public/images/chamilo-start.png");
            width: 168px;
        }

        .middle {
            align-items: center;
            background-image: url("../../public/images/chamilo-fill.png");
            display: flex;
            flex: 1;
            padding-left: 9px;
            padding-top: 3px;
        }

        .end {
            align-items: center;
            background-image: url("../../public/images/chamilo-fill.png");
            display: flex;
            flex: 1;
            justify-content: flex-end;
            padding-right: 12px;
            padding-top: 18px;
        }
    }

    @media (max-width: 899px) {
        .chamilo-header {
            min-height: 51px;

            .start {
                background-image: url("../../public/images/chamilo-start-alt.png");
                width: 176px;
            }

            .middle {
                background-image: url("../../public/images/chamilo-fill-alt.png");
                flex: 1;
                min-width: 120px;
                padding-left: 1px;
                padding-top: 0;
            }

            .end {
                background-image: url("../../public/images/chamilo-fill-alt.png");
                padding-top: 14px;
            }
        }
    }

    @media only screen and (max-width: 690px) {
        .chamilo-header .start {
            min-width: 60px;
            width: 60px;
        }

        .chamilo-header .middle {
            padding-left: 4px;
            padding-top: 1px;
        }
    }

    @media only screen and (max-width: 540px) {
        .chamilo-header .start {
            display: none;
        }
        .chamilo-header .middle {
            padding-left: 14px;
        }
    }

    .container-breadcrumb {
        background-color: #f5f5f5;
        margin-bottom: 20px;

        ol.breadcrumb {
            margin-bottom: 0;
            padding-left: 0;
            padding-right: 0;
        }
    }

    footer.chamilo-footer {
        margin-top: 20px;
        padding: 15px 0;
        font-size: 90%;
        text-align: right;
        border-top: 1px solid #dddddd;
    }

    .chamilo-header {
        .rubrics-menu {
            cursor: pointer;
            display: flex;
            list-style: none;
            margin-left: 0;
            padding-left: 0;
        }

        .rubrics-demo-header {
            color: #fff;
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: .06875rem;
            line-height: 1.4375rem;
            /*text-shadow: 0 2px 3px hsl(240, 57%, 33%);*/
            word-spacing: 2px;
        }

        .rubrics-menu-item {
            margin-right: 10px;

            &.selected {
                margin-right: 10px;
            }

            &:not(:first-child) {
                padding-left: 10px;
            }

            a {
                padding-bottom: 4px;
                text-decoration: none;
                color: white;

                span {
                    padding: 2px;
                    outline: none;
                }

                &:focus {
                    outline: none;
                    span {
                        box-shadow: 0 0 0 1px white;
                    }
                }
            }

            &.selected a {
                color: white;
                border-bottom: 2px solid #ccc;
            }

            &:hover a {
                border-bottom: 2px solid white;
            }
        }
    }

    @media only screen and (max-width: 690px) {
        .chamilo-header .rubrics-menu-item {
            margin-right: 2px;

            &.selected {
                margin-right: 2px;
            }
        }
    }
</style>