<template>
  <div class="popup-overlay" @click.self="closePopup">
    <div class="popup-content">
      <div class="popup-header" :style="{ backgroundColor: headerColor }">
        <dev class="text-header">{{ title }}</dev>
      </div>
      <div class="popup-body">
        <dev class="text-detail">{{ message }}</dev>
      </div>
      <div class="popup-footer">
        <button class="button-close" @click="confirmAction" v-if="closeButton">
          <span class="icon">✘</span>
          <span class="text">ปิด</span>
        </button>
        <button class="button-ok" @click="confirmAction" v-if="confirmButton">
          <span class="icon">✔</span>
          <span class="text">ตกลง</span>
        </button>
      </div>
    </div>
  </div>
</template>
  
<script>
export default {
  props: {
    title: String,
    message: String,
    headerColor: {
      type: String,
      default: '#FFFFFF' // Default color
    },
    confirmButton: {
      type: Boolean,
      default: false // Show button by default
    },
    closeButton: {
      type: Boolean,
      default: false // Show button by default
    }
  },
  methods: {
    closePopup() {
      this.$emit('close');
    },
    confirmAction() {
      this.$emit('confirm');
      this.closePopup();
    },
  },
};
</script>

<style scoped>

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.popup-content {
  background: white;
  border-radius: 8px;
  width: 450px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.popup-header {
  padding: 15px;
  text-align: left;
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.popup-body {
  padding: 20px;
  text-align: center;
  font-size: 14px;
  color: #555;
}

.popup-footer {
  padding: 10px;
  display: flex;
  justify-content: flex-end;
  border-top: 1px solid #eaeaea;
}

.button-ok {
  background-color: #ccefe8; /* Light aqua background */
  border: 1px solid #1e8b50; /* Green border */
  color: #1e8b50; /* Green text color */
  padding: 5px 15px;
  border-radius: 4px;
  font-family: 'Noto Sans', sans-serif;
  font-size: 14px;
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-left: 2px;
}

.button-ok .icon {
  margin-right: 5px;
  color: #1e8b50;
}

.button-ok:hover {
  background-color: #b8ddd6; /* Slightly darker on hover */
}

.button-close {
  background-color: #FFDBDB; /* Light aqua background */
  border: 1px solid #684C69; /* Green border */
  color: #684C69; /* Green text color */
  padding: 5px 15px;
  border-radius: 4px;
  font-family: 'Noto Sans', sans-serif;
  font-size: 14px;
  display: flex;
  align-items: center;
  cursor: pointer;
  margin-right: 2px;
}

.button-close .icon {
  margin-right: 5px;
  color: #684C69;
}

.button-close:hover {
  background-color: #f8d1d1; /* Slightly darker on hover */
}

.text-header {
  font-family: 'Noto Sans', sans-serif;
  font-size: 16px;
  color: #684C69; /* Slightly darker on hover */
}

.text-detail {
  font-family: 'Noto Sans', sans-serif;
  font-size: 14px;
  color: #684C69; /* Slightly darker on hover */
}

</style>