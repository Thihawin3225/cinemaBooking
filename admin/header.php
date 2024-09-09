<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>AP Shopping</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <?php
      $link = $_SERVER['PHP_SELF'];
      $link_array = explode('/',$link);
      $page = end($link_array);
    ?>


    <!-- SEARCH FORM -->
    <?php if($page == 'index.php' || $page == 'halls.php' || $page == 'booking_history.php' || $page == 'showtimes.php' || $page == 'rowandprice_manage.php' || $page == 'seat_manage.php' || $page == 'booking_manage.php' || $page == 'user_list.php') {?>
          <form class="form-inline ml-3" method="post"
          <?php if($page == 'index.php') :?>
            action="index.php"
          <?php elseif($page == 'booking_history') :?>
            action="booking_history.php"
          <?php elseif($page == 'halls.php'):?>
            action="halls.php"
          <?php elseif($page == 'seat_manage.php'):?>
            action="seat_manage.php"
          <?php elseif($page == 'booking_manage'):?>
            action="booking_manage.php"
          <?php elseif($page == 'rowandprice_manage'):?>
            action="rowandprice_manage.php"
          <?php elseif($page == 'showtimes.php'):?>
            action="showtimes.php"
          <?php endif;?>
          >
            <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
            <div class="input-group input-group-sm">
              <input name="search" class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </form>
      <?php } ?>
    

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="../images/download.jpg" alt="Cinema Booking Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Cinema Booking</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex" style="align-items: center;">
        <div class="image">
        <i class="nav-icon fas fa-user" style="color: white;"></i>
        </div>
        <div class="info">
          <a href="#" class="d-block" style="text-transform: uppercase;"><?php echo $_SESSION['user_name'] ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fas fa-film"></i>
              <p>
                Movies
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="halls.php" class="nav-link">
            <i class="fas fa-door-open"></i>
            <p>
                Halls
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="showtimes.php" class="nav-link">
              <i class="nav-icon fas fa-clock"></i>
              <p>
                Showtime
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="seat_manage.php" class="nav-link">
              <i class="nav-icon fas fa-chair"></i>
              <p>
                Seats
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="rowandprice_manage.php" class="nav-link">
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>
                Row And Price
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="user_list.php" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>
                user
              </p>
            </a>
          </li>

                    <li class="nav-item has-treeview menu">
            <a href="booking_manage.php" class="nav-link">
              <i class="nav-icon fas fa-calendar-check"></i>
              <p>
                Booking Manage
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="booking_history.php" class="nav-link">
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>
                History
              </p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">

    </div>
    <!-- /.content-header -->