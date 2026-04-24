<?php
$h = password_hash('admin', PASSWORD_DEFAULT);
file_put_contents('hash.txt', $h);
