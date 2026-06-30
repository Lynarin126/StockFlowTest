<script setup>
defineProps({
  title: String,
  items: Array,
  accent: { type: String, default: 'default' } // default | warn | danger
})
</script>

<template>
  <div class="widget" :class="`widget--${accent}`">
    <div class="widget-head">
      <span class="widget-dot"></span>
      <h6>{{ title }}</h6>
    </div>
    <ul v-if="items && items.length" class="widget-list">
      <li v-for="(item, idx) in items" :key="idx">
        <span v-for="(value, key) in item" :key="key" class="widget-field">
          <span class="widget-key">{{ key.replace(/_/g, ' ') }}</span>{{ value }}
        </span>
      </li>
    </ul>
    <p v-else class="widget-empty">No records</p>
  </div>
</template>

<style scoped>
.widget {
  background: #fff;
  border: 1px solid #E4E0D6;
  border-left: 3px solid #1B2430;
  border-radius: 6px;
  padding: 18px 18px 14px;
  height: 100%;
}
.widget--warn { border-left-color: #E8A33D; }
.widget--danger { border-left-color: #C2483A; }

.widget-head {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}
.widget-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #1B2430;
  flex-shrink: 0;
}
.widget--warn .widget-dot { background: #E8A33D; }
.widget--danger .widget-dot { background: #C2483A; }

.widget-head h6 {
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  font-size: 0.92rem;
  margin: 0;
  color: #1B2430;
}

.widget-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.widget-list li {
  border-top: 1px dashed #E4E0D6;
  padding: 9px 0;
  font-size: 0.84rem;
  line-height: 1.5;
}
.widget-list li:first-child { border-top: none; }

.widget-field { display: inline-block; margin-right: 10px; color: #1B2430; }
.widget-key {
  display: block;
  text-transform: uppercase;
  font-size: 0.66rem;
  letter-spacing: 0.05em;
  color: #8A93A3;
  font-weight: 600;
}

.widget-empty {
  color: #8A93A3;
  font-size: 0.85rem;
  margin: 0;
}
</style>