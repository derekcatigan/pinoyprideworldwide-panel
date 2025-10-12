<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import Navbar from '@/components/LayoutHeader.vue'
import Sidebar from '@/components/LayoutSidebar.vue'

/**
 * Behavior:
 * - isCollapsed: desktop collapse toggle (true => sidebar collapsed on desktop)
 * - mobileOpen: mobile drawer (true => sidebar visible as overlay drawer on mobile)
 *
 * toggleSidebar() decides which state to toggle based on viewport width.
 */
const isCollapsed = ref(false)
const mobileOpen = ref(false)

function toggleSidebar() {
    // if mobile viewport, open/close overlay drawer
    if (typeof window !== 'undefined' && window.innerWidth <= 763) {
        mobileOpen.value = !mobileOpen.value
    } else {
        // desktop: collapse / expand the grid column
        isCollapsed.value = !isCollapsed.value
    }
}

function closeMobile() {
    mobileOpen.value = false
}

// lock body scroll while mobileOpen is true
watch(mobileOpen, (val) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = val ? 'hidden' : ''
    }
})

// close on Escape key
function onKey(e) {
    if (e.key === 'Escape') mobileOpen.value = false
}
onMounted(() => window.addEventListener('keydown', onKey))
onBeforeUnmount(() => window.removeEventListener('keydown', onKey))
</script>

<template>
    <div :class="['layout', { 'layout-collapsed': isCollapsed }]">
        <!-- Header -->
        <header class="nav">
            <Navbar @toggle-sidebar="toggleSidebar" />
        </header>

        <!-- Sidebar -->
        <!-- On desktop this is a grid child. On mobile it becomes fixed off-canvas. -->
        <aside :class="[
            'sidebar',
            { 'sidebar-collapsed': isCollapsed, 'mobile-open': mobileOpen }
        ]" aria-hidden="false">
            <Sidebar />
        </aside>

        <!-- Mobile overlay (fades in) -->
        <transition name="fade">
            <div v-if="mobileOpen" class="overlay" @click="closeMobile" aria-hidden="true" />
        </transition>

        <!-- Main Content -->
        <main class="main-content">
            <slot />
        </main>
    </div>
</template>

<style scoped>
/* ---------- Layout (desktop grid) ---------- */
.layout {
    --sidebar-width: 250px;
    min-height: 100vh;
    display: grid;
    grid-template-columns: var(--sidebar-width) 1fr;
    grid-template-rows: 60px 1fr;
    grid-template-areas:
        "sidebar nav"
        "sidebar main";
    transition: grid-template-columns 0.28s ease;
    box-sizing: border-box;
}

/* When collapsed on desktop, first column becomes 0 */
.layout.layout-collapsed {
    grid-template-columns: 0 1fr;
}

/* ---------- Header ---------- */
.nav {
    grid-area: nav;
    border-bottom: 1px solid #dadada;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    z-index: 20;
}

/* ---------- Sidebar (desktop & mobile base) ---------- */
.sidebar {
    grid-area: sidebar;
    background: #111827;
    /* dark background */
    color: #eee;
    padding: 12px;
    width: var(--sidebar-width);
    box-sizing: border-box;
    overflow: hidden;
    transition: width 0.28s ease, transform 0.28s ease, padding 0.28s ease;
}

/* If layout is collapsed on desktop, keep sidebar width 0 (grid reserves 0) */
.sidebar.sidebar-collapsed {
    width: 0;
    padding: 0;
    overflow: hidden;
}

/* ---------- Main ---------- */
.main-content {
    grid-area: main;
    padding: 12px;
    background: #f8fafc;
    min-height: calc(100vh - 60px);
    overflow: hidden;
}

/* ---------- Mobile behavior (off-canvas) ---------- */
@media (max-width: 763px) {

    /* Switch to single-column grid that only includes nav + main */
    .layout {
        grid-template-columns: 1fr;
        grid-template-areas:
            "nav"
            "main";
    }

    /* Sidebar becomes a fixed drawer off-canvas */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        transform: translateX(-100%);
        width: 250px;
        /* fixed drawer width */
        padding: 12px;
        z-index: 1100;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.45);
        /* make sure it appears above nav when opened (nav has z-index 20) */
    }

    /* When mobileOpen is true, we slide it in */
    .sidebar.mobile-open {
        transform: translateX(0);
    }

    /* overlay behind drawer */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1050;
    }

    /* simple fade transition */
    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.25s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
    }

    .fade-enter-to,
    .fade-leave-from {
        opacity: 1;
    }
}
</style>
