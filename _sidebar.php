<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../dashboard.php" class="brand-link">
      <img src="../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="border-radius: 50%;">
      <span class="brand-text font-weight-light">Sakila DB</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">
            <span id="sidebarUsername">Admin Sakila</span>
          </a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="../dashboard.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
      
          <li class="nav-item noGuest noClient">
            <a href="../register.php" class="nav-link active">
              <p>Registrar usuario</p>
            </a>
          </li>
          <li class="nav-item" id="actor">
            <a href="../tablas/actor.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Actor</p>
            </a>
          </li>
          <li class="nav-item noClient" id="address">
            <a href="../tablas/address.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Address</p>
            </a>
          </li>
          <li class="nav-item" id="category">
            <a href="../tablas/category.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Category</p>
            </a>
          </li>
          <li class="nav-item noClient" id="city">
            <a href="../tablas/city.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>City</p>
            </a>
          </li>
          <li class="nav-item noClient" id="country">
            <a href="../tablas/country.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Country</p>
            </a>
          </li>
          <li class="nav-item noClient" id="customer">
            <a href="../tablas/customer.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Customer</p>
            </a>
          </li>
          <li class="nav-item" id="film">
            <a href="../tablas/film.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Film</p>
            </a>
          </li>
          <li class="nav-item" id="film-actor">
            <a href="../tablas/film_actor.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Film Actor</p>
            </a>
          </li>
          <li class="nav-item" id="film-category">
            <a href="../tablas/film_category.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Film Category</p>
            </a>
          </li>
          <li class="nav-item" id="film-text">
            <a href="../tablas/film_text.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Film Text</p>
            </a>
          </li>
          <li class="nav-item" id="inventory">
            <a href="../tablas/inventory.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Inventory</p>
            </a>
          </li>
          <li class="nav-item noClient" id="language">
            <a href="../tablas/language.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Language</p>
            </a>
          </li>
          <li class="nav-item noClient" id="payment">
            <a href="../tablas/payment.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Payment</p>
            </a>
          </li>
          <li class="nav-item noClient" id="rental">
            <a href="../tablas/rental.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Rental</p>
            </a>
          </li>
          <li class="nav-item noClient" id="staff">
            <a href="../tablas/staff.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Staff</p>
            </a>
          </li>
          <li class="nav-item noClient" id="store">
            <a href="../tablas/store.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Store</p>
            </a>
          </li>

          <li class="nav-item ">
            <a class="nav-link active" onclick="logout()">
              <p>Cerrar sesion</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

<script>

  function logout() {
      localStorage.removeItem('user_data');
      localStorage.removeItem('email');
      document.cookie = "access_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC; SameSite=Lax";
      window.location.href = "/code.php";
  }

  function useComponent(roles) {
      const userData = JSON.parse(localStorage.getItem('user_data'));
      if (!userData || !userData.user || !userData.user.role) {
          return false;
      }
      const userRole = userData.user.role.role_name.toLowerCase();
      return roles.includes(userRole);
  }


  document.addEventListener("DOMContentLoaded", function() {
    if (useComponent(['client'])) {
        let elements = document.getElementsByClassName('noClient');
        Array.from(elements).forEach(el => {
            el.style.display = 'none';
        });
    }

    if (useComponent(['guest'])) {
        let elements = document.getElementsByClassName('noGuest');
        Array.from(elements).forEach(el => {
            el.style.display = 'none';
        });
    }
  });

</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const userData = JSON.parse(localStorage.getItem('user_data'));
    if (userData && userData.user) {
      const fullName = `${userData.user.first_name} ${userData.user.last_name} (${userData.user.role.role_name})`;
      document.getElementById('sidebarUsername').textContent = fullName;
    }
  });
</script>
<script>
function checkLocalStorageAndRedirect() {
    const userData = localStorage.getItem('user_data')  
    if (!userData) {
        window.location.href = "/code.php"
    }
}
checkLocalStorageAndRedirect();
</script>
