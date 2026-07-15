<?php

namespace App\Activities;

enum ActivityTemplate: string
{
    case RecognizeAndSelect = 'recognize_and_select';
    case ListenReadAndRespond = 'listen_read_and_respond';
    case MatchWithLines = 'match_with_lines';
}
