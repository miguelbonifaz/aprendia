<?php

namespace App\Activities;

enum ActivityContentType: string
{
    case Text = 'text';
    case Image = 'image';
    case Audio = 'audio';
}
