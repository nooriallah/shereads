# =====================================================================
# SHEREADS cleanup — moves unused files into _unused_backup\
# (nothing is deleted; test the app, then remove _unused_backup manually)
#
# Run from the project root:
#   powershell -ExecutionPolicy Bypass -File cleanup.ps1
# =====================================================================

$root   = $PSScriptRoot
$backup = Join-Path $root "_unused_backup"

$paths = @(
    # ---- Dead Laravel/Jetstream views (nothing references them) ----
    "resources\views\welcome.blade.php",           # Laravel default landing (route / uses Livewire Start)
    "resources\views\dashboard.blade.php",         # Jetstream default (route uses Backend\Dashboard)
    "resources\views\navigation-menu.blade.php",   # Jetstream default menu (layouts.app has its own)
    "resources\views\policy.blade.php",            # terms/privacy feature disabled
    "resources\views\terms.blade.php",
    "resources\views\api",                         # API tokens feature disabled
    "resources\views\emails",                      # teams feature disabled
    "resources\views\components\welcome.blade.php",        # only used by dead dashboard.blade.php
    "resources\views\components\switchable-team.blade.php",# teams feature disabled
    "resources\views\vendor\livewire",             # published pagination views; Laravel falls back to the package's own
    "resources\views\livewire\backend\loading.blade.php",  # unreferenced

    # ---- Dead PHP classes ----
    "app\Models\Authors.php",                      # duplicate of Author (unused)
    "app\Models\CategoryModel.php",                # duplicate of Category (unused)
    "app\Enums\UserRules.php",                     # unused (UserRole is the real enum)

    # ---- Template leftovers in public/ ----
    "public\test.html",
    "public\backend\index.html",                   # theme demo page
    "public\backend\js\demo.js",
    "public\backend\js\styleSwitcher.js",
    "public\backend\js\dashboard",                 # theme demo dashboard scripts
    "public\backend\js\plugins-init",              # init scripts for plugins we don't load

    # ---- Icon sets no longer imported by style.css (kept: font-awesome, themify) ----
    "public\backend\icons\avasta",
    "public\backend\icons\bootstrap-icons",
    "public\backend\icons\flaticon",
    "public\backend\icons\flaticon_1",
    "public\backend\icons\icomoon",
    "public\backend\icons\line-awesome",
    "public\backend\icons\material-design-iconic-font",
    "public\backend\icons\simple-line-icons",

    # ---- Vendor libraries never loaded by any page ----
    # (kept: global, jquery-nice-select, metismenu, perfect-scrollbar, animate, aos)
    "public\backend\vendor\apexchart",
    "public\backend\vendor\bootstrap-daterangepicker",
    "public\backend\vendor\bootstrap-material-datetimepicker",
    "public\backend\vendor\bootstrap-select",
    "public\backend\vendor\chart.js",
    "public\backend\vendor\chartist",
    "public\backend\vendor\chartist-plugin-tooltips",
    "public\backend\vendor\ckeditor",
    "public\backend\vendor\clockpicker",
    "public\backend\vendor\datatables",
    "public\backend\vendor\draggable",
    "public\backend\vendor\dropzone",
    "public\backend\vendor\flot",
    "public\backend\vendor\flot-spline",
    "public\backend\vendor\fullcalendar",
    "public\backend\vendor\highlightjs",
    "public\backend\vendor\jquery-asColor",
    "public\backend\vendor\jquery-asColorPicker",
    "public\backend\vendor\jquery-asGradient",
    "public\backend\vendor\jquery-smartwizard",
    "public\backend\vendor\jquery-sparkline",
    "public\backend\vendor\jquery-steps",
    "public\backend\vendor\jquery-validation",
    "public\backend\vendor\jqvmap",
    "public\backend\vendor\lightgallery",
    "public\backend\vendor\moment",
    "public\backend\vendor\morris",
    "public\backend\vendor\nestable2",
    "public\backend\vendor\nouislider",
    "public\backend\vendor\owl-carousel",
    "public\backend\vendor\peity",
    "public\backend\vendor\pickadate",
    "public\backend\vendor\raphael",
    "public\backend\vendor\select2",
    "public\backend\vendor\star-rating",
    "public\backend\vendor\svganimation",
    "public\backend\vendor\sweetalert2",
    "public\backend\vendor\toastr",
    "public\backend\vendor\wnumb",

    # ---- Static HTML mockups replaced by Livewire pages ----
    "public\frontend\analys.html",
    "public\frontend\collection.html",
    "public\frontend\index.html",
    "public\frontend\login.html",
    "public\frontend\que_1.html",
    "public\frontend\que_2.html",
    "public\frontend\result_prev.html",
    "public\frontend\signup.html",
    "public\frontend\start_now.html",

    # ---- Unreferenced frontend images ----
    "public\frontend\assets\images\fly_book.png",
    "public\frontend\assets\images\icon_green.png",
    "public\frontend\assets\images\logo_white.png"
)

$moved = 0
$skipped = 0

foreach ($p in $paths) {
    $src = Join-Path $root $p
    if (Test-Path $src) {
        $dest = Join-Path $backup $p
        $destDir = Split-Path $dest -Parent
        New-Item -ItemType Directory -Force -Path $destDir | Out-Null
        Move-Item -Force -Path $src -Destination $dest
        Write-Host "moved   : $p"
        $moved++
    }
    else {
        Write-Host "skipped : $p (not found)"
        $skipped++
    }
}

Write-Host ""
Write-Host "Done. Moved $moved item(s), skipped $skipped."
Write-Host "Backup folder: $backup"
Write-Host ""
Write-Host "Now run:  php artisan view:clear"
Write-Host "Then test the site (admin + questionnaire flow + profile)."
Write-Host "When everything works, delete _unused_backup\ to reclaim the space."
