<script setup>
import { ref } from 'vue'
import WidgetCard from './components/WidgetCard.vue'

const selectedRole = ref('')
const loggedInRole = ref(null)
const dashboardData = ref(null)
const loading = ref(false)
const errorMsg = ref('')

async function login() {
  if (!selectedRole.value) return
  loading.value = true
  errorMsg.value = ''
  try {
    const res = await fetch(`http://localhost:8000/api/dashboard?role=${selectedRole.value}`)
    if (!res.ok) throw new Error('Failed to fetch dashboard data')
    const data = await res.json()
    dashboardData.value = data
    loggedInRole.value = selectedRole.value
  } catch (err) {
    errorMsg.value = 'មិនអាចភ្ជាប់ទៅ API បានទេ៖ ' + err.message
  } finally {
    loading.value = false
  }
}

function logout() {
  loggedInRole.value = null
  dashboardData.value = null
  selectedRole.value = ''
}
</script>

<template>
  <!-- ===================== HERO ===================== -->
  <section class="hero-section text-center py-5">
    <div class="container py-5">
      <h1 class="display-5 fw-bold mb-3">Smart Inventory & Accounts System</h1>
      <p class="lead mb-4">
        គ្រប់គ្រងស្តុក ការទិញចូល ការចេញវិក្កយបត្រ និងគណនេយ្យ ក្នុងកម្មវិធីតែមួយ
      </p>
      <a href="#login" class="btn btn-light btn-lg fw-semibold">ចាប់ផ្តើមប្រើប្រាស់</a>
    </div>
  </section>

  <!-- ===================== FEATURES ===================== -->
  <section class="container py-5">
    <h2 class="text-center fw-bold mb-5">មុខងារសំខាន់ៗ</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="feature-icon mb-3">📦</div>
            <h5 class="card-title fw-semibold">ការគ្រប់គ្រងស្តុក</h5>
            <p class="card-text text-muted small">តាមដានទំនិញចូល-ចេញ និងការផុតកំណត់ឱ្យបានត្រឹមត្រូវ</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="feature-icon mb-3">🧾</div>
            <h5 class="card-title fw-semibold">ការចេញវិក្កយបត្រ</h5>
            <p class="card-text text-muted small">បង្កើតវិក្កយបត្រសម្រាប់អ្នកផ្គត់ផ្គង់ និងអតិថិជន</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 text-center shadow-sm border-0">
          <div class="card-body">
            <div class="feature-icon mb-3">🔐</div>
            <h5 class="card-title fw-semibold">សិទ្ធិតាមតួនាទី</h5>
            <p class="card-text text-muted small">បង្ហាញទិន្នន័យខុសៗគ្នាទៅតាមតួនាទីអ្នកប្រើប្រាស់</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== CTA ===================== -->
  <section class="cta-section py-5">
    <div class="container text-center">
      <h2 class="fw-bold mb-2">ត្រៀមខ្លួនរួចហើយ?</h2>
      <p class="text-muted mb-0">ចូលប្រើតាមតួនាទីរបស់អ្នកខាងក្រោម ដើម្បីមើល Dashboard</p>
    </div>
  </section>

  <!-- ===================== LOGIN MOCKUP ===================== -->
  <section id="login" class="container py-5" style="max-width: 480px;">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h4 class="text-center fw-semibold mb-4">ចូលប្រើប្រាស់ (Login Mockup)</h4>

        <label class="form-label fw-medium">ជ្រើសរើស Role</label>
        <select v-model="selectedRole" class="form-select mb-3">
          <option disabled value="">-- សូមជ្រើសរើស --</option>
          <option value="SUPER_ADMIN">SUPER_ADMIN</option>
          <option value="WAREHOUSE_MANAGER">WAREHOUSE_MANAGER</option>
          <option value="ACCOUNTS">ACCOUNTS</option>
        </select>

        <button
          @click="login"
          :disabled="!selectedRole || loading"
          class="btn btn-primary w-100 fw-semibold">
          {{ loading ? 'កំពុងផ្ទុក...' : 'Login' }}
        </button>

        <div v-if="errorMsg" class="alert alert-danger mt-3 mb-0 text-center py-2">{{ errorMsg }}</div>
      </div>
    </div>
  </section>

  <!-- ===================== DASHBOARD ===================== -->
  <section v-if="loggedInRole" class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold mb-0">Dashboard — {{ loggedInRole }}</h3>
      <button @click="logout" class="btn btn-link text-decoration-none">Logout</button>
    </div>

    <!-- SUPER_ADMIN: all widgets -->
    <template v-if="loggedInRole === 'SUPER_ADMIN'">
      <h5 class="fw-semibold text-secondary mb-3">Warehouse</h5>
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <WidgetCard title="Inventory In/Out" :items="dashboardData?.warehouse?.inventory_in_out" />
        </div>
        <div class="col-md-6 col-lg-3">
          <WidgetCard title="Purchase Tracking" :items="dashboardData?.warehouse?.purchase_tracking" />
        </div>
        <div class="col-md-6 col-lg-3">
          <WidgetCard title="Goods Expiry" :items="dashboardData?.warehouse?.goods_expiry" />
        </div>
        <div class="col-md-6 col-lg-3">
          <WidgetCard title="Low Stock Alert" :items="dashboardData?.warehouse?.inventory_low_alert" />
        </div>
      </div>

      <h5 class="fw-semibold text-secondary mb-3">Accounts</h5>
      <div class="row g-4">
        <div class="col-md-4">
          <WidgetCard title="Billing - Supplier" :items="dashboardData?.accounts?.billing_for_supplier" />
        </div>
        <div class="col-md-4">
          <WidgetCard title="Billing - Stock Out" :items="dashboardData?.accounts?.billing_for_stock_out" />
        </div>
        <div class="col-md-4">
          <WidgetCard title="Billing - Internal Team" :items="dashboardData?.accounts?.billing_for_internal_team" />
        </div>
      </div>
    </template>

    <!-- WAREHOUSE_MANAGER: 5 widgets -->
    <template v-else-if="loggedInRole === 'WAREHOUSE_MANAGER'">
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <WidgetCard title="Inventory In/Out" :items="dashboardData?.warehouse?.inventory_in_out" />
        </div>
        <div class="col-md-6 col-lg-4">
          <WidgetCard title="Purchase Tracking" :items="dashboardData?.warehouse?.purchase_tracking" />
        </div>
        <div class="col-md-6 col-lg-4">
          <WidgetCard title="Goods Expiry" :items="dashboardData?.warehouse?.goods_expiry" />
        </div>
        <div class="col-md-6 col-lg-4">
          <WidgetCard title="Low Stock Alert" :items="dashboardData?.warehouse?.inventory_low_alert" />
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card widget-card shadow-sm border-0">
            <div class="card-body">
              <h6 class="fw-semibold mb-3">Summary</h6>
              <p class="small text-muted mb-1">
                Total Low-Stock Items: {{ dashboardData?.warehouse?.inventory_low_alert?.length || 0 }}
              </p>
              <p class="small text-muted mb-0">
                Total Pending Purchases:
                {{ dashboardData?.warehouse?.purchase_tracking?.filter(p => p.status === 'Pending').length || 0 }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ACCOUNTS: 3 widgets -->
    <template v-else-if="loggedInRole === 'ACCOUNTS'">
      <div class="row g-4">
        <div class="col-md-4">
          <WidgetCard title="Billing - Supplier" :items="dashboardData?.accounts?.billing_for_supplier" />
        </div>
        <div class="col-md-4">
          <WidgetCard title="Billing - Stock Out" :items="dashboardData?.accounts?.billing_for_stock_out" />
        </div>
        <div class="col-md-4">
          <WidgetCard title="Billing - Internal Team" :items="dashboardData?.accounts?.billing_for_internal_team" />
        </div>
      </div>
    </template>
  </section>
</template>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #4f46e5, #6366f1);
  color: #fff;
}
.cta-section {
  background-color: #eef0fb;
}
.feature-icon {
  font-size: 2rem;
}
</style>