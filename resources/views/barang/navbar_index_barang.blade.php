<div class="nav-align-top">
    <form action="{{ route('barang.index') }}" method="GET" class="d-flex me-2">
        <ul class="nav nav-pills flex-column flex-md-row">
            <li class="nav-item"><button type="submit" name="status" value="aktif" class="nav-link {{ request('status')==='aktif' ? 'active' : '' }}"><i class="bx bx-sm bx-checkbox-checked me-1_5"></i> Aktif</button></li>
            <li class="nav-item"><button type="submit" name="status" value="arsip" class="nav-link {{ request('status')==='arsip' ? 'active' : '' }}"><i class="bx bx-sm bx-checkbox me-1_5"></i> Arsip</button></li>
            <li class="nav-item"><button type="submit" name="stok_minimal" value="stok_minimal" class="nav-link {{ request()->has('stok_minimal') ? 'active' : '' }}"><i class="bx bx-sm bx-cart me-1_5"></i> Re-Stok</button></li>
        </ul>
    </form>
</div>