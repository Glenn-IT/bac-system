<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>BAC System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bfar-navy:   #0a2850;
            --bfar-green:  #006440;
            --bfar-light:  #e8f5ee;
            --primary-color: #0a2850;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--bfar-navy) 0%, #0d3b6e 60%, var(--bfar-green) 100%);
            padding-top: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar .logo {
            text-align: center;
            padding: 20px;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.5);
            margin-bottom: 8px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.82);
            padding: 11px 20px;
            margin: 3px 12px;
            border-radius: 8px;
            transition: all 0.25s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            padding-left: 26px;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            background: #f0f4f8;
        }

        .top-navbar {
            background: white;
            padding: 15px 30px;
            margin: -20px -20px 20px -20px;
            box-shadow: 0 2px 6px rgba(10,40,80,0.10);
            border-bottom: 3px solid var(--bfar-green);
        }

        /* ── Cards ── */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(10,40,80,0.08);
            border-radius: 10px;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid var(--bfar-light);
            font-weight: 600;
            color: var(--bfar-navy);
            padding: 15px 20px;
        }

        /* ── Stats Cards ── */
        .stats-card {
            background: linear-gradient(135deg, var(--bfar-navy) 0%, #0d3b6e 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--bfar-green) 0%, #00875a 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #b86e00 0%, #e08c00 100%);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, #a80000 0%, #d32f2f 100%);
        }

        /* ── Bootstrap overrides ── */
        .btn-primary {
            background-color: var(--bfar-navy);
            border-color: var(--bfar-navy);
        }
        .btn-primary:hover {
            background-color: #0d3b6e;
            border-color: #0d3b6e;
        }

        .btn-success {
            background-color: var(--bfar-green);
            border-color: var(--bfar-green);
        }
        .btn-success:hover {
            background-color: #00522e;
            border-color: #00522e;
        }

        .badge.bg-primary {
            background-color: var(--bfar-navy) !important;
        }

        .text-primary {
            color: var(--bfar-navy) !important;
        }

        a {
            color: var(--bfar-navy);
        }
        a:hover {
            color: var(--bfar-green);
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .btn-action {
            padding: 5px 10px;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
