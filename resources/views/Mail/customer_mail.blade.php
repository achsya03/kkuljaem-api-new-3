Dear {{ $info_pengguna['nama'] }}, <br/>
@if($stat=="verify")
    Thanks for register in Kkuljaem <br/><br/>
    Please click link below to activate your account <br/>
    {{ $info_pengguna['url'] }} <br/>
    We waiting for you to join us in Kkuljaem Class.<br/><br/>
@elseif($stat=="forgot-pass")
    We heard that you lost your Kkuljaem password. Sorry about that!<br/><br/>
    But don’t worry! You can use the following link to reset your password: <br/>
    {{ $info_pengguna['url'] }} <br/>
    We waiting for you to join us in Kkuljaem Class.<br/><br/>
@endif
Thanks,<br/>
Kkuljaem Operator<br/>