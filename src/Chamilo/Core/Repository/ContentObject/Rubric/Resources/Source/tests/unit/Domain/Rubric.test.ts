import Rubric from '../../../src/Domain/Rubric';
import Cluster from '../../../src/Domain/Cluster';
import Level from '../../../src/Domain/Level';
import Category from '../../../src/Domain/Category';
import Criterium from '../../../src/Domain/Criterium';
import Choice from '../../../src/Domain/Choice';

let rubric: Rubric, cluster: Cluster, level1: Level, level2: Level, category: Category, clusterCriterium: Criterium,
    categoryCriterium: Criterium;

beforeEach(() => {
    rubric = new Rubric('my_rubric');
    cluster = new Cluster('my cluster');
    level1 = new Level('level1', 'bad');
    level2 = new Level('level2', 'good');
    rubric.addLevel(level1);
    rubric.addLevel(level2);
    category = new Category('category1');
    clusterCriterium = new Criterium('clusterCriterium');
    categoryCriterium = new Criterium('categoryCriterium');
    cluster.addCriterium(clusterCriterium);
    cluster.addCategory(category);
    rubric.addCluster(cluster);

    let choice1 = rubric.getChoice(clusterCriterium, level1);
    choice1.selected = false;
    choice1.feedback = 'a';

    let choice2 = rubric.getChoice(clusterCriterium, level2);
    choice2.selected = false;
    choice2.feedback = 'b';

    category.addCriterium(categoryCriterium);

    let choice3 = rubric.getChoice(categoryCriterium, level1);
    choice3.selected = false;
    choice3.feedback = 'c';

    let choice4 = rubric.getChoice(categoryCriterium, level2);
    choice4.selected = true;
    choice4.feedback = 'd';

});

test('score', () => {
    clusterCriterium.weight = 50;
    expect(rubric.getChoiceScore(clusterCriterium, level1)).toEqual(5);
    let choice = rubric.getChoice(categoryCriterium, level1);
    choice.hasFixedScore = true;
    choice.fixedScore = 7;
    expect(rubric.getChoiceScore(categoryCriterium, level1)).toEqual(7);
});

test('choices', () => {
    expect(() => rubric.getChoice(new Criterium('unknown'), level1)).toThrowError('No choice found for criteria: ');
    expect(() => rubric.getChoice(clusterCriterium, new Level('unknown', ''))).toThrowError('No choice found for criteria: ');

    expect(rubric.getChoice(clusterCriterium, level1)).toEqual(new Choice(false, 'a'));
    expect(rubric.getChoice(clusterCriterium, level2)).toEqual(new Choice(false, 'b'));
    expect(rubric.getChoice(categoryCriterium, level1)).toEqual(new Choice(false, 'c'));
    expect(rubric.getChoice(categoryCriterium, level2)).toEqual(new Choice(true, 'd'));
});

test('relations', () => {
    expect(rubric.children).toEqual([cluster]);
    expect(cluster.parent).toEqual(rubric);
    expect(cluster.children).toEqual([category, clusterCriterium]);
    expect(category.parent).toEqual(cluster);
    expect(clusterCriterium.parent).toEqual(cluster);
    expect(category.children).toEqual([categoryCriterium]);
    expect(categoryCriterium.parent).toEqual(category);
});

test('deletes', () => {
    category.removeCriterium(categoryCriterium);
    cluster.removeCriterium(clusterCriterium);
    cluster.removeCategory(category);
    rubric.removeCluster(cluster);
    expect(rubric.children).toEqual([]);
    expect(cluster.parent).toEqual(null);
    expect(cluster.children).toEqual([]);
    expect(category.parent).toEqual(null);
    expect(clusterCriterium.parent).toEqual(null);
    expect(category.children).toEqual([]);
    expect(categoryCriterium.parent).toEqual(null);
});

test('json', () => {
    expect(Rubric.fromJSON(JSON.stringify(rubric))).toEqual(rubric);
});
