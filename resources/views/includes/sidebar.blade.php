<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">
    @can('aksesBPRS')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('insert-peserta') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Insert Peserta</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('list-peserta-data-diterima') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Insert dok Peserta</span>
            </a>
        </li>
    @endcan

    @can('aksesAdmin')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('list-peserta-pending') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>List Peserta Pending</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('list-peserta-upload-dokumen') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>List Peserta upload dok</span></a>
        </li>
        </li>
    @endcan
    <li class="nav-item">
        <a class="nav-link" href="{{ url('list-peserta-terima-data-dokumen') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>List Peserta Diterima</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('list-peserta-data-ditolak') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>List Peserta Tolak</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('list-peserta-dokumen-ditolak') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>List Peserta Dok Ditolak</span></a>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-white"></i>
                Logout
            </button>
        </form>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
</ul>
