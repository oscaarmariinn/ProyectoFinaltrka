export interface GroupInterface {
    id:           number;
    name:         string;
    description:  string;
    created_at:   Date;
    is_private:   boolean;
    responsibles: User[];
    users:        User[];
}

export interface User {
    id:   number;
    name: string;
}
