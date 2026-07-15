export type ActivityDifficulty = 'easy' | 'medium' | 'hard';

export type ActivityMediaType = 'image' | 'audio';

export type ActivityTemplate =
    'recognize_and_select' | 'listen_read_and_respond' | 'match_with_lines';

export type ActivityContentType = 'text' | ActivityMediaType;

export type ActivityStudent = {
    id: number;
    name: string;
    birth_date: string;
    age: number;
};

export type ActivityItem<TData = Record<string, unknown>> = {
    id: string;
    data: TData;
};

export type ActivityMedia = {
    id: string;
    type: ActivityMediaType;
    source: string;
    alt_text?: string | null;
    transcript?: string | null;
};

export type ActivityResult = {
    score: number;
    summary: string;
    recommendations: string[];
};

export type ActivityDefinition<
    TData = Record<string, unknown>,
    TTemplate extends ActivityTemplate = ActivityTemplate,
> = {
    schema_version: 1;
    template: TTemplate;
    title: string;
    instructions: string;
    learning_objective: string;
    difficulty: ActivityDifficulty;
    student: ActivityStudent;
    items: ActivityItem<TData>[];
    media: ActivityMedia[];
    answer_key: Record<string, string[]>;
    hints: Record<string, string[]>;
    result: ActivityResult | null;
};

export type ActivityContent =
    | { type: 'text'; text: string; media_id?: never }
    | { type: ActivityMediaType; media_id: string; text?: never };

export type ActivityChoiceOption = {
    id: string;
    content: ActivityContent;
};

export type ActivityAnswerFeedback = {
    item_id: string;
    selected_option_id: string;
    is_correct: boolean;
    message: string;
    hint: string | null;
};

export type RecognizeAndSelectContent = ActivityContent;

export type RecognizeAndSelectOption = ActivityChoiceOption;

export type RecognizeAndSelectItemData = {
    prompt: RecognizeAndSelectContent;
    illustration_media_id?: string;
    spoken_word?: string;
    options: RecognizeAndSelectOption[];
    feedback: {
        correct: string;
        incorrect: string;
    };
};

export type RecognizeAndSelectActivityDefinition = ActivityDefinition<
    RecognizeAndSelectItemData,
    'recognize_and_select'
>;

export type RecognizeAndSelectFeedback = ActivityAnswerFeedback;

export type ListenReadAndRespondItemData = {
    stimulus: ActivityContent;
    question: string;
    options: ActivityChoiceOption[];
    feedback: {
        correct: string;
        incorrect: string;
    };
};

export type ListenReadAndRespondActivityDefinition = ActivityDefinition<
    ListenReadAndRespondItemData,
    'listen_read_and_respond'
>;

export type ListenReadAndRespondFeedback = ActivityAnswerFeedback;

export type MatchWithLinesItemData = {
    left: ActivityContent;
    right: ActivityChoiceOption;
    feedback: {
        correct: string;
        incorrect: string;
    };
};

export type MatchWithLinesActivityDefinition = ActivityDefinition<
    MatchWithLinesItemData,
    'match_with_lines'
>;

export type MatchWithLinesAnswers = Record<string, string>;

export type MatchWithLinesFeedback = {
    left_item_id: string;
    right_item_id: string;
    is_correct: boolean;
    message: string;
    hint: string | null;
};
