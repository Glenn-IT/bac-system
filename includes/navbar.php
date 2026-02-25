<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="logo">
        <img src="../img/Logo.jpg" alt="BFAR Logo"><br>
        <span style="font-size:0.78rem; opacity:0.9; line-height:1.4;">Bureau of Fisheries<br>and Aquatic Resources<br><span style="font-size:0.7rem; opacity:0.7;">Bids &amp; Awards Committee</span></span>
    </div>
    
    <nav class="nav flex-column">
        <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="../public/dashboard.php">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        
        <?php if (in_array($_SESSION['user_role'], ['Admin', 'BAC Secretariat Staff', 'BAC Committee Member'])): ?>
        <a class="nav-link <?php echo strpos($current_page, 'records') !== false ? 'active' : ''; ?>" href="../records/list.php">
            <i class="bi bi-file-earmark-plus"></i> New Record
        </a>
        
        <!--<a class="nav-link <?php echo strpos($current_page, 'suppliers') !== false ? 'active' : ''; ?>" href="../suppliers/list.php">
            <i class="bi bi-building"></i> Suppliers
        </a>-->
        
        <!--<a class="nav-link <?php echo strpos($current_page, 'documents') !== false ? 'active' : ''; ?>" href="../documents/list.php">
            <i class="bi bi-file-earmark-text"></i> Documents
        </a>-->
        
        <!--<a class="nav-link <?php echo strpos($current_page, 'bac-docs') !== false ? 'active' : ''; ?>" href="../bac-docs/list.php">
            <i class="bi bi-folder-fill"></i> BAC Documents
        </a>-->
        
        <a class="nav-link <?php echo (strpos($current_page, 'compliance.php') !== false && strpos($_SERVER['REQUEST_URI'], 'bac-docs') !== false) ? 'active' : ''; ?>" href="../bac-docs/compliance.php">
            <i class="bi bi-clipboard-check"></i> BAC Compliance Check
        </a>
        
        <!--<a class="nav-link <?php echo $current_page == 'compliance.php' ? 'active' : ''; ?>" href="../documents/compliance.php">
            <i class="bi bi-clipboard-check"></i> Compliance Check
        </a>-->
        <?php endif; ?>
        
        <?php /* HIDDEN - Users menu disabled*/
        if ($_SESSION['user_role'] == 'Admin'): ?>
        <a class="nav-link <?php echo strpos($current_page, 'users') !== false ? 'active' : ''; ?>" href="../users/list.php">
            <i class="bi bi-people"></i> Users
        </a>
        <?php endif;  ?>
        
        <?php /* HIDDEN - Activity Logs menu disabled*/
        if (in_array($_SESSION['user_role'], ['Admin', 'Auditor/COA'])): ?>
        <a class="nav-link <?php echo $current_page == 'activity.php' ? 'active' : ''; ?>" href="../logs/activity.php">
            <i class="bi bi-clock-history"></i> Activity Logs
        </a>
        <?php endif;  ?>
        
        <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 15px;">
        
       <!-- --><a class="nav-link" href="../public/logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a> 
    </nav>
</div>

<div class="main-content">
    <div class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
            <div>
                <span class="badge bg-primary me-2"><?php echo $_SESSION['user_role']; ?></span>
                <span><i class="bi bi-person-circle"></i> <?php echo $_SESSION['full_name']; ?></span>
            </div>
        </div>
    </div>
