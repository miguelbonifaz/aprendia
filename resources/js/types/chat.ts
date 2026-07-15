export type ChatActivity = {
    public_id: string;
    title: string;
    url: string;
};

export type ChatMessage = {
    role: 'user' | 'assistant';
    content: string;
    activity?: ChatActivity;
};
