export interface EventInterface {
    id:               number;
    title:            string;
    description:      string;
    event_date:       Date;
    location:         string;
    max_participants: number;
    isPublic:         boolean;
    isVerified:       boolean;
    categories:       Category[];
    participants:     Participant[];
}

export interface Category {
    id:   number;
    name: string;
    img:  string;
}

export interface Participant {
    id:   number;
    name: string;
}
