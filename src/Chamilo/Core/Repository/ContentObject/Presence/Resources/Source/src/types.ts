export type PresenceStatusDefault = {
  id: number; type: string; title: string; aliasses: number|undefined;
};

export type PresenceStatus = {
  id: number; type: string; title: string|undefined; code: string; color: string; aliasses: number|undefined;
};

export type Presence = {
  id: number; title: string; statuses: PresenceStatus[]; has_checkout: boolean; verification_icon_data: any|null; global_self_registration_disabled: boolean;
};

export type PresencePeriod = {
  id: number; date: number; label: string; period_self_registration_disabled: boolean;
};
