
<template>
  <header class = "custom-image">
    <img class ="custom-image" src="./assets/SIS.png" >
  </header>

  <body>
    <HelloWorld msg="Welcome to Your Vue.js App"/>
  </body>
</template>

<script>
import HelloWorld from './components/HelloWorld.vue'
import 'material-icons/iconfont/material-icons.css'
import { Options, Vue } from 'vue-class-component';
@Options({
  components: {
    HelloWorld,
  },
})
export default class App extends Vue {}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50
}

.custom-image {
        width: 100px;
        height: 100px;
        margin-left: 5px;
    }
</style>

