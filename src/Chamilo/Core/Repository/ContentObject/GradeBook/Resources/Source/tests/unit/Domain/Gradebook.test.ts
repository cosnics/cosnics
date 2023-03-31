import GradeBook, {ColumnId, GradeScore, ResultsData} from '@/domain/GradeBook';

function createGradeBook() {
    const data = {
        gradeItems: [
            { id: 1, removed: false, title: 'Opdracht 1', breadcrumb: ['Leerpaden', 'Categorie 1', 'Leerpad 2', 'Hoofdstuk 1'] },
            { id: 2, removed: false, title: 'Opdracht 2', breadcrumb: ['Opdrachten', 'Categorie 1'] },
            { id: 3, removed: false, title: 'Opdracht 3', breadcrumb: ['Opdrachten' , 'Categorie 2'] },
            { id: 4, removed: false, title: 'Oefening 1', breadcrumb: ['Oefeningen'] },
            { id: 5, removed: false, title: 'Oefening 2', breadcrumb: ['Leerpaden', 'Leerpad 5', 'Hoofdstuk 4'] },
            { id: 6, removed: false, title: 'Evaluatie 1', breadcrumb: ['Evaluaties'] },
            { id: 7, removed: false, title: 'Evaluatie 2', breadcrumb: ['Leerpaden', 'Leerpad 3', 'Hoofdstuk 2'] }
        ],
        gradeColumns: [
            { id: 1, type: 'group', released: true, title: 'Groepsscore', subItemIds: [1, 3], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 2, type: 'item', released: true, title: null, subItemIds: [2], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 3, type: 'item', released: true, title: null, subItemIds: [4], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 4, type: 'item', released: true, title: null, subItemIds: [5], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
            { id: 5, type: 'item', released: true, title: 'Mondeling examen', subItemIds: [6], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 }
        ],
        categories: [
            { id: 1, color: '#caf1eb', title: 'Categorie 1', columnIds: [1, 2, 3] },
            { id: 2, color: '#ebf2e8', title: 'Categorie 2', columnIds: [4, 5] }
        ],
        nullCategory: { id: 0, color: 'none', title: '', columnIds: [] },
    };

    const resultsData = [
        { columnId: 1, results: [{ studentId: 1, result: 20 }, { studentId: 2, result: 30 }, { studentId: 3, result: 50 }, { studentId: 4, result: 80 }, { studentId: 5, result: 60}] },
        { columnId: 2, results: [{ studentId: 1, result: 60 }, { studentId: 2, result: 50 }, { studentId: 3, result: 30 }, { studentId: 4, result: 40 }, { studentId: 5, result: 10}] },
        { columnId: 3, results: [{ studentId: 1, result: 80 }, { studentId: 2, result: 40 }, { studentId: 3, result: 70 }, { studentId: 4, result: 40 }, { studentId: 5, result: 90}] },
        { columnId: 4, results: [{ studentId: 1, result: 50 }, { studentId: 2, result: 80 }, { studentId: 3, result: 80 }, { studentId: 4, result: 30 }, { studentId: 5, result: 40}] },
        { columnId: 5, results: [{ studentId: 1, result: 75 }, { studentId: 2, result: 65 }, { studentId: 3, result: 95 }, { studentId: 4, result: 75 }, { studentId: 5, result: 25}] },
    ];

    const gradeBook = GradeBook.from(data);
    gradeBook.resultsData = createResultsData(resultsData);
    return gradeBook;
}

let gradeBook: GradeBook;

beforeEach(() => {
    gradeBook = createGradeBook();
});

test('initial', () => {
    expect(gradeBook.gradeColumns.length).toEqual(5);
    expect(gradeBook.categories.length).toEqual(2);
    expect(gradeBook.allCategories.length).toEqual(3);
});

test('getters', () => {
    expect(gradeBook.getGradeItem(3)!.title).toEqual('Opdracht 3');
    expect(gradeBook.getGradeColumn(1)!.title).toEqual('Groepsscore');
    expect(gradeBook.getCategory(1)!.title).toEqual('Categorie 1');
});

test('statusGradedItems', () => {
    const checkedItems = gradeBook.statusGradedItems.filter(item => item.checked);
    const uncheckedItems = gradeBook.statusGradedItems.filter(item => !item.checked);
    expect(checkedItems.map(item => item.id)).toEqual([1, 2, 3, 4, 5, 6]);
    expect(uncheckedItems.map(item => item.id)).toEqual([7]);
});

test('getStatusGradedItemsByColumn', () => {
    let items = gradeBook.getStatusGradedItemsByColumn(1);
    expect(items.filter(item => !item.disabled).length).toEqual(7);
    expect(items.filter(item => item.checked).map(item => item.id)).toEqual([1, 3]);
    items = gradeBook.getStatusGradedItemsByColumn(2);
    expect(items.filter(item => item.checked).map(item => item.id)).toEqual([2]);
    expect(items.filter(item => item.disabled).map(item => item.id)).toEqual([1, 2, 3]);
});

test('hasUnreleasedScores', () => {
    expect(gradeBook.hasUnreleasedScores).toEqual(false);
    gradeBook.getGradeColumn(4)!.countForEndResult = false;
    gradeBook.getGradeColumn(4)!.released = false;
    expect(gradeBook.hasUnreleasedScores).toEqual(false);
    gradeBook.getGradeColumn(4)!.countForEndResult = true;
    expect(gradeBook.hasUnreleasedScores).toEqual(true);
});

test('getWeight', () => {
    expect(gradeBook.getWeight(gradeBook.getGradeColumn(1)!)).toEqual(20);
    gradeBook.setWeight(1, 30);
    expect(gradeBook.getWeight(gradeBook.getGradeColumn(1)!)).toEqual(30);
    expect(gradeBook.getWeight(gradeBook.getGradeColumn(2)!)).toEqual(17.5);
});

test('getTitle', () => {
    expect(gradeBook.getTitle(gradeBook.getGradeColumn(1)!)).toEqual('Groepsscore');
    gradeBook.setTitle(1, '');
    expect(gradeBook.getTitle(gradeBook.getGradeColumn(1)!)).toEqual('Opdracht 1');
    gradeBook.setTitle(1, 'Kolom 1');
    expect(gradeBook.getTitle(gradeBook.getGradeColumn(1)!)).toEqual('Kolom 1');
});

test('hasRemovedSourceData', () => {
    expect(gradeBook.hasRemovedSourceData(gradeBook.getGradeColumn(1)!)).toEqual(false);
    gradeBook.gradeItems[0] = {...gradeBook.gradeItems[0], removed: true};
    expect(gradeBook.hasRemovedSourceData(gradeBook.getGradeColumn(1)!)).toEqual(true);
});

test('getColumnSubItems', () => {
    expect(gradeBook.getColumnSubItems(gradeBook.getGradeColumn(1)!).map(item => item.id)).toEqual([1, 3]);
});

test('hasResult', () => {
    expect(gradeBook.hasResult(5, 1)).toEqual(true);
    expect(gradeBook.hasResult(5, 100)).toEqual(false);
    expect(gradeBook.hasResult(100, 1)).toEqual(false);
});

test('getResult', () => {
    expect(gradeBook.getResult(100, 1)).toEqual(null);
    expect(gradeBook.getResult(1, 100)).toEqual(null);
    expect(gradeBook.getResult(1, 1)).toEqual(20);
    const studentScore = gradeBook.resultsData[1][1]!;
    studentScore.sourceScoreAuthAbsent = true;
    expect(gradeBook.getResult(1, 1)).toEqual('aabs');
    expect(gradeBook.isOverwrittenResult(1, 1)).toEqual(false);
    gradeBook.overwriteResult(1, 1, 40);
    expect(gradeBook.isOverwrittenResult(1, 1)).toEqual(true);
    expect(gradeBook.getResult(1, 1)).toEqual(40);
});

test('revertOverwrittenResult', () => {
    gradeBook.overwriteResult(1, 1, 'aabs');
    expect(gradeBook.getResult(1, 1)).toEqual('aabs');
    gradeBook.revertOverwrittenResult(1, 1);
    expect(gradeBook.isOverwrittenResult(1, 1)).toEqual(false);
    expect(gradeBook.getResult(1, 1)).toEqual(20);
});

test('userTotalNeedsUpdating', () => {
    gradeBook.users = [1,2,3,4,5].map(id => ({id, firstName: 'F' + id, lastName: 'L' + id}));
    const user = gradeBook.users[0];
    expect(gradeBook.userTotalNeedsUpdating(user)).toEqual(false); // unsynchronized
    const totalScore = createSimpleScore(100, 1, 1, null);
    totalScore.overwritten = true;
    totalScore.newScore = 50;
    totalScore.isTotal = true;
    gradeBook.resultsData['totals'] = {};
    gradeBook.resultsData['totals'][1] = totalScore;
    expect(gradeBook.userTotalNeedsUpdating(user)).toEqual(true);
    expect(gradeBook.totalsNeedUpdating).toEqual(true);
    totalScore.newScore = 57;
    expect(gradeBook.userTotalNeedsUpdating(user)).toEqual(false);
    expect(gradeBook.totalsNeedUpdating).toEqual(false);
});

test('endResult', () => {
    expect(gradeBook.getEndResult(1)).toBeCloseTo(57);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(65);
});

test('endResultWithUpdatedWeight', () => {
    gradeBook.setWeight(1, 60);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(38.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(57.5);
});

test('endResultDontCountColumn', () => {
    gradeBook.getGradeColumn(4)!.countForEndResult = false;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(58.75);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(61.25);

    gradeBook.setWeight(1, 70);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(35.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(54.5);
});

test('endResultHandleAbsence', () => {
    let studentScore = gradeBook.resultsData[2][1];

    // test authorized absence
    studentScore.newScoreAuthAbsent = true;
    studentScore.overwritten = true;

    // don't count score
    expect(gradeBook.getEndResult(1)).toBeCloseTo(56.25);

    // max score
    gradeBook.getGradeColumn(2)!.authPresenceEndResult = GradeBook.MAX_SCORE;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(65);

    // min score
    gradeBook.getGradeColumn(2)!.authPresenceEndResult = GradeBook.MIN_SCORE;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(45);

    // test unauthorized absence
    studentScore = gradeBook.resultsData[3][3];
    studentScore.newScoreAuthAbsent = false;
    studentScore.overwritten = true;

    // min score
    expect(gradeBook.getEndResult(3)).toBeCloseTo(51);

    // max score
    gradeBook.getGradeColumn(3)!.unauthPresenceEndResult = GradeBook.MAX_SCORE;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(71);

    // don't count score
    gradeBook.getGradeColumn(3)!.unauthPresenceEndResult = GradeBook.NO_SCORE;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(63.75);
});

test('getResultComment', () => {
    expect(gradeBook.getResultComment(1, 1)).toEqual(null);
    gradeBook.updateResultComment(1, 1, 'My comment');
    expect(gradeBook.getResultComment(1, 1)).toEqual('My comment');
});

test('addScore', () => {
    gradeBook.addGradeColumnFromItem(gradeBook.getGradeItem(7)!);
    gradeBook.resultsData['col1'] = {1: createSimpleScore(100, 'col1', 1, 50)};
    expect(gradeBook.gradeColumns.length).toEqual(6);
    expect(gradeBook.gradeColumns[5].type).toEqual('item');
    expect(gradeBook.getEndResult(1)).toBeCloseTo(55.83);
});

test('removeScore', () => {
    gradeBook.removeColumn(gradeBook.getGradeColumn(3)!);
    expect(gradeBook.gradeColumns.length).toEqual(4);
    expect(gradeBook.getEndResult(1)).toBeCloseTo(51.25);
});

test('addGroupScore', () => {
    gradeBook.setWeight(4, 50);
    const column = gradeBook.getGradeColumn(3);
    column!.countForEndResult = false;
    column!.authPresenceEndResult = GradeBook.MAX_SCORE;
    column!.unauthPresenceEndResult = GradeBook.MAX_SCORE;
    gradeBook.addSubItem(gradeBook.getGradeItem(5)!, 3);
    expect(gradeBook.gradeColumns.length).toEqual(4);
    expect(column!.id).toEqual(3);
    expect(column!.type).toEqual('group');
    expect(column!.title).toEqual('Oefening 1');
    expect(column!.subItemIds).toEqual([4, 5]);
    expect(column!.weight).toEqual(null); // should change so it stays 50?
    expect(column!.countForEndResult).toEqual(false);
    expect(column!.authPresenceEndResult).toEqual(GradeBook.MAX_SCORE);
    expect(column!.unauthPresenceEndResult).toEqual(GradeBook.MAX_SCORE);
});

test('removeFromGroupScore', () => {
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(2);
    let studentScore = gradeBook.resultsData[1][1];// student1!.results.find(r => r.id === 1);
    expect(studentScore.sourceScore).toEqual(20);
    gradeBook.removeSubItem(gradeBook.getGradeItem(3)!);
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(1);
    studentScore.sourceScore = null;
    expect(gradeBook.getEndResult(1)).toBeCloseTo(53);
    studentScore = gradeBook.resultsData[1][2];
    expect(studentScore.sourceScore).toEqual(30);
    gradeBook.removeSubItem(gradeBook.getGradeItem(1)!);
    expect(gradeBook.getGradeColumn(1)!.subItemIds?.length).toEqual(0);
});

test('addNewScore', () => {
    gradeBook.createNewScore();
    expect(gradeBook.nullCategory.columnIds).toEqual(['sc1']);
    expect(gradeBook.gradeColumns.length).toEqual(6);
    const column = gradeBook.gradeColumns[5];
    expect(column.id).toEqual('sc1');
    expect(column.title).toEqual('Score');
    expect(column.type).toEqual('standalone');
    gradeBook.resultsData['sc1'] = {
        1: createSimpleScore(100, 'col1', 1, null),
        3: createSimpleScore(101, 'col1', 1, null)
    };
    expect(gradeBook.getEndResult(1)).toBeCloseTo(47.5);
    expect(gradeBook.getEndResult(3)).toBeCloseTo(54.17);
    const studentScore = gradeBook.resultsData['sc1'][3];
    studentScore.newScore = 50;
    studentScore.overwritten = true;
    expect(gradeBook.getEndResult(3)).toBeCloseTo(62.5);
});

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

test('removeCategory', () => {
    expect(gradeBook.allCategories.length).toEqual(3);
    let category = gradeBook.getCategory(1)!;
    gradeBook.removeCategory(category);
    expect(gradeBook.allCategories.length).toEqual(2);
    expect(gradeBook.nullCategory.columnIds).toEqual([1, 2, 3]);
    category = gradeBook.getCategory(2)!;
    gradeBook.removeCategory(category);
    expect(gradeBook.allCategories.length).toEqual(1);
    expect(gradeBook.nullCategory.columnIds).toEqual([1, 2, 3, 4, 5]);
});

test('updateGradeColumnId', () => {
    gradeBook.updateGradeColumnId(gradeBook.gradeColumns[0], 45);
    expect(gradeBook.gradeColumns[0]).toEqual(gradeBook.getGradeColumn(45)!);
    expect(gradeBook.categories[0].columnIds).toEqual([45, 2, 3]);
});

test('findGradeColumnWithGradeItem', () => {
    expect(gradeBook.findGradeColumnWithGradeItem(7)).toEqual(null);
    expect(gradeBook.findGradeColumnWithGradeItem(3)).toEqual(gradeBook.getGradeColumn(1)!);
});

test('createNewColumnId', () => {
    expect(gradeBook.createNewColumnId()).toEqual('col1');
    gradeBook.gradeColumns[3].id = 'col1';
    gradeBook.gradeColumns[2].id = 'col2';
    expect(gradeBook.createNewColumnId()).toEqual('col3');
});

function createSimpleScore(scoreId: number, columnId: ColumnId, studentId: number, score: number|null): GradeScore {
    return {
        id: scoreId,
        columnId: columnId,
        targetUserId: studentId,
        comment: null,
        isTotal: false,
        newScore: null,
        newScoreAuthAbsent: false,
        overwritten: false,
        sourceScore: score,
        sourceScoreAuthAbsent: false
    };
}

function createResultsData(data: any) {
    const resultsData: ResultsData = {};
    let scoreId = 1;
    data.forEach((column: any) => {
        const students: Record<number, GradeScore> = {};
        column.results.forEach((result: any) => {
            students[result.studentId] = createSimpleScore(scoreId, column.columnId, result.studentId, result.result);
            scoreId++;
        });
        resultsData[column.columnId] = students;
    });
    return resultsData;
}