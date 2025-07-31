<?php
$settingModel = new \App\Models\SettingModel();
$siteLogo = $settingModel->getSetting('site_logo', '');
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="admin-logo">
            <?php if (!empty($siteLogo) && file_exists(FCPATH . $siteLogo)): ?>
                <img src="<?= base_url($siteLogo) ?>" alt="Site Logo" class="sidebar-logo">
                <span class="sidebar-text admin-text">Admin Panel</span>
            <?php else: ?>
                <h4>
                    <i class="fas fa-mushroom me-2"></i>
                    <span class="sidebar-text">Admin Panel</span>
                </h4>
            <?php endif; ?>
        </div>
    </div>

    <nav class="nav flex-column">
        <?php foreach ($sidebarItems as $item): ?>
            <?php 
                $isActive = ($activeSection === $item['key']);
                $hasSubmenu = isset($item['submenu']) && !empty($item['submenu']);
            ?>
            
            <div class="nav-item">
                <?php if ($hasSubmenu): ?>
                    <a class="nav-link <?= $isActive ? 'active' : '' ?>" 
                       href="#" 
                       data-bs-toggle="collapse" 
                       data-bs-target="#submenu-<?= $item['key'] ?>" 
                       aria-expanded="<?= $isActive ? 'true' : 'false' ?>">
                        <i class="<?= $item['icon'] ?>"></i>
                        <span class="sidebar-text"><?= $item['title'] ?></span>
                        <i class="fas fa-chevron-down ms-auto submenu-arrow"></i>
                    </a>
                    
                    <div class="collapse submenu <?= $isActive ? 'show' : '' ?>" id="submenu-<?= $item['key'] ?>">
                        <?php foreach ($item['submenu'] as $subItem): ?>
                            <a class="nav-link" href="<?= $subItem['url'] ?>">
                                <i class="fas fa-circle" style="font-size: 0.5em;"></i>
                                <span class="sidebar-text"><?= $subItem['title'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= $item['url'] ?>">
                        <i class="<?= $item['icon'] ?>"></i>
                        <span class="sidebar-text"><?= $item['title'] ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <!-- Divider -->
        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
        
        <!-- Additional Links -->
        <a class="nav-link" href="<?= base_url() ?>" target="_blank">
            <i class="fas fa-globe"></i>
            <span class="sidebar-text">View Website</span>
        </a>
        
        <a class="nav-link" href="<?= base_url('/logout') ?>">
            <i class="fas fa-sign-out-alt"></i>
            <span class="sidebar-text">Logout</span>
        </a>
    </nav>
</div>

<style>
    .admin-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .sidebar-logo {
        height: 40px;
        max-width: 150px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    .admin-text {
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
        margin: 0;
    }

    .submenu-arrow {
        transition: transform 0.3s ease;
    }

    .nav-link[aria-expanded="true"] .submenu-arrow {
        transform: rotate(180deg);
    }

    .sidebar.collapsed .sidebar-text {
        display: none;
    }

    .sidebar.collapsed .admin-text {
        display: none;
    }

    .sidebar.collapsed .submenu-arrow {
        display: none;
    }

    .sidebar.collapsed .submenu {
        display: none !important;
    }

    .sidebar.collapsed .nav-link {
        text-align: center;
        padding: 12px 10px;
    }

    .sidebar.collapsed .sidebar-header h4 .sidebar-text {
        display: none;
    }
    
    @media (max-width: 768px) {
        .sidebar-text {
            display: none;
        }
        
        .submenu-arrow {
            display: none;
        }
        
        .submenu {
            display: none !important;
        }
        
        .nav-link {
            text-align: center;
            padding: 12px 10px;
        }
    }
</style>
