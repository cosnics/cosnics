export default class APIConfig {
    public readonly loadPresenceEntriesURL = '';
    public readonly loadPresenceURL = '';
    public readonly updatePresenceURL = '';
    public readonly savePresenceEntryURL = '';
    public readonly createPresencePeriodURL = '';
    public readonly updatePresencePeriodURL = '';

    constructor(config: Record<string, string>) {
        Object.assign(this, config);
    }

    public static from(config: Record<string, string>) : APIConfig {
        return new APIConfig(config);
    }
}