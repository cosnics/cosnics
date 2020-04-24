export default class APIConfiguration {
    public readonly addLevelURL = '';
    public readonly addTreeNodeURL = '';
    public readonly deleteLevelURL = '';
    public readonly deleteTreeNodeURL = '';
    public readonly moveLevelURL = '';
    public readonly moveTreeNodeURL = '';
    public readonly updateChoiceURL = '';
    public readonly updateLevelURL = '';
    public readonly updateTreeNodeURL = '';

    private constructor(config: Object) {
        Object.assign(this, config);
    }

    public static fromJSON(config: Object) : APIConfiguration {
        return new APIConfiguration(config);
    }
};
