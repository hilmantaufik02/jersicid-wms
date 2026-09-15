<?php

namespace App\Enums;

enum Workspace: string
{
    case UTAMA = 'UTAMA';
    case GUDANG = 'GUDANG';
    case PACKING = 'PACKING';
    case PO = 'PO';
}

enum Status: string
{
    case AKTIF = 'AKTIF';
    case NONAKTIF = 'NONAKTIF';
}