<script setup>
import { ref, computed } from 'vue'
import WidgetCard from './components/WidgetCard.vue'

const selectedRole = ref('')
const loggedInRole = ref(null)
const dashboardData = ref(null)
const loading = ref(false)
const errorMsg = ref('')

const roleMeta = {
  SUPER_ADMIN: { label: 'Super Admin', tag: 'Full manifest access', code: 'SA' },
  WAREHOUSE_MANAGER: { label: 'Warehouse Manager', tag: 'Warehouse floor only', code: 'WM' },
  ACCOUNTS: { label: 'Accounts', tag: 'Ledger & billing only', code: 'AC' },
}

const activeMeta = computed(() => loggedInRole.value ? roleMeta[loggedInRole.value] : null)

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
    errorMsg.value = 'Could not reach the API: ' + err.message
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
  <div class="manifest-app">

    <!-- ===================== HERO ===================== -->
    <section class="hero">
      <div class="hero-grain"></div>
      <div class="container hero-inner">
        <div class="hero-tag">MANIFEST · STOCK & LEDGER CONTROL</div>
        <h1 class="hero-title">
          One manifest.<br>
          <span class="hero-title-accent">Every crate, every invoice.</span>
        </h1>
        <p class="hero-sub">
          Track what moves through the warehouse and what moves through the books —
          scoped to exactly what each role needs to see.
        </p>
        <a href="#login" class="btn-manifest">Open the manifest →</a>

        <div class="hero-strip">
          <div class="strip-item"><span class="strip-num">04</span><span class="strip-label">Warehouse feeds</span></div>
          <div class="strip-divider"></div>
          <div class="strip-item"><span class="strip-num">03</span><span class="strip-label">Billing ledgers</span></div>
          <div class="strip-divider"></div>
          <div class="strip-item"><span class="strip-num">03</span><span class="strip-label">Access roles</span></div>
        </div>
      </div>
    </section>

    <!-- ===================== FEATURES ===================== -->
    <section class="container features">
      <div class="section-head">
        <span class="section-eyebrow">What's on the floor</span>
        <h2 class="section-title">Built around the two halves of the business</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-mark">IN/OUT</div>
            <h5>Stock movement</h5>
            <p>Every unit logged the moment it crosses the dock door, in or out.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-mark">LEDGER</div>
            <h5>Billing & invoices</h5>
            <p>Supplier bills, stock-out charges, and internal recharges in one place.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="feature-mark">ACCESS</div>
            <h5>Role-scoped views</h5>
            <p>Warehouse staff see the floor. Accounts see the books. Admins see both.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===================== CTA ===================== -->
    <section class="cta-band">
      <div class="container text-center">
        <h2 class="cta-title">Ready to check the manifest?</h2>
        <p class="cta-sub">Sign in below with a role to see its dashboard.</p>
      </div>
    </section>

    <!-- ===================== LOGIN ===================== -->
    <section id="login" class="container login-section">
      <div class="login-card">
        <div class="login-card-head">
          <span class="login-kicker">ACCESS</span>
          <h4>Sign in to the manifest</h4>
        </div>

        <label class="form-label-manifest">Role</label>
        <select v-model="selectedRole" class="form-select form-select-manifest mb-3">
          <option disabled value="">Choose a role…</option>
          <option value="SUPER_ADMIN">Super Admin — full access</option>
          <option value="WAREHOUSE_MANAGER">Warehouse Manager — warehouse only</option>
          <option value="ACCOUNTS">Accounts — billing only</option>
        </select>

        <button
          @click="login"
          :disabled="!selectedRole || loading"
          class="btn-manifest btn-manifest-block">
          {{ loading ? 'Checking manifest…' : 'Sign in' }}
        </button>

        <div v-if="errorMsg" class="error-banner">{{ errorMsg }}</div>
      </div>
    </section>

    <!-- ===================== DASHBOARD ===================== -->
    <section v-if="loggedInRole" class="container dashboard-section">
      <div class="dashboard-head">
        <div class="role-chip">
          <span class="role-chip-code">{{ activeMeta.code }}</span>
          <div>
            <div class="role-chip-label">{{ activeMeta.label }}</div>
            <div class="role-chip-tag">{{ activeMeta.tag }}</div>
          </div>
        </div>
        <button @click="logout" class="link-manifest">Sign out</button>
      </div>

      <!-- SUPER_ADMIN -->
      <template v-if="loggedInRole === 'SUPER_ADMIN'">
        <h6 class="group-label">Warehouse</h6>
        <div class="row g-4 mb-5">
          <div class="col-md-6 col-lg-3">
            <WidgetCard title="Inventory In/Out" :items="dashboardData?.warehouse?.inventory_in_out" />
          </div>
          <div class="col-md-6 col-lg-3">
            <WidgetCard title="Purchase Tracking" :items="dashboardData?.warehouse?.purchase_tracking" />
          </div>
          <div class="col-md-6 col-lg-3">
            <WidgetCard title="Goods Expiry" :items="dashboardData?.warehouse?.goods_expiry" accent="warn" />
          </div>
          <div class="col-md-6 col-lg-3">
            <WidgetCard title="Low Stock Alert" :items="dashboardData?.warehouse?.inventory_low_alert" accent="danger" />
          </div>
        </div>

        <h6 class="group-label">Accounts</h6>
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

      <!-- WAREHOUSE_MANAGER -->
      <template v-else-if="loggedInRole === 'WAREHOUSE_MANAGER'">
        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <WidgetCard title="Inventory In/Out" :items="dashboardData?.warehouse?.inventory_in_out" />
          </div>
          <div class="col-md-6 col-lg-4">
            <WidgetCard title="Purchase Tracking" :items="dashboardData?.warehouse?.purchase_tracking" />
          </div>
          <div class="col-md-6 col-lg-4">
            <WidgetCard title="Goods Expiry" :items="dashboardData?.warehouse?.goods_expiry" accent="warn" />
          </div>
          <div class="col-md-6 col-lg-4">
            <WidgetCard title="Low Stock Alert" :items="dashboardData?.warehouse?.inventory_low_alert" accent="danger" />
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="summary-card">
              <h6>Floor summary</h6>
              <div class="summary-row">
                <span>Low-stock items</span>
                <strong>{{ dashboardData?.warehouse?.inventory_low_alert?.length || 0 }}</strong>
              </div>
              <div class="summary-row">
                <span>Pending purchase orders</span>
                <strong>{{ dashboardData?.warehouse?.purchase_tracking?.filter(p => p.status === 'Pending').length || 0 }}</strong>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- ACCOUNTS -->
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

    <footer class="footer">
      <div class="container">Manifest · internal stock & ledger console</div>
    </footer>

  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap');

