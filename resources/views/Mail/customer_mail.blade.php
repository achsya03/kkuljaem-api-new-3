Dear {{ $info_pengguna['nama'] }}, <br/>
@if($stat=="verify")
    Terima kasih telah mendaftarkan diri di Kkuljaem Korean App.<br/><br/>
    Silakan klik link di bawah untuk memverifikasi alamat email Anda.<br/>
    {{ $info_pengguna['url'] }} <br/><br/>
    Regards,<br/>
    Kkuljaem Korean  <br/><br/>
@elseif($stat=="forgot-pass")
    Kami telah menerima permintaan untuk mengubah kata sandi Anda. <br/><br/>
    Silakan klik link di bawah untuk mengganti kata sandi Anda.<br/>
    {{ $info_pengguna['url'] }} <br/><br/>
    Regards,<br/>
    Kkuljaem Korean  <br/><br/>
@endif
Thanks,<br/>
Kkuljaem Operator<br/>