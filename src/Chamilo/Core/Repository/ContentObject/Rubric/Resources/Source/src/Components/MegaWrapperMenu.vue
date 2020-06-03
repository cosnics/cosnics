<template>
    <nav role="navigation">
        <ul class="rubrics-menu">
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Home' }"><router-link :to="{ name: 'Home' }"><span tabindex="-1">Home</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.path.startsWith('/builder') }"><router-link :to="{ name: 'Builder' }"><span tabindex="-1">Builder</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Entry' }"><router-link :to="{ name: 'Entry' }"><span tabindex="-1">Entry</span></router-link></li>
            <li class="rubrics-menu-item" :class="{ selected: $route.name === 'Results' }"><router-link :to="{ name: 'Results' }"><span tabindex="-1">Builder</span></router-link></li>
        </ul>
    </nav>
</template>
<style lang="scss">
    .chamilo-header {
        .rubrics-menu {
            display: flex;
            margin-top: 48px;
            list-style: none;
            cursor: pointer;
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
    @media only screen and (max-width: 899px) {
        .chamilo-header {
            position: relative;
        }
        .rubrics-menu {
            position: absolute;
            top: 18px;
            margin-top: 0px!important;
        }
    }
    @media only screen and (max-width: 500px) {
        .chamilo-header .start {
            display: none;
        }
        .rubrics-menu {
            left: -25px;
        }
    }
</style>