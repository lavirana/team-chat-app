<template>
  <div class="channel-container">
    <div class="channel-header">
      <h3># {{ channelName }}</h3>
    </div>
    <div class="messages-area">
      <p style="color: #aaa; text-align: center; margin-top: 40px;">
        Messages aayenge yahan — coming soon! 🚀
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute }      from 'vue-router'

const route       = useRoute()
const channelName = computed(() => route.params.channelId)
</script>

<style scoped>
.channel-container {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.channel-header {
    padding: 16px 20px;
    border-bottom: 1px solid #2d2d2d;
    background: #313338;
}
.channel-header h3 {
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
}
.messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}
</style>
