import GradeBook from '@/domain/GradeBook';

const gradeBookObject = {
    gradeItems: [
        { id: 'op1', name: 'Opdracht 1', breadcrumb: 'Leerpaden » Categorie 1 » Leerpad 2 » Hoofdstuk 1' },
        { id: 'op2', name: 'Opdracht 2', breadcrumb: 'Opdrachten » Categorie 1' },
        { id: 'op3', name: 'Opdracht 3', breadcrumb: 'Opdrachten » Categorie 2' },
        { id: 'oe1', name: 'Oefening 1', breadcrumb: 'Oefeningen' },
        { id: 'oe2', name: 'Oefening 2', breadcrumb: 'Leerpaden » Leerpad 5 » Hoofdstuk 4' },
        { id: 'ev1', name: 'Evaluatie 1', breadcrumb: 'Evaluaties' },
        { id: 'ev2', name: 'Evaluatie 2', breadcrumb: 'Leerpaden » Leerpad 3 » Hoofdstuk 2' }
    ],
    gradeColumns: [
        { id: 'gr1', type: 'group', name: 'Groepsscore', subItemIds: ['op1', 'op3'], weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
        { id: 'op2', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
        { id: 'oe1', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
        { id: 'oe2', type: 'item', name: null, weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 },
        { id: 'ev1', type: 'item', name: 'Mondeling examen', weight: null, countForEndResult: true, authPresenceEndResult: 0, unauthPresenceEndResult: 2 }
    ],
    categories: [
        { id: 1, color: '#caf1eb', name: 'Categorie 1', itemIds: ['gr1', 'op2', 'oe1'] },
        { id: 2, color: '#ebf2e8', name: 'Categorie 2', itemIds: ['oe2', 'ev1'] }
    ],
    nullCategory: { id: 0, color: 'none', name: '', itemIds: [] },
    resultsData: [
        { id: 1, 'student': 'Student 1', 'results': {'op1': null, 'op3': 20, 'op2': 'gafw', 'oe1': 80, 'oe2': 50, 'ev1': 75, 'ev2': 50} },
        { id: 2, 'student': 'Student 2', 'results': {'op1': 30, 'op3': null, 'op2': 50, 'oe1': 40, 'oe2': 80, 'ev1': 65, 'ev2': 50} },
        { id: 3, 'student': 'Student 3', 'results': {'op1': null, 'op3': 50, 'op2': 30, 'oe1': 70, 'oe2': 80, 'ev1': 95, 'ev2': 50} },
        { id: 4, 'student': 'Student 4', 'results': {'op1': 80, 'op3': null, 'op2': 40, 'oe1': 40, 'oe2': 30, 'ev1': 75, 'ev2': 50} },
        { id: 5, 'student': 'Student 5', 'results': {'op1': null, 'op3': 60, 'op2': 10, 'oe1': 90, 'oe2': 40, 'ev1': 25, 'ev2': 50} }
    ]
};

let gradeBook: GradeBook;

beforeEach(() => {
    gradeBook = GradeBook.from(gradeBookObject);
});

test('gradebook', () => {
    expect(gradeBook.allCategories.length).toEqual(3);
});