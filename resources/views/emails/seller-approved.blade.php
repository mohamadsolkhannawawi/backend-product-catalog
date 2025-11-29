<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat! Toko Anda telah aktif.</title>
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
            margin-bottom: 30px;
        }
        
        .store-name {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .step-list {
            margin: 30px 0;
        }
        
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #F8F9FA;
            border-radius: 6px;
            border-left: 3px solid #A435F0;
        }
        
        .step-number {
            width: 32px;
            height: 32px;
            background-color: #A435F0;
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 16px;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        
        .step-description {
            font-size: 12px;
            color: #525560;
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
            background-color: #A435F0;
            color: #FFFFFF;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-primary:hover {
            background-color: #8B2DD4;
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
            
            .step-item {
                padding: 16px;
            }
            
            .step-number {
                width: 28px;
                height: 28px;
                font-size: 12px;
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
            <h1 class="headline">Selamat Bergabung di Catalozy!</h1>
            
            <p class="body-text">
                Halo {{ $sellerName }},
            </p>
            
            <p class="body-text">
                Kabar baik! Pendaftaran toko <span class="store-name">{{ $storeName }}</span> telah disetujui oleh tim kami. Sekarang Anda dapat mulai mengelola stok dan berjualan ke ribuan pelanggan.
            </p>
            
            <!-- Next Steps -->
            <div class="step-list">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Tambahkan Produk Pertama Anda</div>
                        <div class="step-description">Mulai unggah produk lokal berkualitas Anda ke platform Catalozy.</div>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Pastikan Produk yang Anda Tambahkan Lengkap</div>
                        <div class="step-description">Periksa detail produk seperti deskripsi, harga, dan stok agar pelanggan mendapatkan informasi yang jelas.</div>
                    </div>
                </div>
            </div>
            
            <!-- Button -->
            <div class="button-group">
                <a href="{{ $activateUrl }}" class="btn btn-primary">Masuk ke Dashboard Penjual</a>
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
