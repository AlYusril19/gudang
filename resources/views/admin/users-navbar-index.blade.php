<div class="nav-align-top">
    <form action="{{ route('users.index') }}" method="GET" class="d-flex me-2">
        <ul class="nav nav-pills flex-column flex-md-row">
            <li class="nav-item">
                <button type="submit" name="status" value="aktif" class="nav-link {{ request('status')!=='arsip' && !request()->has('role') ? 'active' : '' }}">
                    <i class="bx bxs-user me-1_5"></i> Staff
                </button>
            </li>
            <li class="nav-item">
                <button type="submit" name="role" value="mitra" class="nav-link {{ request('role')==='mitra' ? 'active' : '' }}">
                    <i class="bx bxs-group me-1_5"></i> Mitra
                </button>
            </li>
            <li class="nav-item">
                <button type="submit" name="role" value="magang" class="nav-link {{ request('role')==='magang' ? 'active' : '' }}">
                    <i class="bx bxs-graduation me-1_5"></i> Magang
                </button>
            </li>
            <li class="nav-item">
                <button type="submit" name="status" value="arsip" class="nav-link {{ request('status')==='arsip' ? 'active' : '' }}">
                    <i class="bx bxs-archive me-1_5"></i> Arsip
                </button>
            </li>
        </ul>
    </form>
</div>