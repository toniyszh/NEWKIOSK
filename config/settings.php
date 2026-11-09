<?php
$configFile = 'config.php';
$config = include($configFile);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['db_server'] = $_POST['db_server'];
    $config['db_name'] = $_POST['db_name'];
    $config['db_user'] = $_POST['db_user'];
    $config['db_pass'] = $_POST['db_pass'];
    $config['register_no'] = (int)$_POST['register_no'];
    $config['queue_prefix'] = (int)$_POST['queue_prefix'];
    $config['central_server_url'] = $_POST['central_server_url'];

    $printers = explode(',', $_POST['printer_names']);
    $config['printer_names'] = array_filter(array_map('trim', $printers));

    $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
    if (file_put_contents($configFile, $content)) {
        $message = "Configuration saved successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings — Partner Solutions</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #0f0f0f;
            min-height: 100vh;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: black;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-left img {
            height: 22px;
            display: block;
        }

        .topbar-brand {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #111;
        }

        .topbar-sep {
            width: 1px;
            height: 16px;
            background: #ddd;
        }

        .topbar-page {
            font-size: .78rem;
            color: #999;
            font-weight: 500;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: .72rem;
            font-weight: 600;
            color: #15803d;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .4;
            }
        }


        .page {
            max-width: 680px;
            margin: 0 auto;
            padding: 120px 32px 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .page-heading {
            margin-bottom: 56px;
        }

        .page-heading .eyebrow {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #bbb;
            margin-bottom: 10px;
        }

        .page-heading h1 {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: -.05em;
            line-height: 1;
            color: #0f0f0f;
        }

        .page-heading p {
            margin-top: 10px;
            font-size: .9rem;
            color: #aaa;
            font-weight: 400;
        }


        .success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: .85rem;
            font-weight: 500;
            color: #15803d;
            margin-bottom: 40px;
            animation: fadeIn .3s ease;
        }

        .success-icon {
            width: 20px;
            height: 20px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
            font-size: .75rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section {
            margin-bottom: 52px;
        }

        .section-head {
            display: flex;
            align-items: baseline;
            gap: 14px;
            margin-bottom: 24px;
        }

        .section-head h2 {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -.025em;
            color: #111;
        }

        .section-head .line {
            flex: 1;
            height: 1px;
            background: #f0f0f0;
        }


        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full {
            grid-column: 1 / -1;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field label {
            font-size: .78rem;
            font-weight: 600;
            color: #888;
            letter-spacing: .01em;
        }

        .input-wrap {
            position: relative;
        }

        .field input {
            width: 100%;
            padding: 13px 16px;
            font-size: .9rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            color: #111;
            background: #fff;
            border: 1.5px solid #e8e8e8;
            border-radius: 12px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .05), 0 0 0 1px rgba(0, 0, 0, .03);
            transition: border-color .2s, box-shadow .2s;
        }

        .field input:hover {
            border-color: #ccc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08), 0 0 0 1px rgba(0, 0, 0, .04);
        }

        .field input:focus {
            border-color: #111;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .1), 0 0 0 4px rgba(0, 0, 0, .06);
        }

        .field input::placeholder {
            color: #d0d0d0;
            font-weight: 400;
        }

        .field .hint {
            font-size: .72rem;
            color: #ccc;
            font-weight: 400;
        }


        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 32px;
            border-top: 1px solid #f0f0f0;
        }

        .actions-note {
            font-size: .8rem;
            color: #ccc;
        }

        .btn {
            background: #0f0f0f;
            color: #fff;
            border: none;
            padding: 13px 30px;
            border-radius: 12px;
            font-size: .875rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            letter-spacing: -.01em;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
        }

        .btn:hover {
            background: #2a2a2a;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .2);
        }

        .btn:active {
            transform: scale(.97);
        }

        /* ── Footer ── */
        .page-footer {
            text-align: center;
            margin-top: 60px;
            font-size: .72rem;
            color: #ddd;
            padding-bottom: 40px;
        }

        @media (max-width: 520px) {
            .topbar {
                padding: 0 20px;
            }

            .page {
                padding: 100px 20px 60px;
            }

            .page-heading h1 {
                font-size: 2.2rem;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: 1;
            }

            .actions {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .btn {
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <header class="topbar">
        <div class="topbar-left">
            <img src="../images/logos/pscLogo.png" alt="" onerror="this.style.display='none'">

            <span class="topbar-page">Settings</span>
        </div>
        <button class="exit-btn" onclick="exitApp()">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
            Exit
        </button>
    </header>

    <main class="page">

        <div class="page-heading">
            <div class="eyebrow">Node Configuration</div>
            <h1>Settings</h1>
            <p>Manage your database, hardware, and API connections.</p>
        </div>

        <?php if ($message): ?>
            <div class="success">
                <div class="success-icon">✓</div>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">


            <div class="section">
                <div class="section-head">
                    <h2>Database</h2>
                    <div class="line"></div>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label>Host Server</label>
                        <input type="text" name="db_server"
                            value="<?php echo htmlspecialchars($config['db_server']); ?>" placeholder="localhost">
                    </div>
                    <div class="field">
                        <label>Database Name</label>
                        <input type="text" name="db_name" value="<?php echo htmlspecialchars($config['db_name']); ?>"
                            placeholder="my_database">
                    </div>
                    <div class="field">
                        <label>Username</label>
                        <input type="text" name="db_user" value="<?php echo htmlspecialchars($config['db_user']); ?>"
                            placeholder="root">
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="db_pass"
                            value="<?php echo htmlspecialchars($config['db_pass']); ?>" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-head">
                    <h2>Hardware &amp; Logic</h2>
                    <div class="line"></div>
                </div>
                <div class="grid-2">
                    <div class="field full">
                        <label>Active Printers</label>
                        <input type="text" name="printer_names"
                            value="<?php echo htmlspecialchars(implode(', ', $config['printer_names'])); ?>"
                            placeholder="POS80, KITCHEN_1">
                        <span class="hint">Comma-separated printer identifiers</span>
                    </div>
                    <div class="field">
                        <label>Register No.</label>
                        <input type="number" name="register_no" value="<?php echo (int)$config['register_no']; ?>">
                    </div>
                    <div class="field">
                        <label>Queue Prefix</label>
                        <input type="number" name="queue_prefix" value="<?php echo (int)$config['queue_prefix']; ?>">
                    </div>

                </div>
            </div>


            <div class="actions">
                <span class="actions-note">Changes apply immediately to config.</span>
                <button type="submit" class="btn">Save Changes</button>
            </div>

        </form>

        <div class="page-footer">&copy; 2026 PartnerSolutions, co. </div>

    </main>
    <script>
        function exitApp() {
            window.open('', '_self', '');
            window.close();
        }
    </script>
</body>

</html>