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
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: .11rem;
            line-height: 2.3rem;
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