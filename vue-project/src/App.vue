<template>
    <div id="app">
        <!--<img alt="Vue logo" src="./assets/logo.png">-->
        <!--<HelloWorld msg="Welcome to Your Vue.js App"/>-->
        <!--<Countdown deadline="August 22, 2022"></Countdown>-->
        <YoutubeMovie :videoId="videoId"></YoutubeMovie>
    </div>
</template>

<script>
    // import HelloWorld from "./components/HelloWorld.vue";
    // import Countdown from "vuejs-countdown";

    import YoutubeMovie from "./components/YoutubeMovie.vue";
    import Vue from 'vue'
    import VueYoutube from "vue-youtube"

    Vue.use(VueYoutube);

    export default {
        name: "app",
        components: {
            YoutubeMovie
        },
        data() {
            return {
                videoId: document.getElementsByName('videoId')[0].value
            }
        }
    };
</script>

<style>
    #app {
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }
</style>
