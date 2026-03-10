# Data Model (WordPress DB Mapping)

## 1) المشاريع (CPT: project)
- التخزين الأساسي: `wp_posts`
  - `post_type = project`
  - `post_title`: اسم المشروع
  - `post_content`: الوصف التفصيلي
  - `post_excerpt`: الوصف المختصر (اختياري)
- الصورة الرئيسية: `wp_postmeta` عبر `_thumbnail_id`

## 2) حقول المشروع (Post Meta)
كلها في `wp_postmeta`:
- `_pp_gallery_ids`
  - النوع: string فيه IDs مفصولة بفواصل (مثال: `12,15,19`)
  - الاستخدام: سلايدر صور المشروع
- `_pp_location_label`
  - النوع: نص
  - الاستخدام: عنوان/اسم مكان اللوكيشن
- `_pp_location_lat`
  - النوع: نص (latitude)
- `_pp_location_lng`
  - النوع: نص (longitude)
- `_pp_location_display`
  - القيم: `link` أو `map` أو فارغ (يرجع للديفولت)

## 3) أقسام المشاريع (Taxonomy: project_category)
- التخزين: `wp_terms` + `wp_term_taxonomy` + `wp_term_relationships`
- صورة القسم: `wp_termmeta`
  - key: `pp_term_image_id`
  - value: attachment ID

## 4) إعدادات الإضافة (Options)
- الجدول: `wp_options`
- option key: `pp_settings`
- شكل القيمة (array):
  - `location_display_default`: `link` | `map`
  - `google_maps_api_key`: نص
  - `projects_per_page`: رقم
  - `pagination_per_page`: رقم
  - `enabled_views`: array (grid/masonry/slider/list)

## 5) REST API
- Endpoint: `/wp-json/projects-plugin/v1/projects`
- Params:
  - `page`
  - `per_page`
  - `category` (slug أو term id)
  - `latest`
- Response:
  - `items[]`: (id/title/link/excerpt/image/category[])
  - `total`
  - `max_pages`
  - `page`
