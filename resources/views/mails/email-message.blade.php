<div style="max-width: 400px; margin: 0 auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 3px; border-radius: 20px;">
    <div style="background: white; padding: 40px 30px; border-radius: 18px; text-align: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        
        <!-- Icon Lock -->
        <div style="width: 70px; height: 70px; background: #f0f2ff; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 35px; height: 35px; fill: #667eea;" viewBox="0 0 24 24">
                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
            </svg>
        </div>
        
        <!-- Title -->
        <h2 style="color: #333; margin-bottom: 15px; font-size: 24px; font-weight: 600;">Kode Verifikasi</h2>
        
        <!-- Message -->
        <p style="color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 25px;">
            Gunakan kode OTP berikut untuk melanjutkan proses verifikasi Anda:
        </p>
        
        <!-- OTP Code -->
        <div style="background: #f8f9ff; border: 2px dashed #667eea; padding: 20px; border-radius: 15px; margin-bottom: 25px;">
            <h3 style="color: #667eea; font-size: 36px; letter-spacing: 8px; font-weight: 700; margin: 0;">{{ $mailMessage }}</h3>
        </div>
        
        <!-- Warning -->
        <p style="color: #999; font-size: 13px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
            ⚠️ Jangan berikan kode ini kepada siapa pun, termasuk pihak yang mengaku dari kami.
            Kode akan kadaluarsa dalam 5 menit.
        </p>
    </div>
</div>