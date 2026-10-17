<?php $session = session();
$datos = $session->get();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('assets/images/logo.png'); ?>">
    <link href="<?php echo base_url('assets/plugins/simplebar/css/simplebar.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css'); ?>" rel="stylesheet" />
    <link href="<?php echo base_url('assets/plugins/metismenu/css/metisMenu.min.css'); ?>" rel="stylesheet" />
    <!-- loader-->
    <link href="<?php echo base_url('assets/css/pace.min.css'); ?>" rel="stylesheet" />
    <script src="<?php echo base_url('assets/js/pace.min.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/jquery-ui.min.css'); ?>">
    <!-- Bootstrap CSS -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/bootstrap-extended.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/app.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/icons.css'); ?>" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dark-theme.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/semi-dark.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/header-colors.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/DataTables/datatables.min.css'); ?>" />
    <link href="<?php echo base_url('css/notie.min.css'); ?>" rel="stylesheet" />

    <title>Sistema de venta</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="<?php echo base_url('assets/images/logo.png'); ?>" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text">PV</h4>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <li>
                    <a href="<?php echo base_url('admin/dashboard'); ?>">
                        <div class="parent-icon"><i class="fa-solid fa-house-user"></i>
                        </div>
                        <div class="menu-title">Tablero</div>
                    </a>
                </li>
                <?php if (
                    verificar('actualizar', $datos['permisos']) || verificar('listar usuarios', $datos['permisos'])
                    || verificar('listar roles', $datos['permisos']) || verificar('backup', $datos['permisos'])
                ) { ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <div class="menu-title">Administración</div>
                        </a>
                        <ul>
                            <?php if (verificar('listar usuarios', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('usuarios'); ?>"><i class="bx bx-right-arrow-alt"></i>Usuarios</a>
                                </li>
                            <?php }
                            if (verificar('actualizar', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('admin'); ?>"><i class="bx bx-right-arrow-alt"></i>Configuracion</a>
                                </li>
                            <?php }
                            if (verificar('listar roles', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('roles'); ?>"><i class="bx bx-right-arrow-alt"></i>Roles</a>
                                </li>
                            <?php }
                            if (verificar('backup', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('backup'); ?>"><i class="bx bx-right-arrow-alt"></i>Backup</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php }
                if (
                    verificar('listar categorias', $datos['permisos']) || verificar('listar marcas', $datos['permisos'])
                    || verificar('listar productos', $datos['permisos'])
                ) { ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div class="menu-title">Mantenimiento</div>
                        </a>
                        <ul>
                            <?php if (verificar('listar categorias', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('categorias'); ?>"><i class="bx bx-right-arrow-alt"></i>Categorias</a>
                                </li>
                            <?php }
                            if (verificar('listar marcas', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('marcas'); ?>"><i class="bx bx-right-arrow-alt"></i>Marcas</a>
                                </li>
                            <?php }
                            if (verificar('listar productos', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('productos'); ?>"><i class="bx bx-right-arrow-alt"></i>Productos</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php }
                if (verificar('listar clientes', $datos['permisos'])) { ?>
                    <li>
                        <a href="<?php echo base_url('clientes'); ?>">
                            <div class="parent-icon"><i class="fa-solid fa-users"></i>
                            </div>
                            <div class="menu-title">Clientes</div>
                        </a>
                    </li>
                <?php }
                if (verificar('listar cajas', $datos['permisos']) || verificar('apertura y cierre', $datos['permisos'])) { ?>
                    <li>
                        <a href="<?php echo base_url('cajas'); ?>">
                            <div class="parent-icon"><i class="fa-solid fa-box-open"></i>
                            </div>
                            <div class="menu-title">Cajas</div>
                        </a>
                    </li>

                <?php }
                if (verificar('nueva compra', $datos['permisos']) || verificar('listar compras', $datos['permisos'])) { ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <div class="menu-title">Compras</div>
                        </a>
                        <ul>
                            <?php if (verificar('nueva compra', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('compras'); ?>"><i class="bx bx-right-arrow-alt"></i>Compras</a>
                                </li>
                            <?php }
                            if (verificar('listar compras', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('compras/historial'); ?>"><i class="bx bx-right-arrow-alt"></i>Historial</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php }
                if (verificar('nueva venta', $datos['permisos']) || verificar('listar ventas', $datos['permisos'])) { ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-cash-register"></i>
                            </div>
                            <div class="menu-title">Ventas</div>
                        </a>
                        <ul>
                            <?php if (verificar('nueva venta', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('ventas'); ?>"><i class="bx bx-right-arrow-alt"></i>Ventas</a>
                                </li>
                            <?php }
                            if (verificar('listar ventas', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('ventas/historial'); ?>"><i class="bx bx-right-arrow-alt"></i>Historial</a>
                                </li>
                            <?php }
                            if (verificar('creditos ventas', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('credventas'); ?>"><i class="bx bx-right-arrow-alt"></i>Creditos</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php }
                if (verificar('nueva cotizacion', $datos['permisos']) || verificar('listar cotizaciones', $datos['permisos'])) { ?>

                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-list"></i>
                            </div>
                            <div class="menu-title">Cotizaciones</div>
                        </a>
                        <ul>
                            <?php if (verificar('nueva cotizacion', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('cotizaciones'); ?>"><i class="bx bx-right-arrow-alt"></i>Cotizaciones</a>
                                </li>
                            <?php }
                            if (verificar('listar cotizaciones', $datos['permisos'])) { ?>
                                <li> <a href="<?php echo base_url('cotizaciones/historial'); ?>"><i class="bx bx-right-arrow-alt"></i>Historial</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                    </div>
                    <div class="search-bar flex-grow-1">
                        <div class="position-relative">
                            <h6>POS</h6>
                        </div>
                    </div>
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo base_url('img/logo.png'); ?>" class="user-img" alt="user-img">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0 text-uppercase"><?php echo $datos['usuario']; ?></p>
                                <p class="designattion mb-0"><?php echo $datos['nombre']; ?></p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo base_url('usuarios/perfil'); ?>"><i class="bx bx-user"></i><span>Perfil</span></a>
                            </li>
                            <li>
                                <div class="dropdown-divider mb-0"></div>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo base_url('usuarios/salir'); ?>"><i class='bx bx-log-out-circle'></i><span>Salir</span></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">