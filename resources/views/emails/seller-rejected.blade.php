<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembaruan status pendaftaran toko Anda.</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', sans-serif;
            background-color: #F2F3F5;
            padding: 40px 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            border-top: 3px solid #A435F0;
            padding: 40px 40px 20px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #A435F0;
            letter-spacing: -0.5px;
        }
        
        .content {
            padding: 40px;
        }
        
        .headline {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .body-text {
            font-size: 14px;
            color: #525560;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .store-name {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .reason-box {
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .reason-label {
            font-size: 12px;
            font-weight: 700;
            color: #DC2626;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .reason-text {
            font-size: 14px;
            color: #525560;
            line-height: 1.6;
        }
        
        .encouragement-text {
            font-size: 13px;
            color: #525560;
            line-height: 1.8;
            background-color: #F8F9FA;
            padding: 20px;
            border-radius: 6px;
            margin: 30px 0;
        }
        
        .button-group {
            margin: 30px 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 32px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background-color: #374151;
            color: #FFFFFF;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-primary:hover {
            background-color: #1F2937;
        }
        
        .footer-links {
            margin-top: 12px;
        }
        
        .footer-links a {
            color: #8A92A6;
            text-decoration: none;
            margin: 0 8px;
            font-size: 12px;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                border-radius: 0;
            }
            
            .content, .header, .footer {
                padding: 20px;
            }
            
            .headline {
                font-size: 20px;
            }
            
            .body-text {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">Catalozy</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h1 class="headline">Mohon Maaf, Pendaftaran Belum Disetujui</h1>
            
            <p class="body-text">
                Halo {{ $sellerName }},
            </p>
            
            <p class="body-text">
                Terima kasih telah mendaftar. Setelah peninjauan, saat ini kami belum dapat menyetujui pembukaan toko <span class="store-name">{{ $storeName }}</span> Anda.
            </p>
            
            <!-- Reason Box -->
            <div class="reason-box">
                <div class="reason-label">Alasan Penolakan:</div>
                <div class="reason-text">{{ $rejectionReason }}</div>
            </div>
            
            <!-- Encouragement -->
            <div class="encouragement-text">
                Jangan khawatir, Anda dapat memperbaiki data dan mendaftar ulang kapan saja. Data yang Anda gunakan untuk mendaftar sebelumnya sudah bisa digunakan kembali.
            </div>
            
            <!-- Button -->
            <div class="button-group">
                <a href="{{ $reapplyUrl }}" class="btn btn-primary">Perbaiki Pendaftaran</a>
            </div>
        </div>
        
        <!-- Divider -->
        <div style="height: 1px; background-color: #E8E8E8; margin: 0;"></div>
        
        <!-- Footer -->
        <div style="background-color: #F8F9FA; padding: 30px 40px; text-align: center; font-size: 12px; color: #8A92A6;">
            <div style="margin-bottom: 15px;">© 2025 Catalozy. Tembalang, Kota Semarang.</div>
            
            <div class="footer-links">
                <a href="{{ $helpUrl }}">Bantuan</a> | <a href="{{ $privacyUrl }}">Privasi</a>
            </div>
        </div>
    </div>
</body>
</html>
