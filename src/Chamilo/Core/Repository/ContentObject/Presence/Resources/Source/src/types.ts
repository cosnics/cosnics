export type PresenceStatusDefault = {
  id: number; type: string; title: string; aliasses: number|undefined;
};

export type PresenceStatus = {
  id: number; type: string; title: string|undefined; code: string; color: string; aliasses: number|undefined;
};

export type Presence = {
  id: number; title: string; statuses: PresenceStatus[];
};

export type PresencePeriod = {
  id: number; date: number; label: string;
};
