# Redesign Portal Publik — Google Research Minimal

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign Portal Publik AvaraDesa mengacu Google Research Minimal design reference (`resources/js/research.google-design.md`).

**Architecture:** Token-first — update `app.css` design tokens dulu, lalu komponen UI, lalu layout, lalu halaman. Setiap task bersifat independen dan testable.

**Tech Stack:** Vue 3 + Inertia.js + Tailwind CSS v4 + Instrument Sans + Lucide icons

## Global Constraints

- Font: Instrument Sans (existing, jangan ganti)
- Warna: Primary #121317, Secondary #2F3034, Tertiary #5F6368, Surface #FFFFFF, Border #2122260F
- Spacing: 8px grid — xs(8) sm(16) md(24) lg(40) xl(80)
- Radius: sm(4) md(8) lg(16) xl(24) full(9999)
- Shadow: minimal, gunakan border untuk hierarki
- Semua bg gradient/gelap di PUBLIC pages diganti putih
- Component buttons: primary = black pill, secondary = transparent + border, tertiary = text-only
- Jangan ubah CitizenLayout atau halaman warga

---

### Task 1: Design Tokens — app.css

**Files:**
- Modify: `resources/css/app.css`

**Interfaces:**
- Consumes: `research.google-design.md` sebagai referensi
- Produces: Tailwind `@theme` tokens baru + utility classes untuk komponen dan layout

- [ ] **Step 1: Update font dan warna tokens**

```css
@import url('https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap');
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../js/**/*.vue';

@theme {
  --font-sans: 'Instrument Sans', 'Inter', system-ui, sans-serif;

  /* Design System — Google Research Minimal */
  --color-primary: #121317;
  --color-secondary: #2F3034;
  --color-tertiary: #5F6368;
  --color-surface: #FFFFFF;
  --color-on-surface: #121317;
  --color-muted: #E5E7EB;
  --color-border: #2122260F;
  --color-link: #202124;
  --color-inverse: #FFFFFF;
  --color-success: #1E8E3E;
  --color-warning: #FBBC04;
  --color-error: #D93025;

  /* Spacing — 8px grid */
  --spacing-xs: 8px;
  --spacing-sm: 16px;
  --spacing-md: 24px;
  --spacing-lg: 40px;
  --spacing-xl: 80px;

  /* Border Radius */
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 16px;
  --radius-xl: 24px;
  --radius-full: 9999px;

  /* Font Sizes — Editorial Scale */
  --text-headline-display: 72px;
  --text-headline-display--line-height: 0.95;
  --text-headline-display--font-weight: 450;
  --text-headline-display--letter-spacing: -0.02em;

  --text-headline-lg: 32px;
  --text-headline-lg--line-height: 38px;
  --text-headline-lg--font-weight: 700;
  --text-headline-lg--letter-spacing: 0px;

  --text-headline-md: 28px;
  --text-headline-md--line-height: 34px;
  --text-headline-md--font-weight: 450;
  --text-headline-md--letter-spacing: 0px;

  --text-headline-sm: 24px;
  --text-headline-sm--line-height: 29.7px;
  --text-headline-sm--font-weight: 450;
  --text-headline-sm--letter-spacing: 0px;

  --text-headline-xs: 20px;
  --text-headline-xs--line-height: 24px;
  --text-headline-xs--font-weight: 450;
  --text-headline-xs--letter-spacing: 0px;

  --text-body-lg: 18px;
  --text-body-lg--line-height: 25.4px;
  --text-body-lg--font-weight: 400;

  --text-body-md: 16px;
  --text-body-md--line-height: 24px;
  --text-body-md--font-weight: 400;

  --text-body-sm: 14px;
  --text-body-sm--line-height: 20px;
  --text-body-sm--font-weight: 400;

  --text-label-lg: 17.5px;
  --text-label-lg--line-height: 24px;
  --text-label-lg--font-weight: 450;

  --text-label-md: 16px;
  --text-label-md--line-height: 22px;
  --text-label-md--font-weight: 400;

  --text-label-sm: 12px;
  --text-label-sm--line-height: 16px;
  --text-label-sm--font-weight: 500;
  --text-label-sm--letter-spacing: 0.04em;

  /* Shadows — minimal */
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.03);
  --shadow-md: 0 2px 8px 0 rgb(0 0 0 / 0.04);
  --shadow-lg: 0 4px 16px 0 rgb(0 0 0 / 0.06);

  --z-dropdown: 1000;
  --z-sticky: 1020;
  --z-fixed: 1030;
  --z-modal-backdrop: 1040;
  --z-modal: 1050;
  --z-popover: 1060;
  --z-tooltip: 1070;
  --z-toast: 9999;
}
```

