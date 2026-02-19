<script setup>
import {ref} from 'vue';
const secs = ref(0);
const minutes = Math.round(secs/60);
const hours = Math.round(minutes/60);
const startTime = ref(null);
const endTime = ref(null);
const timer = setInterval(()=>{
    secs+=1;
},1000)
const endTimer = ()=>{
    clearInterval(timer)
}
</script>
<template>
{{ hours }} : {{ minutes }} : {{ secs }}
<button @click="timer">Start Timer</button>
<button @click="endTimer">End timer</button>
</template>
<style scoped>
</style>