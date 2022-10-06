import GradeBook from '@/domain/GradeBook';

function createGradeBookObject() {
    return {
        gradeItems: [
            { id: 1, title: 'Opdracht 1', breadcrumb: ['Leerpaden', 'Categorie 1', 'Leerpad 2', 'Hoofdstuk 1'] },
            { id: 2, title: 'Opdracht 2', breadcrumb: ['Opdrachten', 'Categorie 1'] },
            { id: 3, title: 'Opdracht 3', breadcrumb: ['Opdrachten' , 'Categorie 2'] },
            { id: 4, title: 'Oefening 1', breadcrumb: ['Oefeningen'] },
            { id: 5, title: 'Oefening 2', breadcrumb: ['Leerpaden', 'Leerpad 5', 'Hoofdstuk 4'] },
            { id: 6, title: 'Evaluatie 1', breadcrumb: ['Evaluaties'] },
            { id: 7, title: 'Evaluatie 2', breadcrumb: ['Leerpaden', 'Leerpad 3', 'Hoofdstuk 2'] }
        ],
        gradeColumns: [
            { id: 1, type: 'group', title: 'Groepsscore', subItemIds: [1, 3], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 2, type: 'item', title: null, subItemIds: [2], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 3, type: 'item', title: null, subItemIds: [4], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 4, type: 'item', title: null, subItemIds: [5], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 5, type: 'item', title: 'Mondeling examen', subItemIds: [6], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 }
        ],
        categories: [
            { id: 1, color: '#caf1eb', title: 'Categorie 1', columnIds: [1, 2, 3] },
            { id: 2, color: '#ebf2e8', title: 'Categorie 2', columnIds: [4, 5] }
        ],
        nullCategory: { id: 0, color: 'none', title: '', columnIds: [] },
        resultsData: [
            { id: 1, 'student': 'Student 1', 'results': [{id: 1, value: null}, {id: 3, value: 20}, {id: 2, value: 60}, {id: 4, value: 80}, {id: 5, value: 50}, {id: 6, value: 75}, {id: 7, value: 50}] },
            { id: 2, 'student': 'Student 2', 'results': [{id: 1, value: 30}, {id: 3, value: null}, {id: 2, value: 50}, {id: 4, value: 40}, {id: 5, value: 80}, {id: 6, value: 65}, {id: 7, value: 50}] },
            { id: 3, 'student': 'Student 3', 'results': [{id: 1, value: null}, {id: 3, value: 50}, {id: 2, value: 30}, {id: 4, value: 70}, {id: 5, value: 80}, {id: 6, value: 95}, {id: 7, value: 50}] },
            { id: 4, 'student': 'Student 4', 'results': [{id: 1, value: 80}, {id: 3, value: null}, {id: 2, value: 40}, {id: 4, value: 40}, {id: 5, value: 30}, {id: 6, value: 75}, {id: 7, value: 50}] },
            { id: 5, 'student': 'Student 5', 'results': [{id: 1, value: null}, {id: 3, value: 60}, {id: 2, value: 10}, {id: 4, value: 90}, {id: 5, value: 40}, {id: 6, value: 25}, {id: 7, value: 50}] }
        ]
    };
}

let gradeBook: GradeBook;

beforeEach(() => {
    gradeBook = GradeBook.from(createGradeBookObject());
});

test('initial', () => {
    expect(gradeBook.gradeColumns.length).toEqual(5);
    expect(gradeBook.allCategories.length).toEqual(3);
});

/*test('endResult', () => {
    expect(gradeBook.getEndResult(1)).toBeCloseTo(57);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(65);
});*/

/*test('endResultWithUpdatedWeight', () => {
    gradeBook.setWeight(1, 60);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(38.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(57.5);
});*/

/*test('endResultDontCountColumn', () => {
    gradeBook.getGradeColumn(4)!.countForEndResult = false;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(58.75);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(61.25);

    gradeBook.setWeight(1, 70);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(35.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(54.5);
});*/