- [ ] **Step 2: Update base styles — hapus teal/amber, pakai token baru**

```css
html {
    scroll-behavior: smooth;
    overflow-x: hidden;
    width: 100%;
}

body {
    @apply bg-surface text-primary antialiased;
    overflow-x: hidden;
    width: 100%;
}

button, a, input, select, textarea {
    @apply focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2;
}
```

- [ ] **Step 3: Update badge utility classes**

```css
.badge-pending { @apply bg-muted text-secondary; }
.badge-diproses { @apply bg-muted text-primary; }
.badge-selesai { @apply text-success; }
.badge-ditolak { @apply text-error; }
.badge-disetujui { @apply text-success; }
```

- [ ] **Step 4: Update prose styling**

```css
.prose h2 { @apply mt-lg mb-md text-headline-sm text-primary; }
.prose h3 { @apply mt-md mb-sm text-headline-xs text-primary; }
.prose p { @apply mb-md text-body-md text-secondary; }
.prose ul, .prose ol { @apply mb-md pl-md; }
.prose li { @apply mb-xs text-body-md text-secondary; }
.prose a { @apply text-link underline; }
.prose img { @apply my-md rounded-md; }
```

- [ ] **Step 5: Hapus/arsipkan CSS lama yang sudah tidak dipakai**

Hapus semua `@theme` lama (teal/amber colors, old shadows, old fonts). Hapus utility classes yang redundant dengan Tailwind tokens (`.text-primary`, `.bg-primary`, `.btn-primary`, `.btn-secondary`, `.editorial-card`, `.headline-lg`, `.headline-md`, `body-lg`, `.label-lg`, `.avatar-circle-sm`, `.avatar-circle-lg`, `.icon-wrapper`, `.bg-primary-soft`, `.bg-success-soft`, `.bg-warning-soft`, `.bg-accent-soft`, `.input-wrapper`, `.alert-box`, `.warning-alert`, `.error-alert`, `.category-label`, `.google-editorial`, `.data-item`).

Pertahankan `.badge-*`, `.prose`, `.animate-progress`, `.animate-fade-in`, `.back-link`.

- [ ] **Step 6: Commit**

```bash
git add resources/css/app.css
git commit -m "feat(design): implement Google Research Minimal design tokens in app.css"
```

Run: `npm run lint` (should pass — CSS tidak di-lint ESLint)

---

### Task 2: Component Updates — AppButton, AppCard, FormInput, FormSelect, StatusBadge, Pagination

**Files:**
- Modify: `resources/js/Components/AppButton.vue`
- Modify: `resources/js/Components/AppCard.vue`
- Modify: `resources/js/Components/FormInput.vue`
- Modify: `resources/js/Components/FormSelect.vue`
- Modify: `resources/js/Components/StatusBadge.vue`
- Modify: `resources/js/Components/Pagination.vue`

**Interfaces:**
- Consumes: Design tokens dari Task 1
- Produces: Komponen reusable yang konsisten dengan Google Research Minimal

- [ ] **Step 1: Update AppButton.vue — variants sesuai token**

```vue
<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    loading: Boolean,
});

const base = 'inline-flex items-center justify-center gap-2 px-md py-xs text-label-lg font-semibold transition-all duration-200 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 rounded-full';
const variants = {
    primary: 'bg-primary text-inverse hover:opacity-85',
    secondary: 'bg-transparent text-secondary border border-border hover:border-primary/20 hover:text-primary',
    tertiary: 'bg-transparent text-link hover:opacity-60 rounded-none px-0',
    danger: 'bg-error text-inverse hover:opacity-85',
};
</script>

<template>
    <Link v-if="href" :href="href" :class="[base, variants[variant]]">
        <span v-if="loading" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />
        <slot />
    </Link>
    <button v-else :type="type" :disabled="loading" :class="[base, variants[variant]]">
        <span v-if="loading" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" />
        <slot />
    </button>
</template>
```

- [ ] **Step 2: Update AppCard.vue — flat, border only**

```vue
<template>
    <section class="bg-surface border border-border rounded-md p-md">
        <slot />
    </section>
</template>
```

- [ ] **Step 3: Update FormInput.vue — pill shape, hairline border**

