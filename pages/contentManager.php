<?php
require_once dirname(__DIR__) . '/config/connection.php';

$bannerDir = "../images/adsBanner/";
$videoDir  = "../images/background/";
$headerDir = "../images/header/";
$categoryIconDir = "../images/categories/";
$contentVersionFile = dirname(__DIR__) . '/cache/kiosk_content_version.txt';

foreach ([$bannerDir, $videoDir, $headerDir, $categoryIconDir] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

$view = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$message = "";

if (isset($_POST['delete_file'])) {
    $fileToDelete = $_POST['delete_file'];
    $realPath = realpath($fileToDelete);
    $allowed = false;
    foreach ([$bannerDir, $videoDir, $headerDir] as $d) {
        if (strpos($realPath, realpath($d)) === 0) {
            $allowed = true;
            break;
        }
    }
    if ($allowed && is_file($realPath)) {
        unlink($realPath);
        bumpKioskContentVersion($contentVersionFile);
        $message = "File purged.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_type'])) {
    $type = $_POST['upload_type'];
    $slot = ($type === 'banners') ? $_POST['banner_slot'] : (($type === 'header') ? "main_header" : "bg");
    $targetDir = ($type === 'banners') ? $bannerDir : (($type === 'header') ? $headerDir : $videoDir);

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileName = buildManagedAssetName($slot, $_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetDir . $fileName)) {
            bumpKioskContentVersion($contentVersionFile);
            $message = "Sync complete.";
        } else {
            $message = "Upload failed.";
        }
    } elseif (!empty($_POST['selected_asset'])) {
        $selectedAsset = $_POST['selected_asset'];
        $selectedPath = realpath($selectedAsset);
        $targetPath = realpath($targetDir);

        if ($selectedPath && $targetPath && strpos($selectedPath, $targetPath . DIRECTORY_SEPARATOR) === 0 && is_file($selectedPath)) {
            $fileName = buildManagedAssetName($slot, basename($selectedPath));
            if (copy($selectedPath, $targetDir . $fileName)) {
                bumpKioskContentVersion($contentVersionFile);
                $message = "Asset synced to kiosk.";
            } else {
                $message = "Asset sync failed.";
            }
        } else {
            $message = "Invalid asset selected.";
        }
    } else {
        $message = "Select a file or library asset.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_category_sequence'])) {
    $sequenceIds = $_POST['sequence_id'] ?? [];
    $sequenceNos = $_POST['sequence_no'] ?? [];
    $activeRows = $_POST['is_active'] ?? [];
    $categoryDescriptions = $_POST['category_description'] ?? [];
    $selectedCategoryIcons = $_POST['selected_category_icon'] ?? [];
    $updated = 0;
    $iconUpdated = 0;

    $rowOrder = [];
    foreach ($sequenceIds as $index => $id) {
        $rowOrder[] = [
            'index' => $index,
            'sequence' => isset($sequenceNos[$index]) ? (int) $sequenceNos[$index] : ($index + 1),
        ];
    }

    usort($rowOrder, function ($a, $b) {
        if ($a['sequence'] === $b['sequence']) {
            return $a['index'] - $b['index'];
        }

        return $a['sequence'] - $b['sequence'];
    });

    foreach ($rowOrder as $newIndex => $rowOrderItem) {
        $index = $rowOrderItem['index'];
        $id = (int) $sequenceIds[$index];
        $sequenceNo = $newIndex + 1;
        $isActive = isset($activeRows[$id]) ? 1 : 0;

        if ($id <= 0) {
            continue;
        }

        $stmt = sqlsrv_query(
            $conn,
            "UPDATE dbo.KIOSK_CategorySequence SET SequenceNo = ?, IsActive = ? WHERE ID = ?",
            [$sequenceNo, $isActive, $id]
        );

        if ($stmt) {
            $updated++;
        }

        $categoryDescription = $categoryDescriptions[$id] ?? '';
        if ($categoryDescription !== '') {
            $targetIcon = $categoryIconDir . getCategoryIconFilename($categoryDescription);
            $uploadedIcon = $_FILES['category_icon'] ?? null;

            if (isset($uploadedIcon['error'][$id]) && $uploadedIcon['error'][$id] === UPLOAD_ERR_OK) {
                $sourceName = $uploadedIcon['name'][$id] ?? '';
                $extension = strtolower(pathinfo($sourceName, PATHINFO_EXTENSION));

                if ($extension === 'png' && move_uploaded_file($uploadedIcon['tmp_name'][$id], $targetIcon)) {
                    $iconUpdated++;
                }
            } elseif (!empty($selectedCategoryIcons[$id])) {
                $selectedIcon = realpath($selectedCategoryIcons[$id]);
                $iconRoot = realpath($categoryIconDir);

                if ($selectedIcon && $iconRoot && strpos($selectedIcon, $iconRoot . DIRECTORY_SEPARATOR) === 0 && is_file($selectedIcon)) {
                    $currentTargetIcon = realpath($targetIcon);
                    if ($currentTargetIcon && $selectedIcon === $currentTargetIcon) {
                        continue;
                    }

                    if (copy($selectedIcon, $targetIcon)) {
                        $iconUpdated++;
                    }
                }
            }
        }
    }

    if ($updated > 0 || $iconUpdated > 0) {
        bumpKioskContentVersion($contentVersionFile);
    }

    $message = ($updated > 0 || $iconUpdated > 0) ? "Category sequence saved." : "No category sequence changes saved.";
}

function bumpKioskContentVersion($versionFile)
{
    $versionDir = dirname($versionFile);
    if (!is_dir($versionDir)) {
        mkdir($versionDir, 0777, true);
    }

    file_put_contents($versionFile, (string) time(), LOCK_EX);
}

function buildManagedAssetName($prefix, $sourceName)
{
    $originalName = preg_replace("/^(slot[123]|head|main_header|bg)_\d+_/i", "", basename($sourceName));
    $safeName = preg_replace("/[^A-Za-z0-9.]/", "_", $originalName);
    return $prefix . "_" . time() . "_" . $safeName;
}

function getCategoryIconFilename($categoryName)
{
    $cleanName = strtolower(trim($categoryName));
    $cleanName = str_replace(' ', '_', $cleanName);
    $cleanName = preg_replace('/[\/\\\\:*?"<>|]/', '_', $cleanName);
    return $cleanName . '.png';
}

function getCategoryIconFile($dir, $categoryName)
{
    $iconFile = $dir . getCategoryIconFilename($categoryName);
    return is_file($iconFile) ? $iconFile : $dir . 'mains.png';
}

function getRecentFiles($dir)
{
    $files = glob($dir . "*.*");
    usort($files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    return $files;
}

function getLatestHeaderFile($dir)
{
    $files = array_merge(
        glob($dir . "main_header_*.*") ?: [],
        glob($dir . "head_*.*") ?: []
    );
    if (!$files) return null;

    usort($files, function ($a, $b) {
        $timeDiff = filemtime($b) - filemtime($a);
        return $timeDiff !== 0 ? $timeDiff : strcmp($b, $a);
    });

    return $files[0];
}

function getLatestSlotFile($dir, $slot)
{
    $files = glob($dir . $slot . "_*.*");
    if (!$files) return null;

    usort($files, function ($a, $b) {
        $timeDiff = filemtime($b) - filemtime($a);
        return $timeDiff !== 0 ? $timeDiff : strcmp($b, $a);
    });

    return $files[0];
}

function getSlotAssetUrl($slot, $file)
{
    return "../api/kioskAsset.php?s=" . urlencode($slot) . "&v=" . filemtime($file);
}

function getAssetsForType($type, $bannerDir, $headerDir, $videoDir)
{
    $dir = ($type === 'banners') ? $bannerDir : (($type === 'header') ? $headerDir : $videoDir);
    return getRecentFiles($dir);
}

function getCategoryIconAssets($dir)
{
    $files = glob($dir . "*.png") ?: [];
    usort($files, function ($a, $b) {
        return strcasecmp(basename($a), basename($b));
    });
    return $files;
}

function getCategorySequenceRows($conn)
{
    $query = "SELECT
            kcs.ID,
            kcs.CategoryID,
            kcs.SequenceNo,
            kcs.IsActive,
            COALESCE(c.Name, CONCAT('Category #', kcs.CategoryID)) AS CategoryDescription
        FROM dbo.KIOSK_CategorySequence kcs
        LEFT JOIN dbo.Category c ON c.ID = kcs.CategoryID
        ORDER BY kcs.SequenceNo, CategoryDescription";

    $stmt = sqlsrv_query($conn, $query);
    $rows = [];

    if ($stmt) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $rows[] = $row;
        }
    }

    return $rows;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kiosk Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fcfcfd;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .sidebar-item-active {
            color: #000;
            background: #f4f4f5;
            font-weight: 600;
            border-right: 2px solid #000;
        }

        .upload-zone:hover {
            border-color: #000;
            background: #fafafa;
        }

        /* ── Preview box ── */
        .file-preview-box {
            display: none;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            position: relative;
        }

        .file-preview-box.visible {
            display: block;
        }

        .file-preview-box img,
        .file-preview-box video {
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            display: block;
        }

        .file-preview-box .preview-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            padding: 5px 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Filename pill ── */
        .selected-name {
            display: none;
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            font-weight: 700;
            color: #52525b;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            border-radius: 999px;
            padding: 4px 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .selected-name.visible {
            display: block;
        }

        .asset-library-panel {
            display: none;
        }

        .asset-library-panel.visible {
            display: block;
        }

        .asset-library-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            max-height: 280px;
            overflow-y: auto;
        }

        .asset-library-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
            background: #fff;
            text-align: left;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .asset-library-card:hover,
        .asset-library-card.selected {
            border-color: #000;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .asset-library-card img,
        .asset-library-card video {
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            display: block;
            background: #f1f5f9;
        }

        .category-icon-option img {
            aspect-ratio: 1 / 1;
            object-fit: contain;
            padding: 0.25rem;
            background: #1e293b;
        }

        .category-icon-preview-box {
            background: #1e293b;
        }
    </style>
</head>

<body class="text-slate-900">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 border-r border-slate-200 flex flex-col bg-white">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-black rounded-full"></div>
                    <span class="font-extrabold tracking-tighter text-xl uppercase">PSC KIOSK</span>
                </div>
            </div>

            <nav class="flex-1 mt-4">
                <a href="?page=dashboard" class="flex items-center gap-3 px-6 py-3 text-sm text-slate-500 transition-all <?= $view === 'dashboard' ? 'sidebar-item-active' : 'hover:text-black' ?>">
                    <i data-lucide="monitor" class="w-4 h-4"></i> Live Studio
                </a>
                <a href="?page=archive" class="flex items-center gap-3 px-6 py-3 text-sm text-slate-500 transition-all <?= $view === 'archive' ? 'sidebar-item-active' : 'hover:text-black' ?>">
                    <i data-lucide="layers" class="w-4 h-4"></i> Asset Library
                </a>
                <a href="?page=category-sequence" class="flex items-center gap-3 px-6 py-3 text-sm text-slate-500 transition-all <?= $view === 'category-sequence' ? 'sidebar-item-active' : 'hover:text-black' ?>">
                    <i data-lucide="list-ordered" class="w-4 h-4"></i> Category Sequence
                </a>
            </nav>

            <div class="p-6 border-t border-slate-100">
                <a href="../" target="_blank" class="text-[10px] font-bold uppercase tracking-widest text-slate-400 hover:text-black flex items-center gap-2">
                    External View <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                </a>
            </div>
        </aside>

        <main class="flex-1 <?= $view === 'category-sequence' ? 'overflow-hidden' : 'overflow-y-auto' ?>">

            <div class="h-16 border-b border-slate-200 flex items-center justify-between px-10 bg-white/80 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <span class="text-xs mono font-bold bg-slate-100 px-2 py-1 rounded text-slate-600">V1.0.0</span>
                    <?php if ($message): ?>
                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1 animate-pulse">
                            <i data-lucide="check" class="w-3 h-3"></i> <?= $message ?>
                        </span>
                    <?php endif; ?>
                </div>

            </div>

            <div class="<?= $view === 'category-sequence' ? 'p-10 h-[calc(100vh-4rem)] overflow-hidden flex flex-col' : 'p-10' ?>">
                <?php if ($view === 'dashboard'): ?>
                    <div class="grid grid-cols-12 gap-10">

                        <div class="col-span-12 xl:col-span-4 space-y-10">
                            <div>
                                <h2 class="text-3xl font-extrabold tracking-tight mb-2">Studio</h2>
                                <p class="text-slate-500 text-sm">Update live kiosk components.</p>
                            </div>

                            <div class="space-y-6">
                                <?php
                                $types = [
                                    ['id' => 'banners', 'title' => 'Banner Ads', 'icon' => 'image'],
                                    ['id' => 'header', 'title' => 'Top Header', 'icon' => 'layout'],
                                    ['id' => 'background', 'title' => 'idle Video', 'icon' => 'video']
                                ];
                                foreach ($types as $t):
                                ?>
                                    <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm transition-all hover:shadow-md">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center border border-slate-100">
                                                <i data-lucide="<?= $t['icon'] ?>" class="w-4 h-4 text-slate-900"></i>
                                            </div>
                                            <h3 class="font-bold text-sm uppercase tracking-tight"><?= $t['title'] ?></h3>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                            <input type="hidden" name="upload_type" value="<?= $t['id'] ?>">
                                            <?php if ($t['id'] === 'banners'): ?>
                                                <select name="banner_slot" class="w-full text-xs font-bold bg-slate-50 border-none rounded-lg p-3">
                                                    <option value="slot1">SLOT_01</option>
                                                    <option value="slot2">SLOT_02</option>
                                                    <option value="slot3">SLOT_03</option>
                                                </select>
                                            <?php endif; ?>
                                            <div class="relative flex items-center justify-center p-4 border-2 border-dashed border-slate-200 rounded-xl upload-zone transition-all">
                                                <input type="file" name="file" class="absolute inset-0 opacity-0 cursor-pointer" onchange="handlePreview(this)">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">Click to Select File</span>
                                            </div>
                                            <?php $libraryAssets = getAssetsForType($t['id'], $bannerDir, $headerDir, $videoDir); ?>
                                            <input type="hidden" name="selected_asset" class="selected-asset-input">
                                            <button type="button" class="w-full flex items-center justify-center gap-2 p-3 bg-slate-50 text-xs font-black text-slate-600 uppercase tracking-widest rounded-xl hover:bg-slate-100 transition" onclick="toggleAssetLibrary(this)">
                                                <i data-lucide="image-plus" class="w-4 h-4"></i>
                                                Select from Asset Library
                                            </button>
                                            <div class="asset-library-panel border border-slate-200 rounded-xl p-3 bg-slate-50">
                                                <?php if ($libraryAssets): ?>
                                                    <div class="asset-library-grid">
                                                        <?php foreach ($libraryAssets as $asset):
                                                            $assetBaseName = basename($asset);
                                                            $isVideo = preg_match('/\.(mp4|webm|mov)$/i', $assetBaseName);
                                                            $assetSize = round(filesize($asset) / 1024, 1) . " KB";
                                                        ?>
                                                            <button type="button" class="asset-library-card" data-asset="<?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?>" data-kind="<?= $isVideo ? 'video' : 'image' ?>" data-name="<?= htmlspecialchars($assetBaseName, ENT_QUOTES, 'UTF-8') ?>" data-size="<?= htmlspecialchars($assetSize, ENT_QUOTES, 'UTF-8') ?>" onclick="selectAssetLibraryItem(this)">
                                                                <?php if ($isVideo): ?>
                                                                    <video src="<?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?>" muted loop></video>
                                                                <?php else: ?>
                                                                    <img src="<?= htmlspecialchars($asset, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($assetBaseName, ENT_QUOTES, 'UTF-8') ?>">
                                                                <?php endif; ?>
                                                                <div class="p-2">
                                                                    <p class="mono text-[9px] font-bold text-slate-500 truncate"><?= htmlspecialchars($assetBaseName) ?></p>
                                                                    <p class="text-[9px] font-bold text-slate-400"><?= htmlspecialchars($assetSize) ?></p>
                                                                </div>
                                                            </button>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="py-6 text-center text-xs font-bold text-slate-400 italic">No assets found.</div>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Added: filename pill -->
                                            <div class="selected-name"></div>
                                            <!-- Added: image/video preview -->
                                            <div class="file-preview-box">
                                                <div class="preview-bar"></div>
                                            </div>
                                            <button class="w-full py-3 bg-black text-white text-xs font-black uppercase tracking-widest rounded-xl hover:opacity-80 transition">Sync to Kiosk</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-span-12 xl:col-span-8">
                            <div class="mb-6">
                                <h2 class="text-xl font-bold tracking-tight">Active Deployment</h2>
                                <p class="text-slate-500 text-sm">Currently rendering on the kiosk hardware.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div class="flex flex-col gap-6">
                                    <div class="border border-slate-200 rounded-2xl p-4 bg-white shadow-sm">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase mono mb-3 block">Current Header</span>
                                        <div class="h-25 bg-slate-50 rounded-lg border border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                                            <?php $h = getLatestHeaderFile($headerDir);
                                            if ($h): ?>
                                                <img src="<?= htmlspecialchars($h, ENT_QUOTES, 'UTF-8') ?>?v=<?= filemtime($h) ?>" class="object-contain">
                                            <?php else: ?>
                                                <span class="text-xs italic text-slate-300">No active header</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="border border-slate-200 rounded-2xl p-6 bg-white shadow-sm flex-1">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase mono mb-4 block">Active Banner Slots</span>
                                        <div class="flex flex-col gap-4">
                                            <?php foreach (['slot1', 'slot2', 'slot3'] as $index => $s):
                                                $activeBanner = getLatestSlotFile($bannerDir, $s); ?>
                                                <div class="flex flex-col gap-1">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Slot 0<?= $index + 1 ?></p>
                                                    <div class="w-full rounded-xl bg-slate-50 border border-slate-200 overflow-hidden" style="height: 120px;">
                                                        <?php if ($activeBanner): ?>
                                                            <img src="<?= htmlspecialchars(getSlotAssetUrl($s, $activeBanner), ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                                                <i data-lucide="image-off" class="w-4 h-4 text-slate-300"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-slate-200 rounded-2xl p-4 bg-white shadow-sm">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase mono mb-3 block">Current Background</span>
                                    <div class="w-full bg-slate-900 rounded-lg overflow-hidden relative" style="aspect-ratio: 9/16;">
                                        <?php $v = getRecentFiles($videoDir);
                                        if ($v): ?>
                                            <video src="<?= $v[0] ?>" class="w-full h-full object-cover" muted autoplay loop></video>
                                            <div class="absolute bottom-2 left-2 bg-black/50 backdrop-blur-md px-2 py-1 rounded text-[10px] text-white mono">
                                                <?= basename($v[0]) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs italic">
                                                No motion background set
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                <?php elseif ($view === 'archive'): ?>
                    <div class="max-w-6xl">
                        <div class="mb-10">
                            <h2 class="text-4xl font-black tracking-tighter uppercase mb-2">Asset Library</h2>
                            <p class="text-slate-500 font-medium">Manage and cleanup system storage.</p>
                        </div>

                        <?php
                        $sections = [
                            ['title' => 'Carousel Banners', 'dir' => $bannerDir, 'cols' => 'grid-cols-4 lg:grid-cols-6'],
                            ['title' => 'Header Media', 'dir' => $headerDir, 'cols' => 'grid-cols-2 lg:grid-cols-4'],
                            ['title' => 'Video Backgrounds', 'dir' => $videoDir, 'cols' => 'grid-cols-2 lg:grid-cols-3']
                        ];
                        foreach ($sections as $sec):
                        ?>
                            <div class="mb-12">
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-6 border-b pb-2"><?= $sec['title'] ?></h3>
                                <div class="grid <?= $sec['cols'] ?> gap-4">
                                    <?php foreach (getRecentFiles($sec['dir']) as $file): ?>
                                        <div class="group border border-slate-200 rounded-xl overflow-hidden bg-white hover:border-black transition-all">
                                            <div class="aspect-video bg-slate-100 overflow-hidden relative">
                                                <?php if (strpos($file, '.mp4')): ?>
                                                    <video src="<?= $file ?>" class="w-full h-full object-cover"></video>
                                                <?php else: ?>
                                                    <img src="<?= $file ?>" class="w-full h-full object-cover">
                                                <?php endif; ?>
                                                <form method="POST" class="absolute inset-0 bg-white/90 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                    <input type="hidden" name="delete_file" value="<?= $file ?>">
                                                    <button class="text-xs font-black uppercase text-red-600 hover:underline">DELETE</button>
                                                </form>
                                            </div>
                                            <div class="p-3">
                                                <p class="mono text-[9px] text-slate-400 truncate"><?= basename($file) ?></p>
                                                <p class="text-[9px] font-bold text-slate-800"><?= round(filesize($file) / 1024, 1) ?> KB</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($view === 'category-sequence'): ?>
                    <?php $categorySequenceRows = getCategorySequenceRows($conn); ?>
                    <div class="w-full flex-1 min-h-0 flex flex-col">
                        <div class="mb-6 flex-shrink-0">
                            <h2 class="text-4xl font-black tracking-tighter uppercase mb-2">Category Sequence</h2>
                            <p class="text-slate-500 font-medium">Control kiosk category order and visibility.</p>
                        </div>

                        <form method="POST" enctype="multipart/form-data" class="border border-slate-200 rounded-2xl bg-white shadow-sm overflow-hidden flex flex-col flex-1 min-h-0">
                            <input type="hidden" name="save_category_sequence" value="1">
                            <div class="grid grid-cols-[90px_minmax(260px,1fr)_150px_140px_360px] gap-5 px-5 py-3 bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-widest mono flex-shrink-0">
                                <span>ID</span>
                                <span>Category</span>
                                <span>Sequence</span>
                                <span>Status</span>
                                <span>Icon</span>
                            </div>

                            <div id="categorySequenceList" class="divide-y divide-slate-100 flex-1 min-h-0 overflow-y-auto">
                                <?php if ($categorySequenceRows): ?>
                                    <?php $categoryIconAssets = getCategoryIconAssets($categoryIconDir); ?>
                                    <?php foreach ($categorySequenceRows as $row):
                                        $rowId = (int) $row['ID'];
                                        $isActive = (int) $row['IsActive'] === 1;
                                        $categoryDescription = (string) $row['CategoryDescription'];
                                        $currentIcon = getCategoryIconFile($categoryIconDir, $categoryDescription);
                                        $targetIconName = getCategoryIconFilename($categoryDescription);
                                    ?>
                                        <div class="category-sequence-row grid grid-cols-[90px_minmax(260px,1fr)_150px_140px_360px] gap-5 px-5 py-4 items-center">
                                            <input type="hidden" name="sequence_id[]" value="<?= $rowId ?>">
                                            <input type="hidden" name="category_description[<?= $rowId ?>]" value="<?= htmlspecialchars($categoryDescription, ENT_QUOTES, 'UTF-8') ?>">
                                            <span class="mono text-[11px] font-bold text-slate-400">#<?= (int) $row['CategoryID'] ?></span>
                                            <div>
                                                <span class="text-sm font-bold text-slate-900"><?= htmlspecialchars($categoryDescription) ?></span>
                                                <p class="mono text-[9px] font-bold text-slate-400 mt-1"><?= htmlspecialchars($targetIconName) ?></p>
                                            </div>
                                            <input type="number" min="1" name="sequence_no[]" value="<?= (int) $row['SequenceNo'] ?>" class="sequence-input w-full text-sm font-bold bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                                            <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-600">
                                                <input type="checkbox" name="is_active[<?= $rowId ?>]" value="1" <?= $isActive ? 'checked' : '' ?> class="w-4 h-4 accent-black">
                                                Active
                                            </label>
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="category-icon-preview-box w-16 h-16 rounded-xl border border-slate-200 overflow-hidden flex items-center justify-center">
                                                        <?php if (is_file($currentIcon)): ?>
                                                            <img src="<?= htmlspecialchars($currentIcon, ENT_QUOTES, 'UTF-8') ?>?v=<?= filemtime($currentIcon) ?>" class="category-icon-preview w-full h-full object-contain" alt="<?= htmlspecialchars($categoryDescription, ENT_QUOTES, 'UTF-8') ?>">
                                                        <?php else: ?>
                                                            <i data-lucide="image-off" class="w-5 h-5 text-slate-300"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-1 space-y-2">
                                                        <label class="relative flex items-center justify-center p-2 border-2 border-dashed border-slate-200 rounded-xl upload-zone transition-all cursor-pointer">
                                                            <input type="file" name="category_icon[<?= $rowId ?>]" accept=".png,image/png" class="absolute inset-0 opacity-0 cursor-pointer" onchange="handleCategoryIconUpload(this)">
                                                            <span class="text-[9px] font-black text-slate-400 uppercase">Select PNG</span>
                                                        </label>
                                                        <input type="hidden" name="selected_category_icon[<?= $rowId ?>]" class="selected-category-icon-input">
                                                        <button type="button" class="w-full p-2 bg-slate-50 text-[9px] font-black text-slate-600 uppercase tracking-widest rounded-xl hover:bg-slate-100 transition" onclick="toggleCategoryIconLibrary(this)">Use Existing</button>
                                                    </div>
                                                </div>
                                                <div class="category-icon-library-panel asset-library-panel border border-slate-200 rounded-xl p-2 bg-slate-50">
                                                    <?php if ($categoryIconAssets): ?>
                                                        <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                                            <?php foreach ($categoryIconAssets as $iconAsset):
                                                                $iconAssetName = basename($iconAsset);
                                                            ?>
                                                                <button type="button" class="asset-library-card category-icon-option" data-icon="<?= htmlspecialchars($iconAsset, ENT_QUOTES, 'UTF-8') ?>" data-name="<?= htmlspecialchars($iconAssetName, ENT_QUOTES, 'UTF-8') ?>" onclick="selectCategoryIconItem(this)">
                                                                    <img src="<?= htmlspecialchars($iconAsset, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($iconAssetName, ENT_QUOTES, 'UTF-8') ?>">
                                                                    <div class="p-1">
                                                                        <p class="mono text-[8px] font-bold text-slate-500 truncate"><?= htmlspecialchars($iconAssetName) ?></p>
                                                                    </div>
                                                                </button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="py-4 text-center text-[10px] font-bold text-slate-400 italic">No PNG icons found.</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="px-5 py-10 text-center text-sm text-slate-400 italic">
                                        No kiosk category sequence rows found.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center justify-end gap-3 px-5 py-4 bg-white border-t border-slate-100 flex-shrink-0">
                                <button class="px-5 py-3 bg-black text-white text-xs font-black uppercase tracking-widest rounded-xl hover:opacity-80 transition">Save Sequence</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function handlePreview(input) {
            const file = input.files[0];
            if (!file) return;

            const form = input.closest('form');
            const selectedAssetInput = form.querySelector('.selected-asset-input');
            if (selectedAssetInput) {
                selectedAssetInput.value = '';
            }
            form.querySelectorAll('.asset-library-card.selected').forEach((card) => {
                card.classList.remove('selected');
            });
            const nameEl = form.querySelector('.selected-name');
            const previewBox = form.querySelector('.file-preview-box');
            const previewBar = form.querySelector('.preview-bar');

            // Show filename
            nameEl.textContent = file.name;
            nameEl.classList.add('visible');

            // Remove previous media if any
            const old = previewBox.querySelector('img, video');
            if (old) old.remove();

            // Create new media element
            const url = URL.createObjectURL(file);
            let media;
            if (file.type.startsWith('video/')) {
                media = document.createElement('video');
                media.src = url;
                media.muted = true;
                media.autoplay = true;
                media.loop = true;
            } else {
                media = document.createElement('img');
                media.src = url;
                media.alt = file.name;
            }

            previewBox.insertBefore(media, previewBar);
            previewBar.textContent = file.name + '  ·  ' + (file.size / 1024).toFixed(1) + ' KB';
            previewBox.classList.add('visible');
        }

        function toggleAssetLibrary(button) {
            const form = button.closest('form');
            const panel = form.querySelector('.asset-library-panel');
            if (!panel) return;

            panel.classList.toggle('visible');
            panel.querySelectorAll('video').forEach((video) => {
                if (panel.classList.contains('visible')) {
                    video.play().catch(() => {});
                } else {
                    video.pause();
                }
            });
        }

        function selectAssetLibraryItem(card) {
            const form = card.closest('form');
            const selectedAssetInput = form.querySelector('.selected-asset-input');
            const fileInput = form.querySelector('input[type="file"]');
            const nameEl = form.querySelector('.selected-name');
            const previewBox = form.querySelector('.file-preview-box');
            const previewBar = form.querySelector('.preview-bar');

            if (fileInput) {
                fileInput.value = '';
            }

            if (selectedAssetInput) {
                selectedAssetInput.value = card.dataset.asset || '';
            }

            form.querySelectorAll('.asset-library-card.selected').forEach((assetCard) => {
                assetCard.classList.remove('selected');
            });
            card.classList.add('selected');

            const old = previewBox.querySelector('img, video');
            if (old) old.remove();

            const fileName = card.dataset.name || 'Library asset';
            const previewUrl = card.dataset.asset;
            const mediaKind = card.dataset.kind;

            let media;
            if (mediaKind === 'video') {
                media = document.createElement('video');
                media.src = previewUrl;
                media.muted = true;
                media.autoplay = true;
                media.loop = true;
            } else {
                media = document.createElement('img');
                media.src = previewUrl;
                media.alt = fileName;
            }

            nameEl.textContent = fileName;
            nameEl.classList.add('visible');
            previewBox.insertBefore(media, previewBar);
            previewBar.textContent = fileName + '  ·  ' + (card.dataset.size || 'Library asset');
            previewBox.classList.add('visible');
        }

        function setCategoryIconPreview(row, imageUrl, imageName) {
            const previewWrap = row.querySelector('.w-16.h-16');
            if (!previewWrap) return;

            previewWrap.innerHTML = '';
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = imageName || 'Category icon';
            img.className = 'category-icon-preview w-full h-full object-contain';
            previewWrap.appendChild(img);
        }

        function handleCategoryIconUpload(input) {
            const file = input.files[0];
            if (!file) return;

            if (!file.name.toLowerCase().endsWith('.png')) {
                alert('Please select a PNG file.');
                input.value = '';
                return;
            }

            const row = input.closest('.category-sequence-row');
            const selectedIconInput = row.querySelector('.selected-category-icon-input');
            if (selectedIconInput) {
                selectedIconInput.value = '';
            }

            row.querySelectorAll('.category-icon-option.selected').forEach((card) => {
                card.classList.remove('selected');
            });

            setCategoryIconPreview(row, URL.createObjectURL(file), file.name);
        }

        function toggleCategoryIconLibrary(button) {
            const row = button.closest('.category-sequence-row');
            const panel = row.querySelector('.category-icon-library-panel');
            if (!panel) return;

            panel.classList.toggle('visible');
        }

        function selectCategoryIconItem(card) {
            const row = card.closest('.category-sequence-row');
            const selectedIconInput = row.querySelector('.selected-category-icon-input');
            const fileInput = row.querySelector('input[type="file"]');

            if (fileInput) {
                fileInput.value = '';
            }

            if (selectedIconInput) {
                selectedIconInput.value = card.dataset.icon || '';
            }

            row.querySelectorAll('.category-icon-option.selected').forEach((option) => {
                option.classList.remove('selected');
            });
            card.classList.add('selected');

            setCategoryIconPreview(row, card.dataset.icon, card.dataset.name);
        }
    </script>
</body>

</html>
