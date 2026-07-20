# Design System: Tokens and Components

This document extracts the foundational design tokens and visual guidelines of the Mitra Privacy Tool, inferred directly from `tailwind.config.js` and the Vue Components library.

## 1. Design Tokens

### 1.1 Typography
- **Primary Font Family:** `Inter` (sans-serif)
- Falls back to the default Tailwind sans-serif stack. 
- "Inter" implies a clean, modern, highly legible geometric sans-serif aesthetic, suitable for data-dense dashboards.

### 1.2 Color Palette
The application extends the default Tailwind palette with two custom scales:

**Brand Colors (Primary)**
Used for primary actions, active states, and core branding.
- `brand-50` to `brand-950`.
- **Primary Focus (`brand-500`):** `#5c73fb` (A vibrant, modern blue-indigo).

**Surface Colors (Neutrals)**
Used for backgrounds, borders, structural elements, and typography.
- `surface-50` to `surface-950`.
- Ranges from a very light slate/gray `#f8fafc` to a deep slate `#020617`.

### 1.3 Shadows & Elevation (Tactile Design)
The system implements custom shadows to provide a "tactile" or slightly elevated, physical feel to cards and buttons.
- `shadow-tactile`: Default elevation (`0 2px 8px -2px...`).
- `shadow-tactile-hover`: Higher elevation for hovered interactive elements (`0 10px 25px -5px...`).
- `shadow-tactile-active`: Pressed state (`0 1px 2px 0...`).

### 1.4 Animation & Transitions
Custom timing functions indicate a focus on micro-interactions.
- `ease-smooth`: `cubic-bezier(0.4, 0, 0.2, 1)` for general UI transitions.
- `ease-bounce`: `cubic-bezier(0.34, 1.56, 0.64, 1)` for playful/springy interactions (e.g., modals popping in or success checks).

## 2. Core Components

Based on `resources/js/Components`, the design system relies on these visual blocks:

### Inputs & Forms
- Relies on `@tailwindcss/forms` plugin to normalize input fields.
- Custom `TextInput.vue`, `Checkbox.vue`, and `Dropdown.vue` wrap the native elements to apply consistent `brand` focus rings and `surface` borders.

### Cards & Panels
- **`Card.vue`**: The primary structural component for wrapping dashboard content, likely using `shadow-tactile`.
- **`QuestionCard.vue`**: A specialized layout to handle long text, options, and notes cleanly.

### Badges
- Used heavily for status indication.
- **`ConsensusBadge.vue`**, **`DivergenceBadge.vue`**, **`ProvenanceBadge.vue`**.

---
**Confidence Level:** ★★★★★ (Directly extracted from `tailwind.config.js`).
