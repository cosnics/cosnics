export default interface APIConfig {
    readonly gradeBookRootURL: string;
    readonly gradeBookImportCsvURL: string;
    readonly loadGradeBookDataURL: string;
    readonly addCategoryURL: string;
    readonly updateCategoryURL: string;
    readonly moveCategoryURL: string;
    readonly removeCategoryURL: string;
    readonly addColumnURL: string;
    readonly updateColumnURL: string;
    readonly updateColumnCategoryURL: string;
    readonly addColumnSubItemURL: string;
    readonly removeColumnSubItemURL: string;
    readonly moveColumnURL: string;
    readonly removeColumnURL: string;
    readonly synchronizeGradeBookURL: string;
    readonly overwriteScoreURL: string;
    readonly revertOverwrittenScoreURL: string;
    readonly updateScoreCommentURL: string;
    readonly calculateTotalScoresURL: string;
    readonly csrfToken: string;
}