:root {
  --ink: #1B2430;
  --ink-soft: #4C5A6B;
  --paper: #F7F5F0;
  --paper-card: #FFFFFF;
  --amber: #E8A33D;
  --amber-deep: #C97F1E;
  --teal: #3FA796;
  --danger: #C2483A;
  --line: #E4E0D6;
}

body {
  background: var(--paper);
  color: var(--ink);
  font-family: 'Inter', sans-serif;
}

.manifest-app h1, .manifest-app h2, .manifest-app h4, .manifest-app h5, .manifest-app h6,
.hero-title, .section-title, .feature-card h5, .login-card-head h4 {
  font-family: 'Sora', sans-serif;
}

.container { max-width: 1080px; }

/* ---------- HERO ---------- */
.hero {
  position: relative;
  background: var(--ink);
  color: var(--paper);
  overflow: hidden;
  padding: 96px 0 64px;
}
.hero-grain {
  position: absolute;
  inset: 0;
  background-image:
    repeating-linear-gradient(0deg, rgba(232,163,61,0.06) 0px, rgba(232,163,61,0.06) 1px, transparent 1px, transparent 28px),
    repeating-linear-gradient(90deg, rgba(232,163,61,0.06) 0px, rgba(232,163,61,0.06) 1px, transparent 1px, transparent 28px);
  pointer-events: none;
}
.hero-inner { position: relative; z-index: 1; }
.hero-tag {
  font-size: 0.78rem;
  letter-spacing: 0.14em;
  color: var(--amber);
  font-weight: 600;
  margin-bottom: 22px;
}
.hero-title {
  font-size: clamp(2.2rem, 5vw, 3.4rem);
  font-weight: 800;
  line-height: 1.12;
  margin-bottom: 20px;
}
.hero-title-accent { color: var(--amber); }
.hero-sub {
  max-width: 540px;
  color: #C9CEDA;
  font-size: 1.05rem;
  line-height: 1.6;
  margin-bottom: 32px;
}
.btn-manifest {
  display: inline-block;
  background: var(--amber);
  color: var(--ink);
  font-weight: 700;
  padding: 13px 28px;
  border-radius: 4px;
  text-decoration: none;
  border: none;
  transition: background 0.15s ease, transform 0.15s ease;
}
.btn-manifest:hover { background: var(--amber-deep); color: var(--ink); transform: translateY(-1px); }
.btn-manifest-block { width: 100%; text-align: center; }

