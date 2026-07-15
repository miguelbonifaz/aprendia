<?php

namespace App\Activities\Agent;

use App\Models\Student;

interface ActivityAgent
{
    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $messages
     */
    public function respond(Student $student, array $messages): string;
}
