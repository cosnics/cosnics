import Cluster from "../../../src/Domain/Cluster";
import Category from "../../../src/Domain/Category";
import Criterium from "../../../src/Domain/Criterium";

test('json', () => {
    let cluster = new Cluster("my_cluster");
    let category = new Category('my category');
    let criterium = new Criterium('my criterium');
    cluster.addCategory(category);
    category.color = 'red';
    cluster.addCriterium(criterium);
    expect(Cluster.fromJSON(JSON.stringify(cluster))).toEqual(cluster);
});
