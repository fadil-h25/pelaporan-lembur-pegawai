<?php

namespace App;

enum UserRole: string
{
    case ADMIN = 'admin';

    case OPERATOR = 'operator';

    case PEGAWAI = 'pegawai';
}
