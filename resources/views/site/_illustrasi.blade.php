{{-- Ilustrasi perpustakaan: tumpukan buku + tanaman + elemen dekor sederhana.
     Dipakai di hero beranda dan panel kanan halaman login/register.
     Murni SVG flat tanpa gradient agar terlihat natural. --}}
<svg class="perpus-ilustrasi" viewBox="0 0 520 400" xmlns="http://www.w3.org/2000/svg"
    role="img" aria-label="Ilustrasi tumpukan buku dan tanaman di perpustakaan">

    {{-- dekor latar lembut --}}
    <circle cx="82" cy="96" r="16" fill="#FFE45C" opacity="0.85"/>
    <circle cx="444" cy="84" r="11" fill="#F7B8D0"/>
    <circle cx="470" cy="238" r="18" fill="none" stroke="#16C94A" stroke-width="3" opacity="0.45"/>
    <rect x="52" y="228" width="16" height="16" rx="4" fill="#A9C8F4" transform="rotate(-12 60 236)" opacity="0.9"/>
    <path d="M118 62 l7 14 14 2 -10 10 2 14 -13 -7 -13 7 2 -14 -10 -10 14 -2 z" fill="#F7B8D0" opacity="0.65"/>

    {{-- rak/meja --}}
    <rect x="58" y="332" width="404" height="12" rx="6" fill="#111827"/>
    <rect x="92" y="344" width="10" height="22" rx="4" fill="#111827" opacity="0.85"/>
    <rect x="418" y="344" width="10" height="22" rx="4" fill="#111827" opacity="0.85"/>

    {{-- tumpukan buku --}}
    <rect x="150" y="300" width="164" height="34" rx="7" fill="#16C94A"/>
    <rect x="166" y="308" width="12" height="18" rx="3" fill="#FFFFFF" opacity="0.6"/>
    <rect x="160" y="268" width="146" height="34" rx="7" fill="#F7B8D0"/>
    <rect x="286" y="274" width="12" height="22" rx="3" fill="#FFFFFF" opacity="0.75"/>
    <rect x="156" y="236" width="152" height="34" rx="7" fill="#111827"/>
    <rect x="170" y="244" width="12" height="18" rx="3" fill="#FFE45C"/>
    <g transform="rotate(-7 240 220)">
        <rect x="176" y="204" width="128" height="32" rx="7" fill="#FFE45C"/>
        <rect x="188" y="212" width="10" height="16" rx="3" fill="#111827" opacity="0.8"/>
    </g>

    {{-- buku berdiri bersandar --}}
    <g transform="rotate(9 350 296)">
        <rect x="336" y="230" width="34" height="104" rx="6" fill="#A9C8F4"/>
        <rect x="344" y="244" width="18" height="4" rx="2" fill="#FFFFFF" opacity="0.8"/>
        <rect x="344" y="254" width="12" height="4" rx="2" fill="#FFFFFF" opacity="0.6"/>
    </g>
    <rect x="368" y="238" width="30" height="96" rx="6" fill="#FFFFFF" stroke="#D8E4F8" stroke-width="2"/>
    <rect x="376" y="252" width="14" height="4" rx="2" fill="#16C94A"/>

    {{-- tanaman dalam pot --}}
    <path d="M256 330 C 252 300 234 292 220 290 C 232 310 240 322 252 332 Z" fill="#109E3B"/>
    <path d="M260 330 C 264 296 282 288 298 288 C 286 310 276 320 264 332 Z" fill="#16C94A"/>
    <path d="M258 330 C 258 304 258 286 258 270 C 250 284 246 300 250 318 Z" fill="#0C7A2D"/>
    <path d="M238 332 h44 l-6 40 a8 8 0 0 1 -8 7 h-16 a8 8 0 0 1 -8 -7 Z" fill="#FFFFFF" stroke="#D8E4F8" stroke-width="2"/>
    <rect x="233" y="326" width="54" height="12" rx="6" fill="#F7B8D0"/>

    {{-- buku terbuka mengambang kecil --}}
    <g transform="translate(388 148)">
        <path d="M0 14 C 12 4 26 4 36 12 L36 40 C 26 32 12 32 0 42 Z" fill="#FFFFFF" stroke="#111827" stroke-width="2.5"/>
        <path d="M72 14 C 60 4 46 4 36 12 L36 40 C 46 32 60 32 72 42 Z" fill="#FFFFFF" stroke="#111827" stroke-width="2.5"/>
        <line x1="36" y1="12" x2="36" y2="40" stroke="#111827" stroke-width="2.5"/>
    </g>

    {{-- titik-titik halus --}}
    <circle cx="120" cy="180" r="3.5" fill="#111827" opacity="0.25"/>
    <circle cx="140" cy="196" r="2.5" fill="#111827" opacity="0.2"/>
    <circle cx="430" cy="180" r="3.5" fill="#111827" opacity="0.25"/>
    <circle cx="412" cy="196" r="2.5" fill="#111827" opacity="0.2"/>
</svg>