```vue
<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    error: String,
    placeholder: String,
    type: { type: String, default: 'text' },
    disabled: Boolean,
    required: Boolean,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="input-wrapper space-y-xs">
        <label v-if="label" class="block text-label-sm text-secondary font-medium" :class="{ 'text-error': error }">
            {{ label }}
            <span v-if="required" class="text-error ml-0.5">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            class="w-full h-12 px-sm text-body-md text-primary bg-surface border border-border rounded-full placeholder:text-tertiary/60 transition-colors duration-200 hover:border-primary/30 focus:border-primary focus:ring-1 focus:ring-primary/20 disabled:opacity-40 disabled:cursor-not-allowed"
            :class="{ 'border-error focus:border-error focus:ring-error/20': error }"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="text-label-sm text-error mt-1">{{ error }}</p>
    </div>
</template>

<style scoped>
.input-wrapper input:focus {
    outline: none;
}
</style>
```

- [ ] **Step 4: Update FormSelect.vue**

```vue
<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    error: String,
    options: { type: Array, default: () => [] },
    placeholder: String,
    disabled: Boolean,
    required: Boolean,
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-xs">
        <label v-if="label" class="block text-label-sm text-secondary font-medium" :class="{ 'text-error': error }">
            {{ label }}
            <span v-if="required" class="text-error ml-0.5">*</span>
        </label>
        <select
            :value="modelValue"
            :disabled="disabled"
            :required="required"
            class="w-full h-12 px-sm text-body-md text-primary bg-surface border border-border rounded-full transition-colors duration-200 hover:border-primary/30 focus:border-primary focus:ring-1 focus:ring-primary/20 disabled:opacity-40 disabled:cursor-not-allowed appearance-none"
            :class="{ 'border-error focus:border-error focus:ring-error/20': error }"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
            <option v-for="opt in options" :key="opt.value || opt" :value="opt.value || opt">
                {{ opt.label || opt }}
            </option>
        </select>
        <p v-if="error" class="text-label-sm text-error mt-1">{{ error }}</p>
    </div>
</template>
```

- [ ] **Step 5: Update StatusBadge.vue**

```vue
<script setup>
defineProps({
    status: { type: String, required: true },
    label: String,
});
</script>

<template>
    <span
        class="inline-flex items-center px-2.5 py-1 rounded-full text-label-sm font-semibold"
        :class="{
            'text-tertiary bg-muted': 'pending' === status || 'diproses' === status,
            'text-success': 'selesai' === status || 'disetujui' === status,
            'text-error': 'ditolak' === status || 'rejected' === status,
            'text-warning': 'menunggu' === status,
        }"
    >
        {{ label || status }}
    </span>
</template>
```

- [ ] **Step 6: Update Pagination.vue — minimal text links**

```vue
<template>
    <nav v-if="links?.length > 3" class="flex items-center justify-center gap-1 py-sm">
        <template v-for="(link, i) in links" :key="i">
            <span v-if="link.url === null" class="px-2 py-1 text-label-sm text-tertiary cursor-not-allowed" v-html="link.label" />
            <Link
                v-else
                :href="link.url"
                class="px-3 py-1.5 text-label-sm rounded-full transition-colors duration-150"
                :class="link.active ? 'bg-primary text-inverse font-semibold' : 'text-secondary hover:bg-muted'"
                v-html="link.label"
            />
        </template>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ links: Array });
</script>
```

- [ ] **Step 7: Commit**

```bash
git add resources/js/Components/AppButton.vue resources/js/Components/AppCard.vue resources/js/Components/FormInput.vue resources/js/Components/FormSelect.vue resources/js/Components/StatusBadge.vue resources/js/Components/Pagination.vue
git commit -m "feat(components): update UI components to Google Research Minimal style"
```

Run: `npm run lint`

---

### Task 3: PublicLayout — Header & Footer Restructure

**Files:**
- Modify: `resources/js/Layouts/PublicLayout.vue`

**Interfaces:**
- Consumes: Komponen dari Task 2, design tokens dari Task 1
- Produces: Layout publik dengan header single-bar + footer putih

- [ ] **Step 1: Tulis ulang PublicLayout.vue**

Header baru:
- Single baris, sticky, bg-white, border-b border-border
- Logo minimal 40px (rounded-full + hairline border) + AvaraDesa teks
- Nav links: text-label-md, text-secondary, rounded-full hover:bg-muted
- Active link: text-primary bg-muted
- Mobile menu: white dropdown, clean
- CTA: AppButton variant=primary
- Top-bar info (map, clock, phone) dihapus — pindah ke footer

Footer baru:
- Surface putih, border-t border-border
- 3 kolom grid:
  1. Logo + deskripsi + alamat (text-body-sm text-tertiary)
  2. Menu (link list, text-body-sm text-secondary hover:text-primary)
  3. Kontak & jam kerja (text-body-sm text-tertiary)
- Bottom bar: copyright + AvaraDesa

