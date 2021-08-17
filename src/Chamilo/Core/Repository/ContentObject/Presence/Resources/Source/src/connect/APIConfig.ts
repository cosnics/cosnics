export default class APIConfig {
    public readonly loadStudentsURL = '';
    public readonly loadStatusDefaultsURL = '';
    public readonly loadPresencesURL = '';
    public readonly updatePresencesURL = '';

    constructor(config: Record<string, string>) {
        Object.assign(this, config);
    }

    public static from(config: Record<string, string>) : APIConfig {
        return new APIConfig(config);
    }
}
