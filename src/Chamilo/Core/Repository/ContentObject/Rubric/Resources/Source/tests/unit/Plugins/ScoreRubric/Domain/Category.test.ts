import Category from "@/../../../../../ScoreRubric/src/Domain/Category";
import Criterium from "@/../../../../../ScoreRubric/src/Domain/Criterium";

test('json', () => {
    let category = new Category('my category');
    category.color = 'red';
    let  criterium = new Criterium('my criterium');
    category.addCriterium(criterium);
    expect(Category.fromJSON(JSON.stringify(category))).toEqual(category);
});
