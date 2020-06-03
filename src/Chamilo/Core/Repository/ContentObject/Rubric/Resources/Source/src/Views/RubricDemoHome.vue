<template>
    <div v-else id="app" class="demo-page">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <h1>Rubric Demo Page</h1>
        <router-link class="rubric-demo-item" :to="{ name: 'Builder' }"><i class="fa fa-institution"/>Builder</router-link>
        <router-link class="rubric-demo-item" :to="{ name: 'Entry' }"><i class="fa fa-edit"/>Entry</router-link>
        <router-link class="rubric-demo-item" :to="{ name: 'Results' }"><i class="fa fa-calculator"/>Results</router-link>
    </div>
</template>
<style lang="scss">
    .demo-page {
        /*margin-bottom: -20px;
        margin-top: -20px;*/
        padding: 0 2.5em;
        border-top: 1px solid #d6d6d6;
        color: #555;

        h1 {
            font-size: 2rem;
        }

        .rubric-demo-item {
            font-size: 1.7rem;
            margin: .5em 0;
            background: #e0e0e0;
            width: 10em;
            padding: .35em .6em;
            border-radius: $border-radius;
            cursor: pointer;
            transition: 200ms background;
            text-decoration: none;

            .fa {
                font-size: 1.4rem;
                margin-right: .4em;
            }

            &:hover {
                background: #d6d9d7;
            }

        }
    }
</style>