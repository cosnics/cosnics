export default class APIConfig {
    public readonly loadPresenceEntriesURL = '';
    public readonly loadPresenceURL = '';
    public readonly updatePresenceURL = '';
    public readonly savePresenceEntryURL = '';
    public readonly bulkSavePresenceEntriesURL = '';
    public readonly createPresencePeriodURL = '';
    public readonly updatePresencePeriodURL = '';
    public readonly deletePresencePeriodURL = '';
    public readonly loadRegisteredPresenceEntryStatusesURL = '';
    public readonly togglePresenceEntryCheckoutURL = '';
    public readonly exportURL = '';
    public readonly csrfToken = '';

    constructor(config: Record<string, string>) {
        Object.assign(this, config);
    }

    public static from(config: Record<string, string>) : APIConfig {
        return new APIConfig(config);
    }
}
