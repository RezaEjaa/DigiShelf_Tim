<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Peminjaman - Digishelf</title>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --wood-dark: #4E342E;
            --wood-medium: #795548;
            --paper: #FFF8E1;
            --ink: #2F1D18;
            --muted: #7A625A;
            --green: #2E7D32;
            --orange: #E65100;
            --red: #C62828;
            --violet: #4527A0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: var(--ink);
            background:
                linear-gradient(rgba(47, 29, 24, 0.68), rgba(47, 29, 24, 0.72)),
                url("https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1600&q=80") center/cover fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .scan-shell {
            width: min(100%, 560px);
            text-align: center;
        }

        .brand {
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.92rem;
            margin-bottom: 18px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .brand i { color: #FFE082; }

        .code-panel {
            background: rgba(255, 252, 244, 0.96);
            border: 1px solid rgba(255, 224, 130, 0.65);
            border-radius: 22px;
            padding: 34px 30px;
            box-shadow: 0 28px 80px rgba(0,0,0,0.34);
            backdrop-filter: blur(8px);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--paper);
            color: var(--wood-medium);
            font-size: 0.76rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        h1 {
            font-family: 'Crimson Pro', serif;
            font-size: clamp(1.9rem, 7vw, 3.25rem);
            line-height: 1.02;
            margin-bottom: 10px;
            color: var(--wood-dark);
        }

        .subtitle {
            color: var(--muted);
            font-size: clamp(0.86rem, 3vw, 0.98rem);
            line-height: 1.7;
            margin-bottom: 26px;
        }

        .code-box {
            background: #fff;
            border: 2px dashed #BCAAA4;
            border-radius: 16px;
            padding: 22px 14px;
            margin-bottom: 18px;
        }

        .code-label {
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .code-value {
            font-family: 'Courier New', monospace;
            font-size: clamp(1.75rem, 9vw, 3.15rem);
            font-weight: 800;
            color: var(--wood-dark);
            letter-spacing: clamp(1px, 1vw, 5px);
            overflow-wrap: anywhere;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }

        .meta-item {
            background: #FAF7F2;
            border-radius: 12px;
            padding: 12px;
            text-align: left;
        }

        .meta-item span {
            display: block;
            color: var(--muted);
            font-size: 0.72rem;
            margin-bottom: 4px;
        }

        .meta-item strong {
            display: block;
            color: var(--ink);
            font-size: 0.88rem;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: 18px;
        }

        .status.pending { background: #FFF3E0; color: var(--orange); }
        .status.active { background: #E8F5E9; color: var(--green); }
        .status.returned { background: #EDE7F6; color: var(--violet); }
        .status.cancelled { background: #FFEBEE; color: var(--red); }

        .copy-btn {
            width: 100%;
            border: 0;
            border-radius: 13px;
            padding: 13px 16px;
            margin-top: 18px;
            background: linear-gradient(135deg, var(--wood-medium), var(--wood-dark));
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .copy-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(78, 52, 46, 0.28);
        }

        .footer-note {
            color: rgba(255,255,255,0.82);
            font-size: 0.78rem;
            margin-top: 16px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.35);
        }

        @media (max-width: 520px) {
            body { padding: 18px; align-items: stretch; }
            .scan-shell { display: flex; flex-direction: column; justify-content: center; }
            .code-panel { border-radius: 18px; padding: 26px 18px; }
            .meta-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @php
        $req = $borrowingRequest;
        $statusIcon = match($req->status) {
            'pending' => 'fa-hourglass-half',
            'active' => 'fa-check-circle',
            'returned' => 'fa-rotate-left',
            'cancelled' => 'fa-circle-xmark',
            default => 'fa-circle-info',
        };
    @endphp

    <main class="scan-shell">
        <div class="brand"><i class="fas fa-book-open"></i> Digishelf</div>

        <section class="code-panel" aria-label="Kode peminjaman">
            <div class="eyebrow"><i class="fas fa-qrcode"></i> Hasil Scan QR</div>
            <h1>Kode Peminjaman</h1>
            <p class="subtitle">Tunjukkan kode ini kepada petugas perpustakaan untuk verifikasi peminjaman atau pengembalian.</p>

            <div class="code-box">
                <div class="code-label">Kode</div>
                <div class="code-value" id="borrowCode">{{ $req->qr_code }}</div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Peminjam</span>
                    <strong>{{ $req->user->name }}</strong>
                </div>
                <div class="meta-item">
                    <span>Status</span>
                    <strong>{{ $req->statusLabel() }}</strong>
                </div>
                <div class="meta-item">
                    <span>Pengambilan</span>
                    <strong>{{ $req->pickup_date->format('d M Y') }}</strong>
                </div>
                <div class="meta-item">
                    <span>Pengembalian</span>
                    <strong>{{ $req->return_date->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="status {{ $req->status }}">
                <i class="fas {{ $statusIcon }}"></i> {{ $req->statusLabel() }}
            </div>

            <button type="button" class="copy-btn" id="copyBtn">
                <i class="fas fa-copy"></i> Salin Kode
            </button>
        </section>

        <p class="footer-note">Halaman ini dibuat otomatis dari QR Code peminjaman.</p>
    </main>

    <script>
        const copyBtn = document.getElementById('copyBtn');
        const borrowCode = document.getElementById('borrowCode');

        copyBtn?.addEventListener('click', async () => {
            const code = borrowCode.textContent.trim();
            try {
                await navigator.clipboard.writeText(code);
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Kode Disalin';
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fas fa-copy"></i> Salin Kode';
                }, 1600);
            } catch (e) {
                window.prompt('Salin kode peminjaman:', code);
            }
        });
    </script>
</body>
</html>
