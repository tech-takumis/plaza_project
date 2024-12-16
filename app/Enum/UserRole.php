<?php

namespace App\Enum;

enum UserRole: string
{
    case Captain = 'Kapitan';
    case Kagawad = 'Kagawad';
    case Chairperson = 'Chairperson';
    case Secretary = 'Secretary';
    case Treasure = 'Treasure';

}
