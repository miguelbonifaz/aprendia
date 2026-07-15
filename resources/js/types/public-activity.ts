import type { RecognizeAndSelectFeedback } from './activity';

export type PlayableActivityOption = {
    id: string;
    text: string;
};

export type PlayableActivityItem = {
    id: string;
    prompt: string;
    options: PlayableActivityOption[];
};

export type PlayableActivity = {
    public_id: string;
    student_name: string;
    title: string;
    instructions: string;
    learning_objective: string;
    items: PlayableActivityItem[];
};

export type PlayableActivityResult = {
    score: number;
    summary: string;
};

export type ActivityAnswerResponse = {
    feedback: RecognizeAndSelectFeedback;
};

export type ActivityResultResponse = {
    result: PlayableActivityResult;
};
