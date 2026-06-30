<script setup>
defineProps({
  title: String,
  items: Array
})
</script>

<template>
  <div class="card widget-card shadow-sm border-0 h-100">
    <div class="card-body">
      <h6 class="fw-semibold mb-3">{{ title }}</h6>
      <ul v-if="items && items.length" class="list-unstyled mb-0">
        <li v-for="(item, idx) in items" :key="idx" class="widget-row">
          <span v-for="(value, key) in item" :key="key" class="me-2">
            <span class="text-muted">{{ key }}:</span> {{ value }}
          </span>
        </li>
      </ul>
      <p v-else class="text-muted small mb-0">មិនមានទិន្នន័យ</p>
    </div>
  </div>
</template>

<style scoped>
.widget-row {
  border-bottom: 1px solid #f1f1f1;
  padding: 6px 0;
  font-size: 0.9rem;
}
.widget-row:last-child {
  border-bottom: none;
}
</style>