# Project Overview

## اسم المشروع
Projects Plugin for Elementor

## الفكرة
إضافة WordPress لإدارة المشاريع (`project`) وتصنيفها (`project_category`) مع Widgets في Elementor لعرض المشاريع والأقسام بأكثر من Layout.

## أهم الميزات الحالية
- Custom Post Type: `project`
- Taxonomy: `project_category`
- Meta fields للمشروع: gallery, location, location display mode
- Term image لقسم المشروع
- Settings page داخل admin
- 3 Elementor widgets:
  - Projects Categories
  - Projects
  - Latest Projects
- Templates مخصصة:
  - Single Project
  - Archive Project
- فلترة ديناميكية و Load More / Infinite عبر REST API

## ملاحظة تشغيل
الإضافة تعتمد على Elementor للـ widgets. بدون Elementor، CPT/templates شغالين عادي.