/*test('endResultHandleAbsence', () => {
    let student = gradeBook.resultsData.find(d => d.id === 1);

    // test authorized presence
    let results = student!.results.find(r => r.id === 2);
    results!.value = 'gafw';
    results!.overwritten = true;

    // don't count score
    expect(gradeBook.getEndResult(1)).toBeCloseTo(56.25);

    // max score
    gradeBook.getGradeColumn(2)!.authPresenceEndResult = GradeBook.MAX_SCORE;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(65);

    // min score
    gradeBook.getGradeColumn(2)!.authPresenceEndResult = GradeBook.MIN_SCORE;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(45);

    // test unauthorized presence
    student = gradeBook.resultsData.find(d => d.id === 3);
    results = student!.results.find(r => r.id === 3);
    results!.value = 'afw';
    results!.overwritten = true;

    // min score
    expect(gradeBook.getEndResult(3)).toBeCloseTo(51);

    // min score
    gradeBook.getGradeColumn(3)!.unauthPresenceEndResult = GradeBook.MAX_SCORE;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(71);

    // don't count score
    gradeBook.getGradeColumn(3)!.unauthPresenceEndResult = GradeBook.NO_SCORE;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(63.75);
});*/

/*test('addScore', () => {
    gradeBook.addGradeColumnFromItem(gradeBook.getGradeItem(7)!);
    expect(gradeBook.gradeColumns.length).toEqual(6);
    expect(gradeBook.gradeColumns[5].type).toEqual('item');
    expect(gradeBook.getEndResult(1)).toBeCloseTo(55.83);
});*/

/*test('removeScore', () => {
    gradeBook.removeColumn(gradeBook.getGradeColumn(3)!);
    expect(gradeBook.gradeColumns.length).toEqual(4);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(51.25);
});*/

test('addGroupScore', () => {
    gradeBook.setWeight(4, 50);
    const column = gradeBook.getGradeColumn(3);
    column!.countForEndResult = false;
    column!.authPresenceEndResult = GradeBook.MAX_SCORE;
    column!.unauthPresenceEndResult = GradeBook.MAX_SCORE;
    gradeBook.addSubItem(gradeBook.getGradeItem(5)!, 3);
    expect(gradeBook.gradeColumns.length).toEqual(4);
    //column = gradeBook.gradeColumns[3];
    expect(column!.id).toEqual(3);
    expect(column!.type).toEqual('group');
    expect(column!.title).toEqual('Oefening 1');
    expect(column!.subItemIds).toEqual([4, 5]);
    expect(column!.weight).toEqual(null); // should change so it stays 50?
    expect(column!.countForEndResult).toEqual(false);
    expect(column!.authPresenceEndResult).toEqual(GradeBook.MAX_SCORE);
    expect(column!.unauthPresenceEndResult).toEqual(GradeBook.MAX_SCORE);
});

/*test('removeFromGroupScore', () => {
    const student1 = gradeBook.resultsData.find(d => d.id === 1);
    const student2 = gradeBook.resultsData.find(d => d.id === 2);
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(2);
    let result = student1!.results.find(r => r.id === 1);
    expect(result!.value).toEqual(20);
    gradeBook.removeSubItem(gradeBook.getGradeItem(3)!);
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(1);
    result = student1!.results.find(r => r.id === 1);
    expect(result!.value).toEqual(null);
    result = student2!.results.find(r => r.id === 1);
    expect(result!.value).toEqual(30);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(53);
    gradeBook.removeSubItem(gradeBook.getGradeItem(1)!);
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(0);
    result = student2!.results.find(r => r.id === 1);
    expect(result!.value).toEqual(null);
});*/

/*test('addNewScore', () => {
    gradeBook.createNewScore();
    expect(gradeBook.nullCategory.columnIds).toEqual(['sc1']);
    expect(gradeBook.gradeColumns.length).toEqual(6);
    const column = gradeBook.gradeColumns[5];
    expect(column.id).toEqual('sc1');
    expect(column.title).toEqual('Score');
    expect(column.type).toEqual('standalone');
    const student1 = gradeBook.resultsData.find(d => d.id === 1);
    const student3 = gradeBook.resultsData.find(d => d.id === 3);
    expect(student1!.results.find(r => r.id === 'sc1')).toEqual({ id: 'sc1', value: null, ref: null, overwritten: true});
    expect(gradeBook.getEndResult(1)).toBeCloseTo(47.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(54.17);
    const results = student3!.results.find(r => r.id === 'sc1');
    results!.value = 50;
    results!.overwritten = true;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(62.5);
});*/

test('moveScore', () => {
    gradeBook.addItemToCategory(2, 3);
    expect(gradeBook.getCategory(1)!.columnIds).toEqual([1, 2]);
    expect(gradeBook.getCategory(2)!.columnIds).toEqual([4, 5, 3]);
});

test('addCategory', () => {
    const category = gradeBook.createNewCategory();
    expect(gradeBook.categories.length).toEqual(3);
    expect(category.id).toEqual(3);
    expect(category.color).toEqual('#92eded');
    expect(category.columnIds.length).toEqual(0);
});
