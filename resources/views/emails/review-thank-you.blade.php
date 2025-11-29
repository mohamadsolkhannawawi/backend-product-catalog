<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima kasih atas ulasan Anda!</title>
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
            text-align: center;
        }
        
        .product-name {
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .review-snippet {
            background-color: #F8F9FA;
            border-left: 3px solid #A435F0;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        
        .stars {
            font-size: 14px;
            color: #FFB800;
            margin-bottom: 12px;
            letter-spacing: 2px;
        }
        
        .review-text {
            font-size: 13px;
            color: #525560;
            font-style: italic;
            line-height: 1.6;
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
            margin-bottom: 12px;
            display: block;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-primary:hover {
            background-color: #8B2DD4;
        }
        
        .secondary-link {
            color: #A435F0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .secondary-link:hover {
            text-decoration: underline;
        }
        
        .divider {
            height: 1px;
            background-color: #E8E8E8;
            margin: 30px 0;
        }
        
        .footer {
            background-color: #F8F9FA;
            padding: 30px 40px;
            text-align: center;
            font-size: 12px;
            color: #8A92A6;
        }
        
        .footer-text {
            margin-bottom: 15px;
        }
        
        .social-icons {
            margin: 15px 0;
        }
        
        .social-icon {
            display: inline-block;
            width: 32px;
            height: 32px;
            margin: 0 8px;
            background-color: #E8E8E8;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-size: 14px;
            color: #8A92A6;
            text-decoration: none;
        }
        
        .social-icon:hover {
            background-color: #A435F0;
            color: #FFFFFF;
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
            <h1 class="headline">Terima Kasih, {{ $reviewerName }}!</h1>
            
            <p class="body-text">
                Ulasan Anda untuk produk <span class="product-name">{{ $productName }}</span> telah berhasil ditayangkan. Pendapat Anda sangat membantu pembeli lain dalam menemukan produk lokal terbaik.
            </p>
            
            <!-- Review Snippet -->
            <div class="review-snippet">
                <div class="stars">★★★★★</div>
                <div class="review-text">"{{ $reviewText }}"</div>
            </div>
            
            <!-- Button Group -->
            <div class="button-group">
                <a href="{{ $reviewUrl }}" class="btn btn-primary">Lihat Produk</a>
                <div style="text-align: center; margin-top: 12px;">
                    <a href="{{ $shopUrl }}" class="secondary-link">Kembali Belanja</a>
                </div>
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
