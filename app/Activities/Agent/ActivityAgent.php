<?php

namespace App\Activities\Agent;

use App\Activities\ActivityDefinition;
use App\Models\Student;

interface ActivityAgent
{
    /**
     * @param  list<array{role: 'user'|'assistant', content: string}>  $messages
     */
    public function generate(Student $student, array $messages): ActivityDefinition;
}
