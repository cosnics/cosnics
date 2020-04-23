export default class APIConfiguration {
    public addLevelURL : string = '';
    public addTreeNodeURL: string = '';
    public deleteLevelURL: string = '';
    public deleteTreeNodeURL: string = '';
    public moveLevelURL: string = '';
    public moveTreeNodeURL: string = '';
    public updateChoiceURL: string = '';
    public updateLevelURL: string = '';
    public updateTreeNodeURL: string = '';

    public static fromJSON(config: any): APIConfiguration {
        let apiConfiguration = new APIConfiguration();
        apiConfiguration.addLevelURL = config.addLevelURL;
        apiConfiguration.addTreeNodeURL = config.addTreeNodeURL;
        apiConfiguration.deleteLevelURL = config.deleteLevelURL;
        apiConfiguration.deleteTreeNodeURL = config.deleteTreeNodeURL;
        apiConfiguration.moveLevelURL = config.moveLevelURL;
        apiConfiguration.moveTreeNodeURL = config.moveTreeNodeURL;
        apiConfiguration.updateChoiceURL = config.updateChoiceURL;
        apiConfiguration.updateLevelURL = config.updateLevelURL;
        apiConfiguration.updateTreeNodeURL = config.updateTreeNodeURL;

        return apiConfiguration;
    }
}
