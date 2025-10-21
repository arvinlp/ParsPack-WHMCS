<?php

/**
 * Parspack Module - Persian Language File
 *
 * مسیر: /modules/servers/parspack/lang/persian.php
 */

$_LANG['parspack'] = [
    // Module Info
    'module_name' => 'سرور مجازی پارس‌پک',
    'module_description' => 'ماژول مدیریت و فروش سرور مجازی پارس‌پک',

    // Configuration Options
    'config_plan_id' => 'شناسه پلن',
    'config_plan_id_description' => 'شناسه پلن از پنل پارس‌پک (مثال: c2-m2-d40)',
    'config_os_id' => 'شناسه سیستم عامل',
    'config_os_id_description' => 'شناسه سیستم عامل پیش‌فرض (مثال: ubuntu-22.04)',
    'config_region' => 'منطقه دیتاسنتر',
    'config_region_description' => 'انتخاب منطقه دیتاسنتر',
    'config_backup' => 'پشتیبان‌گیری خودکار',
    'config_backup_description' => 'فعال‌سازی پشتیبان‌گیری خودکار',

    // Server Status
    'status_running' => 'در حال اجرا',
    'status_stopped' => 'متوقف شده',
    'status_building' => 'در حال ساخت',
    'status_error' => 'خطا',
    'status_rebooting' => 'در حال راه‌اندازی مجدد',
    'status_shutting_down' => 'در حال خاموش شدن',
    'status_resizing' => 'در حال تغییر اندازه',
    'status_rebuilding' => 'در حال بازسازی',
    'status_unknown' => 'نامشخص',

    // Actions
    'action_poweron' => 'روشن کردن',
    'action_shutdown' => 'خاموش کردن',
    'action_reboot' => 'راه‌اندازی مجدد',
    'action_reset_password' => 'بازیابی رمز عبور',
    'action_get_info' => 'دریافت اطلاعات',
    'action_create_snapshot' => 'ایجاد اسنپ‌شات',
    'action_create_backup' => 'ایجاد پشتیبان',
    'action_resize' => 'تغییر اندازه',
    'action_rebuild' => 'بازسازی',

    // Success Messages
    'success_created' => 'سرور با موفقیت ایجاد شد',
    'success_deleted' => 'سرور با موفقیت حذف شد',
    'success_poweron' => 'سرور با موفقیت روشن شد',
    'success_shutdown' => 'سرور با موفقیت خاموش شد',
    'success_reboot' => 'سرور در حال راه‌اندازی مجدد است',
    'success_suspended' => 'سرور با موفقیت تعلیق شد',
    'success_unsuspended' => 'تعلیق سرور با موفقیت لغو شد',
    'success_password_reset' => 'رمز عبور با موفقیت بازنشانی شد',
    'success_snapshot_created' => 'اسنپ‌شات با موفقیت ایجاد شد',
    'success_backup_created' => 'پشتیبان با موفقیت ایجاد شد',
    'success_resized' => 'تغییر اندازه سرور با موفقیت انجام شد',
    'success_rebuilt' => 'بازسازی سرور با موفقیت انجام شد',

    // Error Messages
    'error_connection' => 'خطا در اتصال به API پارس‌پک',
    'error_invalid_token' => 'توکن API نامعتبر است',
    'error_server_not_found' => 'سرور مورد نظر یافت نشد',
    'error_insufficient_balance' => 'موجودی حساب کافی نیست',
    'error_invalid_plan' => 'پلن انتخابی نامعتبر است',
    'error_invalid_os' => 'سیستم‌عامل انتخابی نامعتبر است',
    'error_region_unavailable' => 'منطقه انتخابی در دسترس نیست',
    'error_build_timeout' => 'زمان ساخت سرور به پایان رسید',
    'error_operation_failed' => 'عملیات با خطا مواجه شد',
    'error_missing_parameter' => 'پارامتر الزامی وارد نشده است',
    'error_api_error' => 'خطای API: {error}',
    'error_unknown' => 'خطای نامشخص رخ داده است',

    // Client Area
    'client_server_info' => 'اطلاعات سرور',
    'client_server_name' => 'نام سرور',
    'client_server_status' => 'وضعیت',
    'client_server_ip' => 'آدرس IP',
    'client_server_os' => 'سیستم عامل',
    'client_server_cpu' => 'پردازنده',
    'client_server_ram' => 'حافظه RAM',
    'client_server_disk' => 'فضای ذخیره‌سازی',
    'client_server_region' => 'منطقه',
    'client_access_info' => 'اطلاعات دسترسی',
    'client_username' => 'نام کاربری',
    'client_password' => 'رمز عبور',
    'client_ssh_connection' => 'اتصال SSH',
    'client_show_password' => 'نمایش رمز عبور',
    'client_hide_password' => 'مخفی کردن رمز عبور',
    'client_backup_info' => 'پشتیبان‌گیری',
    'client_backup_enabled' => 'پشتیبان‌گیری خودکار فعال است',
    'client_no_backups' => 'هنوز پشتیبانی ایجاد نشده است',
    'client_usage_stats' => 'آمار مصرف',
    'client_cpu_usage' => 'مصرف CPU',
    'client_ram_usage' => 'مصرف RAM',
    'client_disk_usage' => 'مصرف دیسک',
    'client_network_usage' => 'مصرف شبکه',

    // Admin Area
    'admin_server_details' => 'جزئیات سرور',
    'admin_server_id' => 'شناسه سرور',
    'admin_service_id' => 'شناسه سرویس',
    'admin_created_at' => 'تاریخ ایجاد',
    'admin_updated_at' => 'آخرین بروزرسانی',
    'admin_test_connection' => 'تست اتصال',
    'admin_connection_successful' => 'اتصال موفقیت‌آمیز بود',
    'admin_connection_failed' => 'اتصال ناموفق بود',

    // Regions
    'region_ir_thr_ba1' => 'تهران - باقر شهر ۱',
    'region_ir_thr_mn1' => 'تهران - منیریه ۱',

    // Plans
    'plan_starter' => 'استارتر',
    'plan_basic' => 'پایه',
    'plan_advanced' => 'پیشرفته',
    'plan_professional' => 'حرفه‌ای',

    // Units
    'unit_core' => 'هسته',
    'unit_cores' => 'هسته',
    'unit_gb' => 'گیگابایت',
    'unit_mb' => 'مگابایت',
    'unit_tb' => 'ترابایت',

    // Notifications
    'notification_server_ready' => 'سرور شما آماده استفاده است',
    'notification_server_building' => 'سرور در حال ساخت است، لطفاً صبر کنید',
    'notification_server_error' => 'خطا در ساخت سرور رخ داده است',

    // Email Templates
    'email_welcome_subject' => 'سرور مجازی شما آماده است',
    'email_welcome_body' => 'سرور مجازی شما با موفقیت ایجاد شد',
    'email_suspension_subject' => 'سرور شما تعلیق شد',
    'email_suspension_body' => 'سرور مجازی شما به دلیل عدم پرداخت تعلیق شده است',
    'email_termination_subject' => 'سرور شما حذف شد',
    'email_termination_body' => 'سرور مجازی شما حذف شده است',

    // Validation
    'validation_required' => 'فیلد {field} الزامی است',
    'validation_invalid' => 'مقدار {field} نامعتبر است',
    'validation_min_length' => '{field} باید حداقل {min} کاراکتر باشد',
    'validation_max_length' => '{field} باید حداکثر {max} کاراکتر باشد',

    // Misc
    'loading' => 'در حال بارگذاری...',
    'please_wait' => 'لطفاً صبر کنید',
    'confirm_action' => 'آیا از انجام این عملیات اطمینان دارید؟',
    'yes' => 'بله',
    'no' => 'خیر',
    'save' => 'ذخیره',
    'cancel' => 'انصراف',
    'delete' => 'حذف',
    'edit' => 'ویرایش',
    'view' => 'مشاهده',
    'back' => 'بازگشت',
    'next' => 'بعدی',
    'previous' => 'قبلی',
    'search' => 'جستجو',
    'filter' => 'فیلتر',
    'export' => 'خروجی',
    'import' => 'ورودی',
    'refresh' => 'بروزرسانی',
    'close' => 'بستن',

    // Features
    'feature_auto_backup' => 'پشتیبان‌گیری خودکار',
    'feature_monitoring' => 'مانیتورینگ',
    'feature_firewall' => 'فایروال',
    'feature_snapshots' => 'اسنپ‌شات',
    'feature_floating_ip' => 'IP شناور',
    'feature_private_network' => 'شبکه خصوصی',

    // Security
    'security_change_password' => 'تغییر رمز عبور',
    'security_ssh_key' => 'کلید SSH',
    'security_firewall_rules' => 'قوانین فایروال',
    'security_two_factor' => 'احراز هویت دو مرحله‌ای',

    // Backup & Snapshots
    'backup_list' => 'لیست پشتیبان‌ها',
    'backup_create' => 'ایجاد پشتیبان',
    'backup_restore' => 'بازگردانی از پشتیبان',
    'backup_delete' => 'حذف پشتیبان',
    'backup_date' => 'تاریخ پشتیبان',
    'backup_size' => 'حجم پشتیبان',
    'snapshot_list' => 'لیست اسنپ‌شات‌ها',
    'snapshot_create' => 'ایجاد اسنپ‌شات',
    'snapshot_restore' => 'بازگردانی از اسنپ‌شات',
    'snapshot_delete' => 'حذف اسنپ‌شات',

    // Console
    'console_title' => 'کنسول سرور',
    'console_connect' => 'اتصال به کنسول',
    'console_disconnect' => 'قطع اتصال',

    // Monitoring
    'monitoring_cpu' => 'مانیتورینگ CPU',
    'monitoring_ram' => 'مانیتورینگ RAM',
    'monitoring_disk' => 'مانیتورینگ دیسک',
    'monitoring_network' => 'مانیتورینگ شبکه',
    'monitoring_uptime' => 'آپتایم سرور',

    // Logs
    'logs_title' => 'لاگ‌های سرور',
    'logs_action' => 'عملیات',
    'logs_status' => 'وضعیت',
    'logs_date' => 'تاریخ',
    'logs_message' => 'پیام',
    'logs_clear' => 'پاک کردن لاگ‌ها',

    // Help & Support
    'help_documentation' => 'مستندات',
    'help_support' => 'پشتیبانی',
    'help_faq' => 'سوالات متداول',
    'help_contact' => 'تماس با ما',

    // Warnings
    'warning_delete_server' => 'هشدار: حذف سرور غیرقابل بازگشت است!',
    'warning_rebuild_server' => 'هشدار: بازسازی سرور تمام داده‌ها را پاک می‌کند!',
    'warning_resize_server' => 'توجه: تغییر اندازه سرور نیاز به راه‌اندازی مجدد دارد',
    'warning_low_balance' => 'موجودی حساب شما کم است',

    // Tips
    'tip_backup_before_update' => 'نکته: قبل از بروزرسانی حتماً پشتیبان بگیرید',
    'tip_use_ssh_key' => 'نکته: استفاده از کلید SSH امن‌تر است',
    'tip_enable_firewall' => 'نکته: فایروال را برای امنیت بیشتر فعال کنید',
    'tip_regular_backups' => 'نکته: به طور منظم از سرور خود پشتیبان بگیرید',
];

// Return language array
return $_LANG['parspack'];
