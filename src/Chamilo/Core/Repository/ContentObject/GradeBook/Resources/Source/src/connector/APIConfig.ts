export default interface APIConfig {
    readonly loadGradeBookDataURL: string;
    readonly addCategoryURL: string;
    readonly updateCategoryURL: string;
    readonly moveCategoryURL: string;
    readonly addColumnURL: string;
    readonly updateColumnURL: string;
    readonly updateColumnCategoryURL: string;
    readonly moveColumnURL: string;
    readonly csrfToken: string;
}