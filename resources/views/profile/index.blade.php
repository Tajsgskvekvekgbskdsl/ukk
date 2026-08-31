@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.site')

@section('title', 'Profil Saya')

@section('content')
<header class="page-header">
    <div class="site-container">
        <h1><i class="bi bi-person-circle me-2" style="color:#16C94A;"></i>Profil Saya</h1>
        <p>Data akun &amp; keanggotaan perpustakaanmu.</p>
    </div>
</header>

<div class="site-container page-body">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card mb-4" style="box-shadow: var(--shadow-halus);">
                <div class="card-body p-4">
                    {{-- Avatar inisial + identitas singkat --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="stat-icon green mb-0" style="width:56px;height:56px;font-size:1.6rem;">
                            {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">{{ $user->nama_lengkap }}</h5>
                            <small class="text-muted">{{ $user->username }}</small>
                        </div>
                        @if(!$user->isAdmin())
                            <span class="badge-soft green ms-auto">Anggota</span>
                        @else
                            <span class="badge-soft blue ms-auto">Admin</span>
                        @endif
                    </div>

                    <dl class="row detail-info mb-0">
                        <dt class="col-sm-4">Nama Lengkap</dt>
                        <dd class="col-sm-8">{{ $user->nama_lengkap }}</dd>

                        <dt class="col-sm-4">Username</dt>
                        <dd class="col-sm-8">{{ $user->username }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>

                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8"><span class="badge-soft gray">{{ strtoupper($user->role) }}</span></dd>

                        @if($user->anggota)
                            <dt class="col-sm-4">NIS</dt>
                            <dd class="col-sm-8">{{ $user->anggota->nis ?? '-' }}</dd>

                            <dt class="col-sm-4">Kelas</dt>
                            <dd class="col-sm-8">{{ $user->anggota->kelas ?? '-' }}</dd>

                            <dt class="col-sm-4">Alamat</dt>
                            <dd class="col-sm-8">{{ $user->anggota->alamat ?? '-' }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('user.profile.edit') }}" class="btn btn-green"><i class="bi bi-pencil"></i> Edit Profil</a>
                <a href="{{ route('user.profile.password') }}" class="btn btn-outline-navy"><i class="bi bi-key"></i> Ganti Password</a>
            </div>
        </div>
    </div>
</div>
@endsection

