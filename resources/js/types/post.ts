export interface User {
    id: number;
    name: string;
    email: string;
    // Add other user properties as needed
}

export interface PostFormField {
    type: string;
    'col-span': string;
}

export interface PostFormFields {
    user: PostFormField;
    title: PostFormField;
    body: PostFormField;
    json: PostFormField;
    published: PostFormField;
    public: PostFormField;
    featured: PostFormField;
    locked: PostFormField;
    self: PostFormField;
    smart: PostFormField;
    created_at: PostFormField;
    updated_at: PostFormField;
}

export interface PostValidationRules {
    user_id: string;
    title: string;
    body: string;
    json: string;
    published: string;
    public: string;
    featured: string;
    locked: string;
    self: string;
    smart: string;
    created_at: string;
    updated_at: string;
}

export interface Page {
    title: string;
    // Add other page properties as needed
}

export interface BaseSection {
    // Define your section properties
}

export interface Post {
    id: number;
    user: User;
    title: string;
    body: string;
    created_at: string;
    updated_at: string;
    published: boolean;
    public: boolean;
    featured: boolean;
    locked: boolean;
    self: boolean;
    smart: boolean;
}

export interface PostProps {
    page: Page;
    baseSections: BaseSection[];
    posts: {
        data: Post[];
        meta: any;
    };
    formFields: PostFormFields;
    validationRules: PostValidationRules;
}
