export default interface APIConfig {
    readonly loadGradeBookDataURL: string;
    readonly addCategoryURL: string;
    readonly updateCategoryURL: string;
    readonly moveCategoryURL: string;
    readonly csrfToken: string;
}