import Cluster from '../../../src/Domain/Cluster';
import Category from '../../../src/Domain/Category';
import Criterium from '../../../src/Domain/Criterium';

test('json', () => {
    let cluster = new Cluster('my_cluster');
    let category = new Category('my category');
    let criterium = new Criterium('my criterium');
    cluster.addCriterium(criterium);
    category.color = 'red';
    cluster.addCategory(category);
    expect(Cluster.fromJSON(JSON.stringify(cluster))).toEqual(cluster);
});
