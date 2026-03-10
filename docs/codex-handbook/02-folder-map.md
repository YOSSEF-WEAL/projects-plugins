# Folder Map

## الجذر
- `projects-plugin.php`: bootstrap الرئيسي، تعريف constants، تحميل الكلاسات، enqueue، template loader.

## includes/
- `class-post-types.php`: تسجيل `project` + `project_category`.
- `class-meta-boxes.php`: Meta boxes للمشروع + term image لقسم المشروع.
- `class-settings.php`: إعدادات الإضافة داخل لوحة التحكم.
- `class-helpers.php`: دوال مساعدة + REST route.

## elementor/
- `class-elementor.php`: تسجيل widgets في Elementor.
- `widgets/categories-widget.php`: عرض أقسام المشاريع.
- `widgets/projects-widget.php`: عرض المشاريع (filters/pagination/layouts) + text/style controls.
- `widgets/latest-projects-widget.php`: عرض أحدث المشاريع + text/style controls.

## templates/
- `single-project.php`: صفحة المشروع المفرد.
- `archive-project.php`: صفحة الأرشيف.
- `parts/project-card.php`: كارت مشروع reusable.
- `parts/category-card.php`: كارت قسم reusable.
- `parts/slider.php`: سلايدر صور المشروع.
- `parts/location-map.php`: عرض اللوكيشن (Link أو Map).

## public/
- SVG icons المستخدمة في السلايدر والـLightbox (zoom, arrows, close).

## assets/
- `css/frontend.css`: ستايل الواجهة.
- `css/gallery-lightbox.css`: ستايل الـLightbox الخاص بمعرض صور المشروع.
- `css/admin.css`: ستايل لوحة التحكم.
- `js/frontend.js`: فلترة/لود مور/إنفنت عبر REST + تهيئة slider basic.
- `js/gallery-lightbox.js`: موديول Lightbox منفصل لسلايدر المشروع.
- `js/admin.js`: media uploader للصور والجاليري في لوحة التحكم.

## admin/
- `settings-page.php`: HTML صفحة الإعدادات.

## docs/codex-handbook/
مرجع الشغل بيننا.