- [ ] **Step 2: Commit**

```bash
git add resources/js/Layouts/PublicLayout.vue
git commit -m "feat(layout): restructure PublicLayout to Google editorial header + footer"
```

Run: `npm run lint`

---

### Task 4: Home Page Redesign

**Files:**
- Modify: `resources/js/Pages/Public/Home.vue`

**Interfaces:**
- Consumes: PublicLayout baru (Task 3), komponen baru (Task 2), design tokens (Task 1)

Perubahan tiap section:

1. **Hero:** Ganti dark min-h-screen jadi section putih. Headline display 72px, subhead text-body-lg tertiary, CTA black pill + text link. Ilustrasi/foto desa di rounded-lg card. Hapus gradient, blur, grid pattern.

2. **Feature Cards:** 4 card flat — bg-surface, border-border, rounded-md. Icon circle hitam (border, size-11). Title headline-xs, desc text-body-sm tertiary.

3. **Statistik:** 2-column section. Label "Data Kependudukan" sebagai overline kecil. Angka headline-display weight 450. Cards flat, no shadow.

4. **Layanan:** Grid 2-col cards. Kode surat sebagai label kecil (text-label-sm text-tertiary). Nama surat sebagai title. Flat, border-border.

5. **Alur 4 Langkah:** 4 flat boxes, nomor headline-sm weight 450, title headline-xs.

6. **KBN Section:** Flat cards, border-border, without colorful badges — pakai teks biasa.

7. **Aparatur:** Image cards rounded-md, flat. Name headline-xs.

8. **Peta:** Sama seperti sekarang, styling border-border.

9. **Berita:** Image rounded-md, card flat. Title headline-xs, body-sm tertiary.

10. **Aspirasi Section:** Ganti dari dark slate box ke section putih flat dengan border. Atau simpan saja sebagai section sederhana dengan hairline border. Hapus absolute blur circles.

- [ ] **Step 1: Tulis ulang Home.vue — hero putih, flat cards, typography-driven**

- [ ] **Step 2: Commit**

```bash
git add resources/js/Pages/Public/Home.vue
git commit -m "feat(home): redesign Home page per Google Research Minimal spec"
```

Run: `npm run lint`

---

### Task 5: Interior Pages — Profil, Informasi, Fasilitas, Statistik, Verifikasi

**Files:**
- Modify: `resources/js/Pages/Public/Profile.vue`
- Modify: `resources/js/Pages/Public/Information/Index.vue`
- Modify: `resources/js/Pages/Public/Information/Show.vue`
- Modify: `resources/js/Pages/Public/Fasilitas.vue`
- Modify: `resources/js/Pages/Public/Statistik.vue`
- Modify: `resources/js/Pages/Public/Verify.vue`

**Interfaces:**
- Consumes: Design tokens (Task 1), komponen (Task 2), layout (Task 3)

Prinsip: flat cards, border-border, typography hierarchy, no gradient/shadow.

- [ ] **Step 1: Update halaman Profile.vue**

Ganti colored header/image ke putih. Cards flat. Typography pakai headline-xs/body-md/body-sm.

- [ ] **Step 2: Update Information/Index.vue**

Artikel grid: card putih, hairline border, image rounded-md, title headline-xs, date text-body-sm tertiary. No shadow.

- [ ] **Step 3: Update Information/Show.vue**

Article body pake prose yang sudah di-tokenize. Back link tertiary style. Breadcrumb minimal.

- [ ] **Step 4: Update Fasilitas.vue**

Grid fasilitas dengan card flat, icon hitam, border-border.

- [ ] **Step 5: Update Statistik.vue**

Data cards flat, chart palette disesuaikan dengan hitam/putih. Hapus gradient chart colors.

- [ ] **Step 6: Update Verify.vue**

Form input pill putih, border hairline. Result card flat. Button black pill.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Pages/Public/Profile.vue resources/js/Pages/Public/Information/Index.vue resources/js/Pages/Public/Information/Show.vue resources/js/Pages/Public/Fasilitas.vue resources/js/Pages/Public/Statistik.vue resources/js/Pages/Public/Verify.vue
git commit -m "feat(pages): restyle all interior pages to Google Research Minimal"
```

Run: `npm run lint`

---

### Task 6: Final Verification

- [ ] **Step 1: Run linter**

```bash
npm run lint
```

- [ ] **Step 2: Run unit tests**

```bash
npm run test:unit
```

- [ ] **Step 3: Run build**

```bash
npm run build
```

- [ ] **Step 4: Final commit jika ada perbaikan**

```bash
git add -A
git commit -m "chore: final polish after redesign verification"
```
