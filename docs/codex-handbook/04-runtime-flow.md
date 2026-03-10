# Runtime Flow

## A) Admin Flow
1. الأدمن ينشئ Project جديد.
2. يضيف gallery + location meta من meta box.
3. يحدد project categories.
4. يضبط إعدادات عامة من صفحة `Projects Plugin`.

## B) Frontend Flow (Archive/Widget)
1. المستخدم يفتح صفحة فيها `Projects Widget`.
2. الويدجت يجيب أول دفعة مشاريع بـ `WP_Query`.
3. عند الضغط على filter:
   - JS ينادي REST endpoint.
   - يحدّث cards داخل نفس الويدجت بدون reload.
4. عند Load More / Infinite:
   - JS يجيب page جديدة ويضيف cards.

## C) Single Project Flow
1. يدخل صفحة المشروع.
2. يظهر Hero (featured image).
3. يظهر gallery slider من `_pp_gallery_ids`.
4. يقدر يفتح Lightbox من زر التكبير داخل السلايدر.
   - يوجد أسهم للتقليب داخل الـLightbox.
   - الضغط على Thumbnail في السلايدر الرئيسي ينقل للصورة المطلوبة.
4. يظهر المحتوى.
5. يظهر اللوكيشن:
   - `link`: زر يفتح Google Maps.
   - `map`: iframe map (مع API key لو موجود).

## D) Elementor Controls (المهم للشغل السريع)
### Projects Widget
- Content:
  - source/category/latest/layout/pagination/columns
  - show/hide (image/title/excerpt/category/button)
  - نصوص مخصصة: `All`, `View Project`, `Load More`
- Style:
  - card colors/border/title/text
  - button colors
  - card/button border radius

### Latest Projects Widget
- Content: count/category/layout/show-hide + `View Project` text
- Style: card/button/title/text colors + border radius

### Categories Widget
- Content: source/layout/columns/show-hide
- Style: card/title/description/border colors + border radius
