# Inertia.js Data Flow

This document details the request lifecycle and data flow paradigm used in the Mitra Privacy Tool, moving from Laravel to Vue.js.

## 1. The Inertia Paradigm
Unlike traditional SPAs (Single Page Applications) that communicate with a backend via a REST or GraphQL API using `fetch` or `axios`, this application uses Inertia.js. Inertia acts as a routing adapter that allows Laravel to render Vue components directly.

## 2. Request Lifecycle

1. **User Action (Frontend):** 
   - A user clicks a link (e.g., `<Link href="/projects/1">`) or submits a form using Inertia's `useForm` helper.
   - Inertia intercepts the standard browser navigation and makes an XHR (Ajax) request instead.
2. **Routing (Backend):**
   - The request hits `routes/web.php`.
   - The request is routed to a standard Laravel Controller (e.g., `ProjectController@show`).
3. **Controller Processing (Backend):**
   - The Controller retrieves data using Eloquent Models.
   - Instead of returning a Blade view (e.g., `return view(...)`), the Controller returns an Inertia response:
     ```php
     return Inertia::render('Project/Dashboard', [
         'project' => $project,
         'members' => $members
     ]);
     ```
4. **Data Serialization:**
   - Laravel serializes the data into JSON.
5. **Component Rendering (Frontend):**
   - Inertia receives the JSON response.
   - It dynamically swaps the current Vue component to `resources/js/Pages/Project/Dashboard.vue`.
   - The JSON data is injected directly into the Vue component as `props`.

## 3. Handling Shared Data
Global data required across multiple pages (like the authenticated user, active project, or current localization strings) is not fetched explicitly on every page.
- It is injected via the `HandleInertiaRequests` middleware in Laravel.
- Vue components can access this globally via `usePage().props.auth.user` or via the `$page` object in templates.

## 4. Form Handling & Validation
- Forms are managed using Inertia's `useForm` composable.
- When a form is submitted to a Laravel controller, standard `FormRequest` validation is applied.
- If validation fails, Laravel automatically redirects back.
- Inertia catches this redirect and injects the validation error messages directly into the Vue component's `form.errors` object, allowing for instant, reactive UI feedback without manual error handling logic.

## 5. Lazy Evaluation
For complex dashboards (like the Results comparison), the application likely uses Inertia's Lazy Evaluation. This allows certain heavy datasets (like a massive historical snapshot) to be omitted on the initial page load, and only fetched when specifically requested by the Vue component.

---
**Confidence Level:** ★★★★★ (Based on the architectural presence of `@inertiajs/vue3` and standard implementation patterns seen in the routing).