.hero-strip {
  display: flex;
  align-items: center;
  gap: 28px;
  margin-top: 56px;
  padding-top: 28px;
  border-top: 1px solid rgba(255,255,255,0.14);
}
.strip-item { display: flex; flex-direction: column; }
.strip-num { font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.4rem; color: var(--amber); }
.strip-label { font-size: 0.82rem; color: #9CA4B5; }
.strip-divider { width: 1px; height: 32px; background: rgba(255,255,255,0.16); }

/* ---------- FEATURES ---------- */
.features { padding: 76px 0 56px; }
.section-head { margin-bottom: 40px; }
.section-eyebrow {
  display: block;
  font-size: 0.78rem;
  letter-spacing: 0.12em;
  color: var(--amber-deep);
  font-weight: 600;
  margin-bottom: 8px;
}
.section-title { font-size: 1.7rem; font-weight: 700; max-width: 520px; }

.feature-card {
  background: var(--paper-card);
  border: 1px solid var(--line);
  border-radius: 6px;
  padding: 28px 24px;
  height: 100%;
}
.feature-mark {
  display: inline-block;
  font-family: 'Sora', sans-serif;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: var(--ink);
  background: rgba(232,163,61,0.25);
  padding: 4px 10px;
  border-radius: 3px;
  margin-bottom: 16px;
}
.feature-card h5 { font-weight: 700; margin-bottom: 8px; }
.feature-card p { color: var(--ink-soft); font-size: 0.92rem; margin: 0; }

/* ---------- CTA ---------- */
.cta-band { background: #EFE9DB; padding: 56px 0; }
.cta-title { font-weight: 700; font-size: 1.6rem; margin-bottom: 6px; }
.cta-sub { color: var(--ink-soft); margin: 0; }

/* ---------- LOGIN ---------- */
.login-section { max-width: 460px; padding: 64px 0; }
.login-card {
  background: var(--paper-card);
  border: 1px solid var(--line);
  border-radius: 8px;
  padding: 32px;
}
.login-kicker {
  font-size: 0.72rem;
  letter-spacing: 0.12em;
  font-weight: 700;
  color: var(--amber-deep);
}
.login-card-head h4 { font-weight: 700; margin-top: 6px; margin-bottom: 24px; }
.form-label-manifest {
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--ink-soft);
  margin-bottom: 6px;
  display: block;
}
.form-select-manifest {
  border: 1px solid var(--line);
  border-radius: 5px;
  padding: 10px 12px;
  font-size: 0.95rem;
}
.form-select-manifest:focus {
  border-color: var(--amber);
  box-shadow: 0 0 0 3px rgba(232,163,61,0.25);
}
.error-banner {
  margin-top: 14px;
  background: #FBEAE7;
  color: var(--danger);
  border: 1px solid #F1C7BF;
  border-radius: 5px;
  padding: 10px 14px;
  font-size: 0.88rem;
  text-align: center;
}

/* ---------- DASHBOARD ---------- */
.dashboard-section { padding: 16px 0 80px; }
.dashboard-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}
.role-chip { display: flex; align-items: center; gap: 14px; }
.role-chip-code {
  width: 44px; height: 44px;
  display: flex; align-items: center; justify-content: center;
  background: var(--ink);
  color: var(--amber);
  font-family: 'Sora', sans-serif;
  font-weight: 700;
  border-radius: 6px;
  font-size: 0.95rem;
}
.role-chip-label { font-weight: 700; font-size: 1.15rem; font-family: 'Sora', sans-serif; }
.role-chip-tag { font-size: 0.82rem; color: var(--ink-soft); }
.link-manifest {
  background: none; border: none;
  color: var(--amber-deep);
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
}
.link-manifest:hover { text-decoration: underline; }

.group-label {
  text-transform: uppercase;
  font-size: 0.78rem;
  letter-spacing: 0.1em;
  color: var(--ink-soft);
  font-weight: 700;
  margin-bottom: 16px;
}

.summary-card {
  background: var(--ink);
  color: var(--paper);
  border-radius: 6px;
  padding: 20px;
  height: 100%;
}
.summary-card h6 { font-weight: 700; margin-bottom: 14px; color: var(--amber); }
.summary-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.88rem;
  padding: 8px 0;
  border-bottom: 1px solid rgba(255,255,255,0.12);
}
.summary-row:last-child { border-bottom: none; }

.footer {
  border-top: 1px solid var(--line);
  padding: 22px 0;
  text-align: center;
  font-size: 0.82rem;
  color: var(--ink-soft);
}
</style>