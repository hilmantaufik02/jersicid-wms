<?php
namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'OWNER';
    case SUPERVISOR = 'SUPERVISOR';
    case STAFF_GUDANG = 'STAFF_GUDANG';
    case SUBLIM = 'SUBLIM';
    case KONVEKSI = 'KONVEKSI';
}