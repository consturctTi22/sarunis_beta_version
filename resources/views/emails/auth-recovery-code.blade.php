<!DOCTYPE html>
<html lang="id">
    <body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
        <h1 style="font-size: 20px; margin-bottom: 8px;">Atur Ulang Kata Sandi Sarunis</h1>
        <p>Halo, {{ $user->name }}.</p>
        <p>Anda menerima email ini karena kami menerima permintaan atur ulang kata sandi untuk akun Anda.</p>
        <p>Silakan klik tautan di bawah ini untuk mengatur ulang kata sandi Anda:</p>
        <div style="margin: 20px 0;">
            <a href="{{ $resetUrl }}" style="background-color: #2563eb; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Atur Ulang Kata Sandi</a>
        </div>
        <p>Tautan ini hanya berlaku selama 15 menit. Jika Anda tidak meminta pengaturan ulang kata sandi, tidak ada tindakan lebih lanjut yang diperlukan.</p>
    </body>
</html>
